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

                    <!-- Notifications -->
                    @if(session('success'))
                        <div id="notification" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex justify-between items-center">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ session('success') }}
                            </div>
                            <button onclick="closeNotification()" class="text-green-700 hover:text-green-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div id="notification" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex justify-between items-center">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ session('error') }}
                            </div>
                            <button onclick="closeNotification()" class="text-red-700 hover:text-red-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Team: {{ $team->name }}</h2>

                    @if(auth()->id() === $team->leader_id || $team->members()->where('user_id', auth()->id())->wherePivot('role', 'co_leader')->exists())
                    <form action="{{ route('team.update', $team->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Team Picture -->
                        <div class="space-y-2">
                            <label class="text-lg font-medium text-gray-900 dark:text-white">Team Picture</label>
                            <div class="mt-1 flex items-center space-x-6">
                                @if($team->team_picture)
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
                                        <span class="mt-2 text-sm">Change team picture</span>
                                        <input type="file" name="team_picture" class="hidden">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Team Name -->
                        <div class="space-y-2">
                            <label class="text-lg font-medium text-gray-900 dark:text-white">Team Name</label>
                            <input type="text" name="name" value="{{ old('name', $team->name) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent dark:bg-gray-700 dark:text-white">
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
                                    <option value="{{ $game->game_id }}" {{ $team->game == $game->game_id ? 'selected' : '' }}>
                                        {{ $game->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('game'))
                                <p class="text-red-500 text-sm mt-1">{{ $errors->first('game') }}</p>
                            @endif
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <label class="text-lg font-medium text-gray-900 dark:text-white">Team Status</label>
                            <select name="status" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-600 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="pending" {{ $team->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ $team->status == 'active' ? 'selected' : '' }}>Active</option>
                            </select>
                            @if($errors->has('status'))
                                <p class="text-red-500 text-sm mt-1">{{ $errors->first('status') }}</p>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end pt-6">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-lg font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Team
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="bg-gray-100 dark:bg-gray-700 p-6 rounded-lg">
                        <p class="text-gray-600 dark:text-gray-300">You don't have permission to update team information. Only team leaders and co-leaders can modify team details.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Member Invitations Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Team Members & Invitations</h3>

                    <!-- Current Members List -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Members</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach($team->members as $member)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-300">{{ $member->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($member->id === $team->leader_id)
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                        Team Leader
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                                        {{ $member->pivot->role === 'co_leader' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                                        {{ $member->pivot->role === 'co_leader' ? 'Co-Leader' : 'Member' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($member->id === $team->leader_id)
                                                    <span class="text-sm text-gray-500 dark:text-gray-400 italic">
                                                        Team Creator
                                                    </span>
                                                @else
                                                    @if(auth()->id() === $team->leader_id)
                                                        <div class="flex space-x-2">
                                                            @if($member->pivot->role === 'member')
                                                                <form action="{{ route('team.promote-member', [$team->id, $member->id]) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                                        Promote to Co-Leader
                                                                    </button>
                                                                </form>
                                                            @elseif($member->pivot->role === 'co_leader')
                                                                <form action="{{ route('team.demote-member', [$team->id, $member->id]) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                                        Demote to Member
                                                                    </button>
                                                                </form>
                                                                <form action="{{ route('team.transfer-ownership', [$team->id, $member->id]) }}" method="POST" class="inline">
                                                                    @csrf
                                                                    <button type="submit" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300"
                                                                            onclick="return confirm('Are you sure you want to transfer team ownership to this co-leader?')">
                                                                        Transfer Ownership
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <form action="{{ route('team.remove-member', [$team->id, $member->id]) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                                        onclick="return confirm('Are you sure you want to remove this member?')">
                                                                    Remove
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @elseif(auth()->id() === $member->id)
                                                        <form action="{{ route('team.leave', $team->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                                    onclick="return confirm('Are you sure you want to leave this team?')">
                                                                Leave Team
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pending Invitations -->
                    @if(auth()->id() === $team->leader_id || $team->members()->where('user_id', auth()->id())->wherePivot('role', 'co_leader')->exists())
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pending Invitations</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sent</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expires</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @php
                                        $memberEmails = $team->members->pluck('email')->toArray();
                                        $filteredInvitations = $team->invitations->filter(function($invitation) use ($memberEmails) {
                                            return !in_array($invitation->email, $memberEmails);
                                        });
                                    @endphp
                                    @forelse($filteredInvitations as $invitation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ $invitation->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-300">{{ $invitation->created_at->diffForHumans() }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm {{ $invitation->isExpired() ? 'text-red-600' : 'text-gray-500 dark:text-gray-300' }}">
                                                    {{ $invitation->expires_at->diffForHumans() }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                                <button onclick="resendInvitation('{{ $invitation->id }}')"
                                                        class="text-blue-600 hover:text-blue-900">
                                                    Resend
                                                </button>
                                                <form action="{{ route('team.cancel-invitation', [$team->id, $invitation->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                                No pending invitations
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Available Users to Invite -->
                    @if(auth()->id() === $team->leader_id || $team->members()->where('user_id', auth()->id())->wherePivot('role', 'co_leader')->exists())
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Invite Users</h4>
                        <div class="mb-4">
                            <form action="{{ route('team.invite', $team->id) }}" method="POST" class="flex gap-4">
                                @csrf
                                <input type="email" name="email"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="Enter email address">
                                <button type="submit" id="inviteButton"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 relative">
                                    <span class="invite-text">Send Invitation</span>
                                    <span class="invite-loading hidden">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </form>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach($availableUsers as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500 dark:text-gray-300">{{ $user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="{{ route('team.invite', $team->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="email" value="{{ $user->email }}">
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                        Invite
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // Close notification
    function closeNotification() {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.style.display = 'none';
        }
    }

    // Auto-close notifications after 5 seconds
    setTimeout(closeNotification, 5000);

    // File upload preview
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.querySelector('input[name="team_picture"]');
        const previewContainer = document.querySelector('.w-32.h-32');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    alert('Please upload an image file');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewContainer) {
                        // Update existing preview
                        const img = previewContainer.querySelector('img');
                        if (img) {
                            img.src = e.target.result;
                        } else {
                            // Create new preview if doesn't exist
                            const newImg = document.createElement('img');
                            newImg.src = e.target.result;
                            newImg.classList.add('w-full', 'h-full', 'object-cover');
                            previewContainer.appendChild(newImg);
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });

    function openResendModal(invitationId) {
        const modal = document.getElementById('resendModal');
        const form = document.getElementById('resendForm');
        form.action = `/team/invitation/${invitationId}/resend`;
        modal.classList.remove('hidden');
    }

    function closeResendModal() {
        const modal = document.getElementById('resendModal');
        modal.classList.add('hidden');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('resendModal');
        if (event.target == modal) {
            modal.classList.add('hidden');
        }
    }

    // Invite button loading animation
    document.addEventListener('DOMContentLoaded', function() {
        const inviteForm = document.querySelector('form[action*="team.invite"]');
        const inviteButton = document.getElementById('inviteButton');
        const inviteText = inviteButton.querySelector('.invite-text');
        const inviteLoading = inviteButton.querySelector('.invite-loading');

        inviteForm.addEventListener('submit', function() {
            inviteText.classList.add('hidden');
            inviteLoading.classList.remove('hidden');
            inviteButton.disabled = true;
        });
    });
</script>
