<?php

namespace App\Services\Booking;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    /**
     * Render the invoice to PDF and store it, recording the path on the
     * invoice row. Kept separate from the booking transaction: a disk write
     * isn't undone by a DB rollback, so this only runs once the appointment,
     * payment, and invoice rows are already safely committed.
     */
    public function generate(Invoice $invoice): Invoice
    {
        $invoice->loadMissing(['appointment.clinic', 'appointment.doctorService.service', 'patient', 'doctor']);

        $pdf = Pdf::loadView('frontend.invoices.pdf', ['invoice' => $invoice]);

        $path = 'invoices/'.$invoice->invoice_number.'.pdf';

        Storage::disk('s3')->put($path, $pdf->output(), 'public');

        $invoice->forceFill(['pdf_path' => $path])->save();

        return $invoice;
    }
}
