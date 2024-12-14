<?php
<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class WelcomeEmail extends Mailable
{
    public $user;
	
    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return \Illuminate\Mail\Mailables\Message
     */
    public function build()
    {
        return $this->view('emails.welcome') // This is the email view file
                    ->subject('Welcome to Our Application')
                    ->with([
                        'user' => $this->user,
                    ]);
    }
}
