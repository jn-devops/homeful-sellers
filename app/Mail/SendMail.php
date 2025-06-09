<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($template, array $data)
    {
        $this->data = $data;
        $this->template = $template;
    }

    public function build()
    {
        // dd($this->data);
        return $this->subject($this->data['subject'])
                    ->view('mailTemplate.'.$this->template)
                    ->with('body', $this->data);; 
    }
}
