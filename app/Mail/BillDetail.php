<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BillDetail extends Mailable
{
    use Queueable, SerializesModels;

    public $name,$units,$amount,$date,$url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$billAmount,$units,$dueDate,$url)
    {
        $this->name = $name;
        $this->units = $units;
        $this->amount = $billAmount;
        $this->date = $dueDate;
        $this->url = $url;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'New Bill Generated',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.bill-detail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
