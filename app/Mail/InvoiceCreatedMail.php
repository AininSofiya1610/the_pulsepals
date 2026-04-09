<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #1cc88a; padding: 30px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 24px; }
        .header p { color: rgba(255,255,255,0.9); margin: 5px 0 0; }
        .body { padding: 30px; }
        .alert-box { background: #eafaf4; border-left: 4px solid #1cc88a; padding: 15px 20px; border-radius: 4px; margin-bottom: 25px; }
        .alert-box p { margin: 0; color: #0e7a52; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th { background: #f8f9fc; text-align: left; padding: 10px 14px; font-size: 13px; color: #6e707e; border-bottom: 2px solid #e3e6f0; }
        table td { padding: 10px 14px; font-size: 14px; border-bottom: 1px solid #f0f0f0; color: #3a3b45; }
        .amount { font-size: 28px; font-weight: bold; color: #1cc88a; text-align: center; margin: 20px 0; }
        .label { font-size: 12px; color: #858796; text-align: center; }
        .badge-new { display: inline-block; background: #1cc88a; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .footer { background: #f8f9fc; padding: 20px; text-align: center; font-size: 12px; color: #858796; border-top: 1px solid #e3e6f0; }
        .btn { display: inline-block; background: #1cc88a; color: #fff; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: bold; margin: 20px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>✅ Invoice Baru Dibuat</h1>
        <p>{{ $type === 'customer' ? 'Customer Finance' : 'Vendor Finance' }}</p>
    </div>
    <div class="body">
        <div class="alert-box">
            <p>Invoice baru telah berjaya dicipta dalam sistem.</p>
        </div>
        <p style="color:#3a3b45;">Hi <strong>{{ $admin->name }}</strong>,</p>
        <p style="color:#858796; font-size:14px;">
            Invoice baru <span class="badge-new">NEW</span> telah dibuat untuk
            {{ $type === 'customer' ? 'customer' : 'vendor' }} berikut.
        </p>
        <table>
            <tr><th>Invoice No</th><td><strong>{{ $invoice->invoice_no }}</strong></td></tr>
            <tr>
                <th>{{ $type === 'customer' ? 'Customer' : 'Vendor' }} Name</th>
                <td>{{ $type === 'customer' ? $invoice->customer_name : $invoice->vendor_name }}</td>
            </tr>
            <tr><th>Invoice Date</th><td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td></tr>
            <tr>
                <th>Due Date</th>
                <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</td>
            </tr>
            <tr><th>Description</th><td>{{ $invoice->description ?? '-' }}</td></tr>
        </table>
        <div class="amount">RM {{ number_format($invoice->amount, 2) }}</div>
        <div class="label">INVOICE AMOUNT</div>
        <div style="text-align:center;">
            <a href="{{ config('app.url') }}/{{ $type === 'customer' ? 'customers' : 'vendors' }}/finance" class="btn">View Invoice</a>
        </div>
    </div>
    <div class="footer">
        <p>{{ config('app.name') }} &mdash; Automated Finance Notification</p>
        <p>Email ini dihantar secara automatik. Sila jangan reply.</p>
    </div>
</div>
</body>
</html>
