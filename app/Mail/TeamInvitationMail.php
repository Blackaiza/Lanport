<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Team;

class TeamInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $team;
    public $token;
    public $user;

    public function __construct($team, $token, $user = null)
    {
        $this->team = $team;
        $this->token = $token;
        $this->user = $user;
    }

    public function build()
    {
        return $this->markdown('emails.team_invitation')
                    ->subject("Invitation to join {$this->team->name}")
                    ->with([
                        'acceptUrl' => route('team.accept-invitation', $this->token),
                        'team' => $this->team,
                        'token' => $this->token,
                        'user' => $this->user,
                    ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Team Invitation Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.team_invitation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
