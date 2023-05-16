<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Confirmmail extends Mailable
{
    use Queueable, SerializesModels;
    public $maildatas;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($maildatas)
    {
        $this->maildatas = $maildatas;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('あんしん見守り登録確認')
            ->view('mails.confirmmail');
    }
}
