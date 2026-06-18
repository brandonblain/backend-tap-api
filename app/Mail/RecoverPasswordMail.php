<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecoverPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $temporaryPassword;
    public $userName;

    /**
     * Constructor para recibir los datos desde el AuthController
     */
    public function __construct($temporaryPassword, $userName)
    {
        $this->temporaryPassword = $temporaryPassword;
        $this->userName = $userName;
    }

    /**
     * Definir el asunto del correo institucional
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuperación de Credenciales - Terminal TAP',
        );
    }

    /**
     * Definir el cuerpo del mensaje en formato HTML limpio
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                    <h2 style='color: #111827;'>Control de Accesos - Grupo TAP</h2>
                    <p>Hola, <strong>{$this->userName}</strong>.</p>
                    <p>Hemos recibido una solicitud para recuperar tus credenciales de acceso a la plataforma administrativa.</p>
                    <div style='background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb; display: inline-block;'>
                        <p style='margin: 0; font-size: 14px; color: #4b5563;'>Tu nueva contraseña temporal es:</p>
                        <p style='margin: 5px 0 0 0; font-size: 22px; font-weight: bold; color: #111827; letter-spacing: 2px;'>{$this->temporaryPassword}</p>
                    </div>
                    <p style='font-size: 12px; color: #6b7280;'>Por seguridad, te recomendamos cambiar esta contraseña inmediatamente después de iniciar sesión en tu perfil.</p>
                    <hr style='border: 0; border-top: 1px solid #eee; margin-top: 30px;'>
                    <p style='font-size: 11px; color: #9ca3af;'>Este es un correo automático generado por el servidor, por favor no respondas a este mensaje.</p>
                </div>
            "
        );
    }
}