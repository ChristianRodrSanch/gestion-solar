<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function build()
    {
        $body = $this->data['message'] ?? '';
        return $this->subject('Nuevo mensaje de contacto')
            ->view('mail.contact_message', [
                'name'    => $this->data['name']    ?? '',
                'email'   => $this->data['email']   ?? '',
                'message' => $this->data['message'] ?? '',
                'user_id' => $this->data['user_id'] ?? null,
                'body'    => $body,
            ]);
    }
}
