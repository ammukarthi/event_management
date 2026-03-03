<?php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;

trait MailTrait
{
    public function sendMail($to, $subject, $message)
    {
        Mail::raw($message, function ($mail) use ($to, $subject) {
            $mail->to($to)
                 ->subject($subject);
        });
    }
}