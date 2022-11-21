<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendResetPasswordTokenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fullname;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fullname, $token)
    {
        $this->fullname = $fullname;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Réinitialiser votre mot de passe')
            ->view('emails.reset_password_email')
            ->with([
                'fullname' => $this->fullname,
                'token' => $this->token,
            ]);
    }
}
