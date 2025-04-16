<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\User;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Mail;
use App\Models\TeamInvitation;
use Illuminate\Support\Str;
use App\Mail\TeamInvitationMail;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;
class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::all();
        return view('team.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('team.create',compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'game' => 'required|string',
            'status' => 'required|string|in:pending,active',
            'team_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $team = new Team();
        $team->name = $validated['name'];
        $team->game = $validated['game'];
        $team->status = $validated['status'];
        $team->leader_id = auth()->id();

        if ($request->hasFile('team_picture')) {
            $path = $request->file('team_picture')->store('team-pictures', 'public');
            $team->team_picture = $path;
        }

        $team->save();

        // Automatically add the creator as a team member
        $team->members()->attach(auth()->id());

        return redirect()->route('team.show', $team)->with('success', 'Team created successfully!');
    }

    public function addMember(Request $request, Team $team)
    {
        // Check if the authenticated user is the leader of the team
        $leader = $team->members()->wherePivot('role', 'leader')->where('users.id', Auth::id())->exists();

        if (!$leader) {
            return redirect()->back()->with('error', 'Only the team leader can add members.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Add member to the team
        $team->members()->attach($request->user_id, ['role' => 'member']);

        return redirect()->back()->with('success', 'Member added successfully!');
    }

    public function removeMember(Team $team, User $member)
    {
        // Check if trying to remove the team leader
        if ($member->id === $team->leader_id) {
            return back()->with('error', 'The team leader cannot be removed from the team.');
        }

        // Check if user has permission to remove members
        if (auth()->id() !== $team->leader_id && auth()->id() !== $member->id) {
            return back()->with('error', 'You do not have permission to remove this member.');
        }

        $team->members()->detach($member->id);
        return back()->with('success', 'Member removed successfully.');
    }

    public function promoteMember(Team $team, User $member)
    {
        // Check if user is the team leader
        if (auth()->id() !== $team->leader_id) {
            return back()->with('error', 'Only the team leader can promote members.');
        }

        // Check if member is already a co-leader
        if ($team->members()->where('user_id', $member->id)->wherePivot('role', 'co_leader')->exists()) {
            return back()->with('error', 'This member is already a co-leader.');
        }

        $team->members()->updateExistingPivot($member->id, ['role' => 'co_leader']);
        return back()->with('success', 'Member promoted to co-leader successfully.');
    }

    public function demoteMember(Team $team, User $member)
    {
        // Check if user is the team leader
        if (auth()->id() !== $team->leader_id) {
            return back()->with('error', 'Only the team leader can demote members.');
        }

        // Check if member is a co-leader
        if (!$team->members()->where('user_id', $member->id)->wherePivot('role', 'co_leader')->exists()) {
            return back()->with('error', 'This member is not a co-leader.');
        }

        $team->members()->updateExistingPivot($member->id, ['role' => 'member']);
        return back()->with('success', 'Co-leader demoted to member successfully.');
    }

    public function transferOwnership(Team $team, User $member)
    {
        // Check if user is the team leader
        if (auth()->id() !== $team->leader_id) {
            return back()->with('error', 'Only the team leader can transfer ownership.');
        }

        // Check if member is a co-leader
        if (!$team->members()->where('user_id', $member->id)->wherePivot('role', 'co_leader')->exists()) {
            return back()->with('error', 'You can only transfer ownership to a co-leader.');
        }

        try {
            \DB::transaction(function () use ($team, $member) {
                // Update team leader
                $team->update(['leader_id' => $member->id]);

                // Update roles
                $team->members()->updateExistingPivot($member->id, ['role' => 'leader']);
                $team->members()->updateExistingPivot(auth()->id(), ['role' => 'co_leader']);
            });

            return back()->with('success', 'Team ownership transferred successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to transfer team ownership.');
        }
    }

    public function leaveTeam(Team $team)
    {
        // Check if user is the team leader
        if (auth()->id() === $team->leader_id) {
            return back()->with('error', 'The team leader cannot leave the team. Please transfer ownership first.');
        }

        $team->members()->detach(auth()->id());
        return redirect()->route('dashboard')->with('success', 'You have left the team successfully.');
    }

    public function destroy(Team $team)
    {
        // Ensure only the team leader or an admin can delete the team
        if (Auth::id() !== $team->leader_id) {
            return redirect()->back()->with('error', 'Only the team leader can delete the team.');
        }

        // Detach all members first to avoid foreign key constraint issues
        $team->members()->detach();

        // Delete the team
        $team->delete();

        return redirect()->route('team.index')->with('success', 'Team deleted successfully!');
    }

    public function edit(Team $team)
    {
        // Load the team's members with their roles
        $team->load(['members' => function($query) {
            $query->withPivot('role');
        }]);

        $availableUsers = User::whereNotIn('id', $team->members->pluck('id'))
            ->whereNotIn('email', $team->invitations->pluck('email'))
            ->get();

        return view('team.edit', compact('team', 'availableUsers'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'game' => 'required|string',
            'status' => 'required|in:pending,active',
            'team_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $data = $request->except('team_picture');

            if ($request->hasFile('team_picture')) {
                // Delete old picture if exists
                if ($team->team_picture) {
                    Storage::disk('public')->delete($team->team_picture);
                }

                $path = $request->file('team_picture')->store('team-pictures', 'public');
                $data['team_picture'] = $path;
            }

            $team->update($data);

            return back()->with('success', 'Team updated successfully' .
                ($request->hasFile('team_picture') ? ' with new team picture' : ''));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update team. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function inviteMembers(Request $request, Team $team)
    {
        $request->validate([
            'members' => 'required|array',
        ]);

        foreach ($request->members as $userId) {
            $user = User::find($userId);

            if (!$user) continue;

            // Generate token and create an invitation
            $token = Str::random(40);
            TeamInvitation::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'token' => $token,
            ]);

            // Send email
            Mail::to($user->email)->send(new TeamInvitationMail($team, $user, $token));
        }

        return redirect()->back()->with('success', 'Invitations sent!');
    }

    public function acceptInvitation($token)
    {
        // Find the invitation
        $invitation = TeamInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        // Get the authenticated user
        $user = auth()->user();

        // Check if user's email matches the invitation email
        if ($user->email !== $invitation->email) {
            return redirect()->route('dashboard')
                ->with('error', 'This invitation was sent to a different email address.');
        }

        // Check if user is already a member of the team
        if ($invitation->team->members()->where('user_id', $user->id)->exists()) {
            $invitation->update(['accepted_at' => now()]);
            return redirect()->route('dashboard')
                ->with('error', 'You are already a member of this team.');
        }

        try {
            \DB::transaction(function () use ($invitation, $user) {
                // Add user to team
                $invitation->team->members()->attach($user->id);

                // Mark invitation as accepted
                $invitation->update([
                    'accepted_at' => now()
                ]);
            });

            return redirect()->route('team.show', $invitation->team)
                ->with('success', 'You have successfully joined the team!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'There was an error joining the team. Please try again.');
        }
    }

    public function invite(Request $request, Team $team)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Check if user is already a member
        if ($team->members()->where('email', $request->email)->exists()) {
            return back()->with('error', 'This user is already a member of the team.');
        }

        // Check if there's already a pending invitation
        if ($team->invitations()->where('email', $request->email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->exists()) {
            return back()->with('error', 'An invitation has already been sent to this email.');
        }

        // Create invitation
        $invitation = $team->invitations()->create([
            'email' => $request->email,
            'token' => Str::random(32),
            'expires_at' => now()->addDays(7),
        ]);

        // Send invitation email
        Mail::to($request->email)->send(new TeamInvitationMail($team, $invitation->token));

        return back()->with('success', 'Invitation sent successfully!');
    }

    public function cancelInvitation(Team $team, TeamInvitation $invitation)
    {
        $invitation->delete();
        return back()->with('success', 'Invitation cancelled successfully.');
    }

    public function resendInvitation(TeamInvitation $invitation)
    {
        try {
            $invitation->update([
                'token' => Str::random(32),
                'expires_at' => now()->addDays(7)
            ]);

            Mail::to($invitation->email)->send(new TeamInvitation($invitation->team, $invitation));

            return back()->with('success', 'Invitation resent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to resend invitation.');
        }
    }

    /**
     * Display the specified team.
     */
    public function show(Team $team)
    {
        // Load the team's members and invitations
        $team->load(['members', 'invitations']);

        return view('team.show', [
            'team' => $team,
            'isLeader' => auth()->id() === $team->leader_id,
            'isMember' => $team->members->contains(auth()->id())
        ]);
    }
}
