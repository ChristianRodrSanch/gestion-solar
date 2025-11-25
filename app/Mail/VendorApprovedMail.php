<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;



class VendorApprovedMail extends Mailable
{
    public function __construct(public \App\Models\User $user, public ?string $note = null) {}

    public function build(){
        return $this->subject('Tu cuenta de vendedor ha sido aprobada')
            ->view('mail.vendor_approved', [
                'name'=>$this->user->name,
                'note'=>$this->note,
            ]);
    }
}
