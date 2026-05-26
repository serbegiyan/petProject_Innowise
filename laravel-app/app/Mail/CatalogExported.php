<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CatalogExported extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $filePath) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Catalog Exported',
        );
    }

    public function content(): Content
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        return new Content(
            view: 'emails.catalog_exported',
            with: [
                'url' => $disk->temporaryUrl(
                    $this->filePath,
                    now()->addHours(24)
                ),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
