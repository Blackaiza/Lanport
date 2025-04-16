<div class="flex items-center {{ $isWinner ? 'bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded' : '' }}">
    {{ $name }}
    @if($isWinner)
        <span class="ml-2">👑</span>
    @endif
</div>
