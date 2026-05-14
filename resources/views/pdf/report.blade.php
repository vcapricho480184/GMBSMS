<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1e293b; padding: 30px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #4F46E5; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #4F46E5; margin-bottom: 3px; }
        .header h2 { font-size: 14px; color: #1e293b; margin-top: 10px; }
        .header p { color: #64748b; font-size: 10px; }
        .summary { background: #f8fafc; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; }
        .summary span { margin: 0 15px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background: #f1f5f9; color: #475569; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 10px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        table td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .total { font-weight: 700; font-size: 13px; text-align: right; margin-top: 10px; }
        .footer { margin-top: 30px; text-align: center; color: #94a3b8; font-size: 9px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FitCore Elite</h1>
        <p>Premium Fitness & Wellness Center</p>
        <h2>{{ $title }}</h2>
        <p>Period: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
    </div>

    <div class="summary">
        <span><strong>Total Transactions:</strong> {{ $transactions->count() }}</span>
        <span><strong>Total Revenue:</strong> &#8369;{{ number_format($total, 2) }}</span>
    </div>

    <table>
        <thead>
            <tr><th>Invoice</th><th>Member</th><th>Description</th><th>Method</th><th style="text-align:right;">Amount</th><th>Date</th></tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->invoice_number }}</td>
                <td>{{ $t->user->name ?? 'N/A' }}</td>
                <td>{{ $t->description }}</td>
                <td>{{ $t->payment_method_label }}</td>
                <td style="text-align:right;">&#8369;{{ number_format($t->amount, 2) }}</td>
                <td>{{ $t->payment_date->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">Total: &#8369;{{ number_format($total, 2) }}</div>

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y h:i A') }} | FitCore Elite &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
