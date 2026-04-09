<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 620px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #4e73df; padding: 30px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: rgba(255,255,255,0.85); margin: 5px 0 0; font-size: 13px; }
        .body { padding: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0 25px; }
        table th { background: #f8f9fc; text-align: left; padding: 9px 14px; font-size: 12px; color: #6e707e; border-bottom: 2px solid #e3e6f0; }
        table td { padding: 9px 14px; font-size: 14px; border-bottom: 1px solid #f0f0f0; color: #3a3b45; }
        .stat-box { display: inline-block; background: #f8f9fc; border-radius: 6px; padding: 15px 20px; margin: 5px; min-width: 120px; text-align: center; }
        .stat-num { font-size: 26px; font-weight: bold; color: #4e73df; }
        .stat-lbl { font-size: 11px; color: #858796; margin-top: 4px; }
        .stat-num.green { color: #1cc88a; }
        .stat-num.red { color: #e74a3b; }
        .footer { background: #f8f9fc; padding: 20px; text-align: center; font-size: 12px; color: #858796; border-top: 1px solid #e3e6f0; }
        .section-title { font-size: 14px; font-weight: bold; color: #3a3b45; margin: 20px 0 8px; border-bottom: 2px solid #e3e6f0; padding-bottom: 5px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📊 Monthly Finance Report</h1>
        <p>{{ $report['month'] }} — Automated Summary</p>
    </div>

    <div class="body">
        <p style="color:#3a3b45;">Hi <strong>{{ $admin->name }}</strong>,</p>
        <p style="color:#858796; font-size:13px;">Berikut adalah ringkasan kewangan untuk bulan <strong>{{ $report['month'] }}</strong>.</p>

        {{-- Summary Stats --}}
        <div style="text-align:center; margin: 20px 0;">
            <div class="stat-box">
                <div class="stat-num green">RM {{ number_format($report['total_revenue'], 2) }}</div>
                <div class="stat-lbl">TOTAL REVENUE</div>
            </div>
            <div class="stat-box">
                <div class="stat-num red">RM {{ number_format($report['total_expense'], 2) }}</div>
                <div class="stat-lbl">TOTAL EXPENSE</div>
            </div>
            <div class="stat-box">
                <div class="stat-num" style="color:{{ $report['net'] >= 0 ? '#1cc88a' : '#e74a3b' }};">
                    RM {{ number_format(abs($report['net']), 2) }}
                </div>
                <div class="stat-lbl">NET {{ $report['net'] >= 0 ? 'PROFIT' : 'LOSS' }}</div>
            </div>
        </div>

        {{-- Invoice Breakdown --}}
        <div class="section-title">📋 Invoice Summary</div>
        <table>
            <tr>
                <th>Category</th>
                <th>Total Invoices</th>
                <th>Outstanding</th>
            </tr>
            <tr>
                <td>Customer Invoices</td>
                <td>{{ $report['customer_invoice_count'] }}</td>
                <td style="color:#e74a3b; font-weight:bold;">{{ $report['outstanding_customer'] }} belum selesai</td>
            </tr>
            <tr>
                <td>Vendor Invoices</td>
                <td>{{ $report['vendor_invoice_count'] }}</td>
                <td style="color:#e74a3b; font-weight:bold;">{{ $report['outstanding_vendor'] }} belum selesai</td>
            </tr>
        </table>

        <div style="text-align:center; margin-top:15px;">
            <a href="{{ config('app.url') }}/customers/finance" style="display:inline-block;background:#4e73df;color:#ffffff;padding:11px 28px;border-radius:5px;text-decoration:none;font-weight:bold;margin:0 5px;">Customer Finance</a>
            <a href="{{ config('app.url') }}/vendors/finance" style="display:inline-block;background:#858796;color:#ffffff;padding:11px 28px;border-radius:5px;text-decoration:none;font-weight:bold;margin:0 5px;">Vendor Finance</a>
        </div>
    </div>

    <div class="footer">
        <p>{{ config('app.name') }} — Automated Monthly Finance Report</p>
        <p>Email ini dihantar secara automatik pada 1hb setiap bulan. Sila jangan reply.</p>
    </div>
</div>
</body>
</html>
