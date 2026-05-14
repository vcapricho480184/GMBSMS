<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $billing->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #1e293b; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4F46E5; padding-bottom: 20px; }
        .header h1 { font-size: 24px; color: #4F46E5; margin-bottom: 4px; }
        .header p { color: #64748b; font-size: 11px; }
        .receipt-title { text-align: center; margin: 20px 0; }
        .receipt-title h2 { font-size: 16px; color: #1e293b; text-transform: uppercase; letter-spacing: 2px; }
        .info-row { display: flex; margin-bottom: 25px; }
        .info-block { width: 50%; }
        .info-block h4 { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .info-block p { margin-bottom: 3px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th { background: #f1f5f9; color: #475569; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        .total-row td { font-weight: 700; font-size: 14px; border-top: 2px solid #1e293b; border-bottom: none; }
        .status { display: inline-block; padding: 3px 10px; border-radius: 50px; font-size: 10px; font-weight: 600; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .footer { margin-top: 40px; text-align: center; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FitCore Elite</h1>
        <p>Premium Fitness & Wellness Center</p>
    </div>

    <div class="receipt-title">
        <h2>Official Receipt</h2>
    </div>

    <table style="margin-bottom:25px;border:none;">
        <tr style="border:none;">
            <td style="border:none;padding:3px 0;width:50%;">
                <strong style="color:#64748b;font-size:10px;text-transform:uppercase;">Billed To</strong><br>
                <strong>{{ $billing->user->name ?? 'N/A' }}</strong><br>
                {{ $billing->user->email ?? '' }}<br>
                {{ $billing->user->phone ?? '' }}
            </td>
            <td style="border:none;padding:3px 0;text-align:right;">
                <strong style="color:#64748b;font-size:10px;text-transform:uppercase;">Receipt Details</strong><br>
                <strong>{{ $billing->invoice_number }}</strong><br>
                Date: {{ $billing->payment_date->format('F d, Y') }}<br>
                Method: {{ $billing->payment_method_label }}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Type</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $billing->description ?? 'N/A' }}
                    @if($billing->membership && $billing->membership->membershipPlan)
                        <br><small style="color:#64748b;">Plan: {{ $billing->membership->membershipPlan->name }} ({{ $billing->membership->membershipPlan->duration_label }})</small>
                    @endif
                    @if($billing->availedService && $billing->availedService->gymService)
                        <br><small style="color:#64748b;">Service: {{ $billing->availedService->gymService->name }}</small>
                    @endif
                </td>
                <td>{{ ucfirst($billing->type) }}</td>
                <td style="text-align:right;">&#8369;{{ number_format($billing->amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align:right;">TOTAL</td>
                <td style="text-align:right;">&#8369;{{ number_format($billing->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top:10px;">
        <strong>Payment Status:</strong>
        <span class="status status-{{ $billing->payment_status }}">{{ strtoupper($billing->payment_status) }}</span>
    </p>

    <div class="footer">
        <p>This is a computer-generated receipt and does not require a signature.</p>
        <p style="margin-top:5px;">FitCore Elite &copy; {{ date('Y') }} | Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
