<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('welcome',[
            // 'featuredPosts' => Post::where('published_at','<=',Carbon::now())->take(3)->get(),
            'featuredPosts' => Post::published()->featured()->latest('published_at')->take(3)->get(),
            'latestPosts'=> Post::published()->latest('published_at')->take(9)->get()
        ]);
    }
}
