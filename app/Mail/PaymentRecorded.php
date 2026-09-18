<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PaymentRecorded extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payment $payment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Received - Adili Real Estate',
        );
    }

    public function content(): Content
    {
        $payment = $this->payment;
        $sale = $payment->sale;
        $totalPaid = $sale->payments()->sum('amount');
        $remaining = max(0, $sale->total_price - $totalPaid);
        $progress = $sale->total_price > 0
            ? round(($totalPaid / $sale->total_price) * 100, 2)
            : 0;

        return new Content(
            view: 'emails.payment-recorded',
            with: [
                'buyerName' => $sale->buyer->name,
                'plotNumber' => $sale->plot->plot_number,
                'projectName' => $sale->project->name,
                'amount' => $payment->amount,
                'method' => $payment->method,
                'reference' => $payment->reference,
                'allocatedTo' => $payment->allocated_to,
                'date' => $payment->created_at->format('d M Y, H:i'),
                'totalPaid' => $totalPaid,
                'remaining' => $remaining,
                'progress' => $progress,
                'totalPrice' => $sale->total_price,
            ],
        );
    }

    public function attachments(): array
    {
        if (! $this->payment->receipt_path) {
            return [];
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($this->payment->receipt_path)) {
            return [];
        }

        return [
            Attachment::fromPath($disk->path($this->payment->receipt_path))
                ->as('Receipt-'.$this->payment->id.'.'.pathinfo($this->payment->receipt_path, PATHINFO_EXTENSION))
                ->withMime($disk->mimeType($this->payment->receipt_path) ?: 'application/octet-stream'),
        ];
    }
}
