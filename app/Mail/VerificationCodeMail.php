<?php
// backend-laravel-server/app/Mail/VerificationCodeMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $companyName = 'Addis Ababa Science and Technology University Research Grant Management System';

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Email Verification Code')
                    ->view('emails.verification_code')
                    ->with([
                        'verificationCode' => $this->user->verification_code,
                        'companyName' => $this->companyName,
                        'logoPath' => storage_path('app/public/assets/logo.png'),
                    ]);
    }
}