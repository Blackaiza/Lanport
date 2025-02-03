<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $casts=[
        'published_at'=> 'datetime',
    ];


    public function author()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('published_at','<=',Carbon::now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured',true);
    }

    public function getExcerpt()
    {
        return Str::limit(strip_tags($this->body,150));
    }

    public function getReadingTime()
    {
        $mins = round(str_word_count($this->body) / 238);

        return ($mins<1) ?'1':$mins;
    }
}
