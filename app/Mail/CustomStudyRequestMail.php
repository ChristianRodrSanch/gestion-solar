<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomStudyRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function build()
    {
        return $this->subject('Hemos recibido tu solicitud de estudio personalizado')
            ->view('mail.custom_study_request', [
                'user' => $this->user,
            ]);
    }
}
