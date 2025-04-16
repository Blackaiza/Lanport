<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\CompetitionController;

Route::get('/', WelcomeController::class)->name('home');
Route::get('/blog', [PostController::class,'index'])->name('posts.index');

//Test
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
   'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Route::get('/team', function(){
    //     return view('team');
    // })->name('team.index');

    Route::resource('team',TeamController::class);
    Route::resource('competition',CompetitionController::class);

    // Team routes
    Route::get('/team/{team}', [TeamController::class, 'show'])->name('team.show');
    Route::get('/team/{team}/edit', [TeamController::class, 'edit'])->name('team.edit');
    Route::put('/team/{team}', [TeamController::class, 'update'])->name('team.update');
    Route::post('/team', [TeamController::class, 'store'])->name('team.store');

    // Team member management routes
    Route::post('/team/{team}/invite', [TeamController::class, 'invite'])->name('team.invite');
    Route::delete('/team/{team}/member/{member}', [TeamController::class, 'removeMember'])->name('team.remove-member');
    Route::delete('/team/{team}/invitation/{invitation}', [TeamController::class, 'cancelInvitation'])->name('team.cancel-invitation');

    // Team invitation routes
    Route::get('/team/accept-invitation/{token}', [TeamController::class, 'acceptInvitation'])
        ->name('team.accept-invitation');
    Route::post('/team/invitation/{invitation}/resend', [TeamController::class, 'resendInvitation'])->name('team.resend-invitation');

    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
    Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create');
    Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
    Route::get('/tournaments/{id}', [TournamentController::class, 'show'])->name('tournaments.show');
    Route::get('/tournaments/{id}/generate', [TournamentController::class, 'generateBracket'])->name('tournaments.generateBracket');
    Route::post('/matches/{id}/winner', [TournamentController::class, 'updateMatchWinner'])->name('matches.updateWinner');

    Route::get('/competitions', [CompetitionController::class, 'index'])->name('competition.index');
    Route::get('/competitions/{competition}', [CompetitionController::class, 'show'])->name('competition.show');
    Route::post('/competitions/{competition}/join', [CompetitionController::class, 'join'])->name('competition.join');
});

// Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
// Route::get('/tournaments/create', [TournamentController::class, 'create'])->name('tournaments.create');
// Route::post('/tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
// Route::get('/tournaments/{id}', [TournamentController::class, 'show'])->name('tournaments.show');
// Route::get('/tournaments/{id}/generate', [TournamentController::class, 'generateBracket'])->name('tournaments.generateBracket');
// Route::post('/matches/{id}/winner', [TournamentController::class, 'updateMatchWinner'])->name('matches.updateWinner');
