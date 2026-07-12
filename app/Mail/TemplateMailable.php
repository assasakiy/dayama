<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $template;
    public $data;

    public function __construct(string $templateKey, array $data = [])
    {
        $this->template = EmailTemplate::where('key', $templateKey)->firstOrFail();
        
        $brandName = config('app.name');
        $footerText = "© " . date('Y') . " {$brandName}. All rights reserved.";
        $data['brand_name'] = $data['brand_name'] ?? $brandName;
        $data['footer_brand'] = $data['footer_brand'] ?? $footerText;
        
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->interpolate($this->template->subject),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->interpolate($this->template->body),
        );
    }

    /**
     * Replace {{ var }} with $this->data['var'] escaped.
     */
    protected function interpolate(string $text): string
    {
        foreach ($this->data as $key => $value) {
            if (!is_string($value)) {
                $value = (string) $value;
            }
            $isSafeUrl = (bool) preg_match('#^https?://#i', $value);
            $safeValue = $isSafeUrl ? $value : e($value);
            $text = preg_replace('/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/i', $safeValue, $text);
        }
        return $text;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
