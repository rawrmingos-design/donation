@extends('emails.layout')

@section('content')
<h2 style="color: #333; margin-bottom: 20px;">📊 Laporan Harian Platform</h2>

<p>Halo <strong>Admin</strong>,</p>

<p>Berikut adalah ringkasan aktivitas platform <strong>{{ config('app.name') }}</strong> untuk tanggal <strong>{{ $date }}</strong>:</p>

<div style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; border-radius: 10px; text-align: center; margin: 25px 0;">
    <h2 style="margin: 0 0 10px 0; font-size: 24px;">📈 Ringkasan Hari Ini</h2>
    <p style="margin: 0; opacity: 0.9;">{{ $date }}</p>
</div>

<div class="highlight-box">
    <h3 style="margin-top: 0; color: #667eea;">💰 Statistik Donasi</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong>Total Donasi Hari Ini:</strong></td>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee; text-align: right;">
                <span style="color: #28a745; font-weight: bold; font-size: 18px;">{{ $reportData['donations_today'] }} donasi</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong>Total Dana Terkumpul:</strong></td>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee; text-align: right;">
                <span style="color: #28a745; font-weight: bold; font-size: 18px;">Rp {{ number_format($reportData['total_amount_today'], 0, ',', '.') }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong>Rata-rata Donasi:</strong></td>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee; text-align: right;">
                Rp {{ number_format($reportData['average_donation'], 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0;"><strong>Donasi Berhasil:</strong></td>
            <td style="padding: 12px 0; text-align: right;">
                <span style="color: #28a745; font-weight: bold;">{{ $reportData['successful_donations'] }}</span> / {{ $reportData['donations_today'] }}
                ({{ number_format($reportData['success_rate'], 1) }}%)
            </td>
        </tr>
    </table>
</div>

<div class="highlight-box">
    <h3 style="margin-top: 0; color: #667eea;">🎯 Statistik Kampanye</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong>Kampanye Baru:</strong></td>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee; text-align: right;">
                <span style="color: #667eea; font-weight: bold;">{{ $reportData['new_campaigns'] }} kampanye</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong>Kampanye Selesai:</strong></td>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee; text-align: right;">
                <span style="color: #28a745; font-weight: bold;">{{ $reportData['completed_campaigns'] }} kampanye</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong>Total Kampanye Aktif:</strong></td>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee; text-align: right;">
                {{ $reportData['active_campaigns'] }} kampanye
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0;"><strong>Kampanye Trending:</strong></td>
            <td style="padding: 12px 0; text-align: right;">
                {{ $reportData['trending_campaigns'] }} kampanye
            </td>
        </tr>
    </table>
</div>

<div class="highlight-box">
    <h3 style="margin-top: 0; color: #667eea;">👥 Statistik Pengguna</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong>Pengguna Baru:</strong></td>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee; text-align: right;">
                <span style="color: #667eea; font-weight: bold;">{{ $reportData['new_users'] }} pengguna</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong>Donatur Aktif:</strong></td>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee; text-align: right;">
                {{ $reportData['active_donors'] }} donatur
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong>Creator Aktif:</strong></td>
            <td style="padding: 12px 0; border-bottom: 1px solid #eee; text-align: right;">
                {{ $reportData['active_creators'] }} creator
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0;"><strong>Total Pengguna:</strong></td>
            <td style="padding: 12px 0; text-align: right;">
                {{ $reportData['total_users'] }} pengguna
            </td>
        </tr>
    </table>
</div>

@if($reportData['top_campaigns'])
<div class="campaign-info">
    <h3 style="margin-top: 0; color: #667eea;">🏆 Top 5 Kampanye Hari Ini</h3>
    @foreach($reportData['top_campaigns'] as $index => $campaign)
    <div style="padding: 15px; margin: 10px 0; border: 1px solid #e9ecef; border-radius: 8px; background-color: #f8f9fa;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h4 style="margin: 0 0 5px 0; color: #333; font-size: 16px;">{{ $index + 1 }}. {{ $campaign->title }}</h4>
                <p style="margin: 0; color: #666; font-size: 14px;">oleh {{ $campaign->user->name }}</p>
            </div>
            <div style="text-align: right;">
                <div style="color: #28a745; font-weight: bold; font-size: 16px;">
                    Rp {{ number_format($campaign->collected_amount, 0, ',', '.') }}
                </div>
                <div style="color: #666; font-size: 12px;">
                    {{ $campaign->donors_count }} donatur
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@if($reportData['recent_issues'] && count($reportData['recent_issues']) > 0)
<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 25px 0;">
    <h3 style="margin: 0 0 15px 0; color: #856404;">⚠️ Perhatian Khusus</h3>
    <ul style="margin: 0; padding-left: 20px; color: #856404;">
        @foreach($reportData['recent_issues'] as $issue)
        <li style="margin-bottom: 8px;">{{ $issue }}</li>
        @endforeach
    </ul>
</div>
@endif

<div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; margin: 25px 0;">
    <h3 style="margin: 0 0 10px 0; color: #155724;">📈 Perbandingan dengan Kemarin</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; color: #155724;"><strong>Donasi:</strong></td>
            <td style="padding: 8px 0; text-align: right; color: #155724;">
                @if($reportData['donation_growth'] > 0)
                    <span style="color: #28a745;">↗️ +{{ number_format($reportData['donation_growth'], 1) }}%</span>
                @elseif($reportData['donation_growth'] < 0)
                    <span style="color: #dc3545;">↘️ {{ number_format($reportData['donation_growth'], 1) }}%</span>
                @else
                    <span style="color: #6c757d;">→ 0%</span>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; color: #155724;"><strong>Pengguna Baru:</strong></td>
            <td style="padding: 8px 0; text-align: right; color: #155724;">
                @if($reportData['user_growth'] > 0)
                    <span style="color: #28a745;">↗️ +{{ number_format($reportData['user_growth'], 1) }}%</span>
                @elseif($reportData['user_growth'] < 0)
                    <span style="color: #dc3545;">↘️ {{ number_format($reportData['user_growth'], 1) }}%</span>
                @else
                    <span style="color: #6c757d;">→ 0%</span>
                @endif
            </td>
        </tr>
    </table>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/admin') }}" class="btn">
        Lihat Dashboard Admin
    </a>
</div>

<p>Laporan ini digenerate secara otomatis setiap hari untuk membantu monitoring platform. Jika ada pertanyaan atau memerlukan data lebih detail, silakan akses dashboard admin.</p>

<p style="margin-top: 30px;">
    Salam,<br>
    <strong>Sistem {{ config('app.name') }}</strong>
</p>

<div style="text-align: center; margin: 40px 0; padding: 20px; background-color: #f8f9fa; border-radius: 8px;">
    <p style="margin: 0; color: #6c757d; font-size: 12px;">
        Laporan ini dikirim secara otomatis setiap hari pukul 08:00 WIB<br>
        Untuk mengubah pengaturan notifikasi, silakan akses panel admin.
    </p>
</div>
@endsection
