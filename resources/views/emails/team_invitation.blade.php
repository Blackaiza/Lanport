@component('mail::message')
# Team Invitation

You have been invited to join the team **{{ $team->name }}**!

@if (!$user)
If you don't have an account yet, you can create one by clicking the button below:

@component('mail::button', ['url' => route('register')])
Create Account
@endcomponent
@endif

Once you're ready, click the button below to accept the invitation:

@component('mail::button', ['url' => $acceptUrl])
Accept Invitation
@endcomponent

This invitation will expire in 7 days.

If you did not expect to receive an invitation to this team, you may discard this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
