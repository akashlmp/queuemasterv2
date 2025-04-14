<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PrequeueMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailContent;

    /**
     * Create a new message instance.
     *
     * @param  string  $emailContent
     *
     * @return void
     */
    public function __construct($emailContent)
    {
        $this->emailContent = $emailContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.prequeuetemp')
            ->with('emailContent', $this->emailContent);
    }
}
