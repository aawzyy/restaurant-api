<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // Import ini sudah benar
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// --- PERBAIKAN ADA DI BARIS BAWAH INI ---
// Tambahkan 'implements ShouldQueue' agar Laravel tahu ini harus antre
class SendOtpMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;

    public function __construct($otp, $userName)
    {
        $this->otp = $otp;
        $this->userName = $userName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi OTP - Restoran App',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp', // Pastikan file view ini ada
        );
    }
}