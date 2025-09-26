<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Donation Platform' }}</title>
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
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .highlight-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .campaign-info {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .progress-bar {
            background-color: #e9ecef;
            border-radius: 10px;
            height: 20px;
            margin: 10px 0;
            overflow: hidden;
        }
        .progress-fill {
            background: linear-gradient(90deg, #28a745, #20c997);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-body {
                padding: 20px 15px;
            }
            .email-header {
                padding: 20px 15px;
            }
            .email-footer {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>{{ config('app.name') }}</h1>
            <p>Platform Donasi Terpercaya</p>
        </div>
        
        <div class="email-body">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>Terima kasih telah menjadi bagian dari gerakan kebaikan kami.</p>
            
            <div class="social-links">
                <a href="#" style="color: #667eea;">Website</a>
                <a href="#" style="color: #667eea;">Facebook</a>
                <a href="#" style="color: #667eea;">Instagram</a>
                <a href="#" style="color: #667eea;">Twitter</a>
            </div>
            
            <p style="font-size: 12px; color: #6c757d; margin-top: 20px;">
                Email ini dikirim secara otomatis. Jangan balas email ini.<br>
                Jika Anda memiliki pertanyaan, silakan hubungi tim support kami.
            </p>
        </div>
    </div>
</body>
</html>
