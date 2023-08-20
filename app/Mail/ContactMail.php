<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $mail = $this->from('from@example.com')  // 送信元のアドレス
        ->subject('新しい問い合わせ')  // メールの件名
        ->view('emails.contact')  // メールのビュー
        ->with('data', $this->data);
        
        // 添付画像がある場合、添付します
        if (isset($this->data['referenceImage'])) {
            $mail->attachData($this->data['referenceImage']->get(), $this->data['referenceImage']->getClientOriginalName());
        }

        return $mail;
    }
}
