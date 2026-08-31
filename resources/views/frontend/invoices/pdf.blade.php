<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1a1a1a; }
        .header { width: 100%; overflow: hidden; margin-bottom: 24px; }
        .header .brand { float: left; font-size: 22px; font-weight: bold; color: #0d6efd; }
        .header .meta { float: right; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        .parties { width: 100%; margin-bottom: 24px; }
        .parties td { vertical-align: top; width: 50%; }
        .parties h4 { margin: 0 0 6px; font-size: 13px; }
        .parties p { margin: 0; line-height: 1.5; }
        .items { margin-top: 16px; }
        .items th { background: #f1f5f9; text-align: left; padding: 8px; font-size: 11px; text-transform: uppercase; }
        .items td { padding: 8px; border-bottom: 1px solid #e6e8ee; }
        .items .amount { text-align: right; }
        .totals { width: 40%; margin-left: 60%; margin-top: 16px; }
        .totals td { padding: 6px 8px; }
        .totals .grand-total { font-weight: bold; font-size: 14px; border-top: 2px solid #1a1a1a; }
        .status { display: inline-block; padding: 4px 10px; border-radius: 12px; background: #dcfce7; color: #166534; font-weight: bold; }
        .footer { margin-top: 40px; font-size: 10px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Doccure</div>
        <div class="meta">
            <strong>Invoice #{{ $invoice->invoice_number }}</strong><br>
            Appointment #{{ $invoice->appointment->appointment_number }}<br>
            Date: {{ $invoice->generated_at?->format('d M Y') }}<br>
            <span class="status">{{ ucfirst($invoice->status) }}</span>
        </div>
    </div>

    <table class="parties">
        <tr>
            <td>
                <h4>Patient</h4>
                <p>
                    {{ $invoice->appointment->first_name }} {{ $invoice->appointment->last_name }}<br>
                    {{ $invoice->appointment->email }}<br>
                    {{ $invoice->appointment->phone }}
                </p>
            </td>
            <td>
                <h4>Doctor</h4>
                <p>
                    Dr. {{ $invoice->doctor->display_name ?: trim($invoice->doctor->first_name.' '.$invoice->doctor->last_name) }}<br>
                    @if ($invoice->doctor->designation) {{ $invoice->doctor->designation }}<br> @endif
                    @if ($invoice->appointment->clinic) {{ $invoice->appointment->clinic->name }} @endif
                </p>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Service</th>
                <th>Duration</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->appointment->services as $line)
                <tr>
                    <td>{{ $line->service_name }}</td>
                    <td>{{ $line->duration_minutes }} mins</td>
                    <td class="amount">${{ number_format((float) $line->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="amount">${{ number_format((float) $invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Tax</td>
            <td class="amount">${{ number_format((float) $invoice->tax, 2) }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td class="amount">-${{ number_format((float) $invoice->discount, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total</td>
            <td class="amount">${{ number_format((float) $invoice->total, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        This is a system-generated invoice for appointment {{ $invoice->appointment->appointment_number }}. Thank you for choosing Doccure.
    </div>
</body>
</html>
