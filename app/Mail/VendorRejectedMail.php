<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;


class VendorRejectedMail extends Mailable
{
    public function __construct(public \App\Models\User $user, public ?string $reason = null) {}

    public function build(){
        return $this->subject('Tu solicitud de vendedor ha sido rechazada')
            ->view('mail.vendor_rejected', [
                'name'=>$this->user->name,
                'reason'=>$this->reason,
            ]);
    }
}
