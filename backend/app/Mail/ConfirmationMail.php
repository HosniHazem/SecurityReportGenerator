<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $name;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param string $subject
     * @param string $name
     * @param string $password
     * @return void
     */
    public function __construct($subject, $name, $password)
    {
        $this->subject = $subject;
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.email_template');
    }
}
