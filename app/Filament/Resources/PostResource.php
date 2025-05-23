<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use App\Models\User;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Media Management';
    protected static ?string $navigationLabel = 'News';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Main Content')->schema(
                    [
                        TextInput::make('title')
                        ->live()
                        ->required()->minLength(1)->maxLength(150)
                        ->afterStateUpdated(function(string $operation, $state, Forms\Set $set)
                        {
                            if($operation==='edit'){
                                return;
                            }
                            $set('slug',Str::slug($state));
                            // dd($operation);
                            // dd('called');
                        }),
                        TextInput::make('slug')->required()->minLength(1)->unique(ignoreRecord:true)->maxLength(150),
                        RichEditor::make('body')->required()->fileAttachmentsDirectory('posts/images')->columnSpanFull(),
                    ]
                )->columns(2),
                Section::make('Meta')->schema(
                    [
                        FileUpload::make('image')->image()->directory('posts/thumbnails'),
                        DateTimePicker::make('published_at')->nullable(),
                        Checkbox::make('featured'),
                        // Hidden::make('user_id')
                        // ->default(Auth::id()),
                        // Untuk Author
                        // Select::make('author')
                        // ->relationship('author','name')
                        // ->searchable()
                        // ->disabled(fn (string $operation) =>  $operation === 'create')
                        // ->required(),
                        // Author 2
                    Select::make('author')
                        ->relationship('author', 'name')
                        ->searchable()
                        //->default(Auth::id())
                        ->visible(fn (string $operation) => $operation !== 'create') // Hide during creation
                        ->required(),

                   Hidden::make('user_id')
                       ->default(Auth::id())
                       ->visible(fn (string $operation) => $operation === 'create'),
                        //untuk tag
                        Select::make('categories')
                        ->multiple()
                        ->relationship('categories','title')
                        ->searchable(),
                    ]
                ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('slug')->sortable()->searchable(),
                TextColumn::make('author.name')->sortable()->searchable(),
                TextColumn::make('published_at')->date('Y-m-d')->sortable()->searchable(),
                CheckboxColumn::make('featured'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
