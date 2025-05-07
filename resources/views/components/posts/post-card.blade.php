@props(['post'])
<div class="">
    <a href="{{ route('posts.show', $post) }}">
        <div>
            <img class="w-full rounded-xl" src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
        </div>
    </a>
    <div class="mt-3">
        <div class="flex items-center mb-2">
            @foreach($post->categories as $category)
                <a href="#" class="rounded-xl px-3 py-1 text-sm mr-3 transition duration-150"
                    style="background-color: {{ $category->bg_color ?? '#EF4444' }}; color: {{ $category->text_color ?? '#FFFFFF' }}">
                    {{ $category->title }}
                </a>
            @endforeach
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $post->published_at->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('posts.show', $post) }}" class="text-xl font-bold text-gray-900 dark:text-white">{{ $post->title }}</a>
    </div>

</div>
