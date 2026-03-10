<?php

namespace App\Mail\Backstage\Users;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build(): static
    {
        return $this->subject('Welcome to: '.config('app.name'))
            ->markdown('backstage.emails.users.welcome');
    }
}
