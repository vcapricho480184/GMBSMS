<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $billing->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 11px; 
            color: #1e293b; 
            padding: 30px;
            line-height: 1.6;
        }
        .header { 
            margin-bottom: 30px; 
            padding-bottom: 20px; 
            border-bottom: 3px solid #4F46E5;
        }
        .header-row {
            display: table;
            width: 100%;
        }
        .header-left, .header-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .header-right {
            text-align: right;
        }
        .company-name { 
            font-size: 26px; 
            color: #4F46E5; 
            font-weight: 700;
            margin-bottom: 5px;
        }
        .company-tagline {
            color: #64748b;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .company-info {
            color: #64748b;
            font-size: 10px;
            line-height: 1.5;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
        }
        .invoice-meta {
            font-size: 10px;
            line-height: 1.8;
        }
        .invoice-meta strong {
            color: #1e293b;
            display: inline-block;
            width: 90px;
        }
        .section-title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .bill-to-section {
            margin: 25px 0;
            display: table;
            width: 100%;
        }
        .bill-to-left, .bill-to-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .bill-to-right {
            text-align: right;
        }
        .bill-to-info {
            font-size: 11px;
            line-height: 1.6;
        }
        .bill-to-info strong {
            font-size: 12px;
            color: #1e293b;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-failed { background: #fee2e2; color: #991b1b; }
        .status-refunded { background: #dbeafe; color: #1e40af; }
        .status-overdue { background: #fee2e2; color: #991b1b; margin-left: 5px; }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
        }
        table.items-table th { 
            background: #f1f5f9; 
            color: #475569; 
            font-size: 10px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            padding: 12px; 
            text-align: left; 
            border: 1px solid #e2e8f0;
            font-weight: 700;
        }
        table.items-table td { 
            padding: 12px; 
            border: 1px solid #e2e8f0; 
            font-size: 11px;
            vertical-align: top;
        }
        table.items-table td.description {
            width: 60%;
        }
        table.items-table td.type {
            width: 15%;
        }
        table.items-table td.amount {
            width: 25%;
            text-align: right;
            font-weight: 600;
        }
        .item-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .item-detail {
            color: #64748b;
            font-size: 10px;
            line-height: 1.5;
        }
        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .type-membership { background: #dbeafe; color: #1e40af; }
        .type-service { background: #e0e7ff; color: #4338ca; }
        .type-other { background: #f1f5f9; color: #475569; }
        .type-mixed { background: #e2e8f0; color: #334155; }
        
        .totals-section {
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        .totals-left, .totals-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }
        .notes-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-right: 20px;
        }
        .notes-box h6 {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .notes-box p {
            font-size: 10px;
            color: #475569;
            line-height: 1.5;
        }
        table.totals-table {
            margin: 0;
            width: 100%;
        }
        table.totals-table td {
            padding: 8px 0;
            border: none;
            font-size: 11px;
        }
        table.totals-table td:first-child {
            text-align: right;
            padding-right: 15px;
            color: #64748b;
        }
        table.totals-table td:last-child {
            text-align: right;
            font-weight: 600;
            width: 120px;
        }
        table.totals-table tr.discount td {
            color: #dc2626;
        }
        table.totals-table tr.total {
            border-top: 2px solid #1e293b;
            padding-top: 10px;
        }
        table.totals-table tr.total td {
            font-size: 14px;
            font-weight: 700;
            color: #4F46E5;
            padding-top: 12px;
        }
        .payment-info {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 12px;
            margin: 20px 0;
        }
        .payment-info h6 {
            font-size: 10px;
            color: #1e40af;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-weight: 700;
        }
        .payment-info p {
            font-size: 10px;
            color: #1e40af;
            margin: 3px 0;
        }
        .footer { 
            margin-top: 40px; 
            text-align: center; 
            color: #94a3b8; 
            font-size: 9px; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 15px;
        }
        .footer p {
            margin: 3px 0;
        }
        .footer .thank-you {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-row">
            <div class="header-left">
                <div class="company-name">FitCore Elite</div>
                <div class="company-tagline">Premium Fitness & Wellness Center</div>
                <div class="company-info">
                    123 Fitness Street, Gym City<br>
                    Phone: (123) 456-7890<br>
                    Email: info@fitcoreelite.com<br>
                    Website: www.fitcoreelite.com
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <strong>Invoice #:</strong> {{ $billing->invoice_number }}<br>
                    <strong>Invoice Date:</strong> {{ $billing->created_at->format('F d, Y') }}<br>
                    <strong>Payment Date:</strong> {{ $billing->payment_date->format('F d, Y') }}<br>
                    @if($billing->due_date)
                    <strong>Due Date:</strong> {{ $billing->due_date->format('F d, Y') }}<br>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bill To Section -->
    <div class="bill-to-section">
        <div class="bill-to-left">
            <div class="section-title">Bill To:</div>
            <div class="bill-to-info">
                <strong>{{ $billing->user->name ?? 'N/A' }}</strong><br>
                {{ $billing->user->email ?? '' }}<br>
                {{ $billing->user->phone ?? '' }}<br>
                {{ $billing->user->address ?? '' }}
            </div>
        </div>
        <div class="bill-to-right">
            <div class="section-title">Payment Status:</div>
            <span class="status-badge status-{{ $billing->payment_status }}">{{ strtoupper($billing->payment_status) }}</span>
            @if($billing->is_overdue)
            <span class="status-badge status-overdue">OVERDUE</span>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="description">Description</th>
                <th class="type">Type</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @if($billing->items->count())
                @foreach($billing->items as $item)
                <tr>
                    <td class="description">
                        <div class="item-title">{{ $item->description }}</div>
                        @if($item->membership && $item->membership->membershipPlan)
                            <div class="item-detail">
                                Plan: {{ $item->membership->membershipPlan->name }} ({{ $item->membership->membershipPlan->duration_label }})<br>
                                Valid: {{ $item->membership->start_date->format('M d, Y') }} - {{ $item->membership->end_date->format('M d, Y') }}
                            </div>
                        @endif
                        @if($item->availedService && $item->availedService->gymService)
                            <div class="item-detail">
                                Service: {{ $item->availedService->gymService->name }}
                            </div>
                        @endif
                    </td>
                    <td class="type">
                        <span class="type-badge type-{{ $item->item_type }}">{{ ucfirst($item->item_type) }}</span>
                    </td>
                    <td class="amount">₱{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td class="description">
                        <div class="item-title">{{ $billing->description ?? 'N/A' }}</div>
                        @if($billing->membership && $billing->membership->membershipPlan)
                            <div class="item-detail">
                                Plan: {{ $billing->membership->membershipPlan->name }} ({{ $billing->membership->membershipPlan->duration_label }})<br>
                                Valid: {{ $billing->membership->start_date->format('M d, Y') }} - {{ $billing->membership->end_date->format('M d, Y') }}
                            </div>
                        @endif
                        @if($billing->availedService && $billing->availedService->gymService)
                            <div class="item-detail">
                                Service: {{ $billing->availedService->gymService->name }}
                            </div>
                        @endif
                    </td>
                    <td class="type">
                        <span class="type-badge type-{{ $billing->type }}">{{ ucfirst($billing->type) }}</span>
                    </td>
                    <td class="amount">{{ $billing->formatted_subtotal }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Totals Section -->
    <div class="totals-section">
        <div class="totals-left">
            @if($billing->notes)
            <div class="notes-box">
                <h6>Notes:</h6>
                <p>{{ $billing->notes }}</p>
            </div>
            @endif
        </div>
        <div class="totals-right">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td>{{ $billing->formatted_subtotal }}</td>
                </tr>
                @if($billing->discount_percentage > 0)
                <tr class="discount">
                    <td>Discount ({{ $billing->discount_percentage }}%):</td>
                    <td>-{{ $billing->formatted_discount }}</td>
                </tr>
                @endif
                @if($billing->tax_rate > 0)
                <tr>
                    <td>Tax ({{ $billing->tax_rate }}%):</td>
                    <td>+{{ $billing->formatted_tax }}</td>
                </tr>
                @endif
                <tr class="total">
                    <td>TOTAL AMOUNT:</td>
                    <td>{{ $billing->formatted_amount }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="payment-info">
        <h6>Payment Information:</h6>
        <p><strong>Method:</strong> {{ $billing->payment_method_label }}</p>
        <p><strong>Status:</strong> {{ strtoupper($billing->payment_status) }}</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p class="thank-you">Thank you for your business!</p>
        <p>This is a computer-generated invoice and does not require a signature.</p>
        <p>For any questions regarding this invoice, please contact us at info@fitcoreelite.com</p>
        <p style="margin-top:8px;">FitCore Elite &copy; {{ date('Y') }} | Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
