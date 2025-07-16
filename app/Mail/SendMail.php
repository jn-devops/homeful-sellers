<?php

// namespace App\Mail;

// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Mail\Mailable;
// use Illuminate\Mail\Mailables\Content;
// use Illuminate\Mail\Mailables\Envelope;
// use Illuminate\Queue\SerializesModels;

// class SendMail extends Mailable
// {
//     use Queueable, SerializesModels;

//     public $data;

//     public function __construct($template, array $data)
//     {
//         $this->data = $data;
//         $this->template = $template;
//     }

//     public function build()
//     {
//         // dd($this->data);
//         return $this->subject($this->data['subject'])
//                     ->view('mailTemplate.'.$this->template)
//                     ->with('body', $this->data);; 
//     }
// }
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $template;
    public $htmlContent;

    public function __construct($template = null, array $data = [], $htmlBody = null)
    {
        $this->data = $data;
        $this->template = $template;
        $this->htmlBody = $htmlBody;
    }
    public function build()
    {
    $email = $this->subject($this->data['subject'] ?? 'No Subject');

    if ($this->htmlBody && $this->template === "freeform") {
        $renderedView = view('mailTemplate.freeform', ['body' => $this->data])->render();
        $combinedContent = $this->htmlBody . $renderedView;
        return $email->html($combinedContent);
    }

    if ($this->htmlBody) {
        return $email->html($this->htmlBody);
    }

    if ($this->template === "freeform") {
        return $email->view('mailTemplate.freeform')->with('body', $this->data);
    }

    if ($this->template && view()->exists('mailTemplate.' . $this->template)) {
        return $email->view('mailTemplate.' . $this->template)->with('body', $this->data);
    }
    return $email->text('mailTemplate.default')->with('body', $this->data);
    }

}

