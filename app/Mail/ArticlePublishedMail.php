<?php
// app/Mail/ArticlePublishedMail.php

namespace App\Mail;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticlePublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Article $article,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nouvel article : {$this->article->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.article_published',
            with: [
                'articleTitle'      => $this->article->title,
                'articleDescription' => $this->article->description,
                'authorName'        => $this->article->author->name,
                'articleUrl'        => config('app.frontend_url') . '/articles/' . $this->article->slug,
            ],
        );
    }
}
