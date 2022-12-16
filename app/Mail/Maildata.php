<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Maildata extends Mailable
{
    use Queueable, SerializesModels;
    public $maildatas;
    public $gps_url;
    public $wanderer_time;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($maildatas, $gps_url, $wanderer_time)
    {
        $this->maildatas = $maildatas;
        $this->gps_url = $gps_url;
        $this->wanderer_time = $wanderer_time;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('発見通知')
            ->view('mails.template');
    }
}
