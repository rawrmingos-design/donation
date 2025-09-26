<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['title'] }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
        }
        .message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #4b5563;
        }
        .details {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #374151;
        }
        .detail-value {
            color: #1f2937;
        }
        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .button:hover {
            background: #1d4ed8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .notes {
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            color: #991b1b;
        }
        .success { color: #059669; }
        .danger { color: #dc2626; }
        .warning { color: #d97706; }
        .info { color: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">💝 DonationPlatform</div>
            <div class="icon">{{ $data['icon'] }}</div>
            <div class="title {{ $data['color'] }}">{{ $data['title'] }}</div>
        </div>

        <div class="message">
            {{ $data['message'] }}
        </div>

        <div class="details">
            <div class="detail-row">
                <span class="detail-label">ID Penarikan:</span>
                <span class="detail-value">#{{ $withdrawal->id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Kampanye:</span>
                <span class="detail-value">{{ $withdrawal->campaign->title }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Jumlah:</span>
                <span class="detail-value amount">{{ $data['formatted_amount'] }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value {{ $data['color'] }}">{{ ucfirst($withdrawal->status) }}</span>
            </div>
            @if(isset($data['reference_number']) && $data['reference_number'])
            <div class="detail-row">
                <span class="detail-label">Nomor Referensi:</span>
                <span class="detail-value">{{ $data['reference_number'] }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Tanggal:</span>
                <span class="detail-value">{{ $withdrawal->updated_at->format('d F Y, H:i') }}</span>
            </div>
        </div>

        @if(isset($data['notes']) && $data['notes'])
        <div class="notes">
            <strong>Catatan:</strong><br>
            {{ $data['notes'] }}
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ config('app.url') . $data['action_url'] }}" class="button">
                Lihat Detail Penarikan
            </a>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem DonationPlatform.</p>
            <p>Jika Anda memiliki pertanyaan, silakan hubungi tim support kami.</p>
            <p>&copy; {{ date('Y') }} DonationPlatform. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
