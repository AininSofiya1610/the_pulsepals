<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 680px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #e74a3b; padding: 28px 30px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: rgba(255,255,255,0.85); margin: 5px 0 0; font-size: 13px; }
        .body { padding: 28px 30px; }
        .alert-box { background: #fdf3f3; border-left: 4px solid #e74a3b; padding: 12px 18px; border-radius: 4px; margin-bottom: 22px; }
        .alert-box p { margin: 0; color: #c0392b; font-weight: bold; font-size: 13px; }
        .stat-row { text-align: center; margin: 18px 0 22px; }
        .stat-item { display: inline-block; margin: 0 15px; }
        .stat-num { font-size: 30px; font-weight: bold; color: #e74a3b; }
        .stat-lbl { font-size: 11px; color: #858796; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table th { background: #f8f9fc; text-align: left; padding: 9px 12px; font-size: 12px; color: #6e707e; border-bottom: 2px solid #e3e6f0; white-space: nowrap; }
        table td { padding: 9px 12px; font-size: 13px; border-bottom: 1px solid #f0f0f0; color: #3a3b45; vertical-align: middle; }
        table tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; background: #e74a3b; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
        .amount { font-weight: bold; color: #e74a3b; white-space: nowrap; }
        .more-box { background: #f8f9fc; border: 1px dashed #e3e6f0; border-radius: 6px; padding: 15px; text-align: center; margin: 15px 0 20px; }
        .more-box p { margin: 0 0 10px; font-size: 13px; color: #858796; }
        .footer { background: #f8f9fc; padding: 18px; text-align: center; font-size: 12px; color: #858796; border-top: 1px solid #e3e6f0; }
        .page-info { font-size: 12px; color: #858796; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>⚠ {{ $type === 'customer' ? 'Customer' : 'Vendor' }} Overdue Invoices</h1>
        <p>{{ now()->format('d M Y') }} — Automated Finance Notification</p>
    </div>

    <div class="body">
        <p style="color:#3a3b45; margin-bottom:12px;">Hi <strong>{{ $admin->name }}</strong>,</p>
        <div class="alert-box">
            <p>Terdapat {{ $total }} {{ $type === 'customer' ? 'customer' : 'vendor' }} invoice yang telah melebihi due date. Sila ambil tindakan segera.</p>
        </div>

        {{-- Summary Stats --}}
        <div class="stat-row">
            <div class="stat-item">
                <div class="stat-num">{{ $total }}</div>
                <div class="stat-lbl">TOTAL OVERDUE</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">RM {{ number_format($invoices->sum('_balance'), 2) }}</div>
                <div class="stat-lbl">OUTSTANDING (TOP {{ count($invoices) }})</div>
            </div>
        </div>

        {{-- Page info --}}
        <p class="page-info">
            Menunjukkan <strong>{{ count($invoices) }}</strong> daripada <strong>{{ $total }}</strong> invoice
            (disusun: paling lama overdue dahulu)
        </p>

        {{-- Invoice Table --}}
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice No</th>
                    <th>{{ $type === 'customer' ? 'Customer' : 'Vendor' }}</th>
                    <th>Due Date</th>
                    <th>Overdue</th>
                    <th>Outstanding</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices->values() as $i => $inv)
                @php $days = \Carbon\Carbon::parse($inv->due_date)->diffInDays(\Carbon\Carbon::today()); @endphp
                <tr>
                    <td style="color:#aaa;font-size:12px;">{{ $i + 1 }}</td>
                    <td><strong>{{ $inv->invoice_no }}</strong></td>
                    <td>{{ $type === 'customer' ? $inv->customer_name : $inv->vendor_name }}</td>
                    <td style="color:#e74a3b; white-space:nowrap;">{{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}</td>
                    <td><span class="badge">{{ $days }} hari</span></td>
                    <td class="amount">RM {{ number_format($inv->_balance, 2) }}</td>
                    <td>
                        <a href="{{ config('app.url') }}/{{ $type === 'customer' ? 'customers' : 'vendors' }}/finance"
                           style="display:inline-block;background:#4e73df;color:#ffffff;padding:4px 12px;border-radius:4px;text-decoration:none;font-size:12px;font-weight:bold;">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Remaining notice --}}
        @if ($remaining > 0)
        <div class="more-box">
            <p>📋 Dan <strong>{{ $remaining }} invoice lagi</strong> yang tidak dipaparkan di sini.</p>
            <a href="{{ config('app.url') }}/{{ $type === 'customer' ? 'customers' : 'vendors' }}/finance"
               style="display:inline-block;background:#e74a3b;color:#ffffff;padding:10px 26px;border-radius:5px;text-decoration:none;font-weight:bold;font-size:13px;">
                View All {{ $total }} Overdue Invoices
            </a>
        </div>
        @else
        <div style="text-align:center; margin:15px 0;">
            <a href="{{ config('app.url') }}/{{ $type === 'customer' ? 'customers' : 'vendors' }}/finance"
               style="display:inline-block;background:#e74a3b;color:#ffffff;padding:10px 26px;border-radius:5px;text-decoration:none;font-weight:bold;font-size:13px;">
                View All in System
            </a>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>{{ config('app.name') }} — Automated Finance Notification</p>
        <p>Email ini dihantar secara automatik. Sila jangan reply.</p>
    </div>
</div>
</body>
</html>
