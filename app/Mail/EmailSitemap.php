<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailSitemap extends Mailable
{
    use Queueable, SerializesModels;

    public string $sitemap;

    public function __construct($sitemap)
    {
        $this->sitemap = $sitemap;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Your Sitemap',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.sitemap',
        );
    }

    public function attachments()
    {
        return [
            Attachment::fromData(fn() => $this->sitemap, 'sitemap.xml')
                ->withMime('application/xml'),
        ];
    }
}
