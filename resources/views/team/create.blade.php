<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('team.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Team
                            </div>
                        </a>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Create New Team</h2>

                    <form action="{{ route('team.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Team Picture -->
                        <div class="space-y-2">
                            <label class="text-lg font-medium text-gray-900 dark:text-white">Team Picture</label>
                            <div class="mt-1 flex items-center space-x-6">
                                @if(isset($team) && $team->team_picture)
                                    <div class="w-32 h-32 rounded-lg overflow-hidden">
                                        <img src="{{ asset('storage/' . $team->team_picture) }}"
                                             class="w-full h-full object-cover"
                                             alt="Current team picture">
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <label class="flex flex-col items-center px-4 py-6 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 rounded-lg border-2 border-dashed border-blue-600 dark:border-blue-400 cursor-pointer hover:border-blue-700 dark:hover:border-blue-500">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="mt-2 text-sm">{{ isset($team) ? 'Change team picture' : 'Select a team picture' }}</span>
                                        <span class="mt-1 text-xs text-gray-500">Max 2MB (JPG, PNG, GIF)</span>
                                        <input type="file" name="team_picture" class="hidden" accept="image/*">
                                    </label>
                                </div>
                            </div>
                            @error('team_picture')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Team Name -->
                        <div class="space-y-2">
                            <label class="text-lg font-medium text-gray-900 dark:text-white">Team Name</label>
                            <input type="text" name="name" required
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                   placeholder="Enter team name">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Game Selection -->
                        <div class="space-y-2">
                            <label class="text-lg font-medium text-gray-900 dark:text-white">Game</label>
                            <select name="game" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">Select a game</option>
                                @foreach(\App\Models\Game::all() as $game)
                                    <option value="{{ $game->game_id }}">{{ $game->name }}</option>
                                @endforeach
                            </select>
                            @error('game')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <label class="text-lg font-medium text-gray-900 dark:text-white">Team Status</label>
                            <select name="status" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-lg font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Create Team
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
