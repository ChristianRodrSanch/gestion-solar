<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Validar datos del formulario
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:50'],   // opcional
            'subject' => ['nullable', 'string', 'max:255'],  // opcional
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Si hay usuario logueado, lo añadimos como info extra (opcional)
        $user = $request->user();
        $data['user_id'] = $user?->id;

        // Enviar email al admin (Mailtrap)
        $to = config('mail.from.address'); // o pon aquí el correo del admin si quieres
        Mail::to($to)->send(new ContactMessageMail($data));

        return response()->json([
            'message' => 'Mensaje enviado correctamente',
        ], 201);
    }
}
