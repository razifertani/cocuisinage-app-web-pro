<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvitationLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $owner_fullname;
    public $employe_fullname;
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($owner_fullname, $employe_fullname, $url)
    {
        $this->owner_fullname = $owner_fullname;
        $this->employe_fullname = $employe_fullname;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Invitation pour accéder à votre planning de travail')
            ->view('emails.invitation_email')
            ->with([
                'owner_fullname' => $this->owner_fullname,
                'employe_fullname' => $this->employe_fullname,
                'url' => $this->url,
            ]);

    }
}
