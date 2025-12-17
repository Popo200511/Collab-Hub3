<?php


namespace App\Mail;

use Illuminate\Mail\Mailable;

class UpdateMail extends Mailable
{
    public $messageContent;

    public function __construct($messageContent)
    {
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        return $this->subject('Test Update Notification Mail')
                    ->view('emails.site_update_notification')  // ชื่อ view สำหรับอีเมล
                    ->with([
                        'messageContent' => $this->messageContent,
                    ]);
    }
}
