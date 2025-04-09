<?php

namespace App\Mail;

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DynamicTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $viewName;
    public $subjectLine;

    public function __construct($data, $subjectLine, $viewName)
    {
        $this->data = $data;
        $this->subjectLine = $subjectLine;
        $this->viewName = $viewName;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view($this->viewName)
                    ->with('data', $this->data);
    }
}
