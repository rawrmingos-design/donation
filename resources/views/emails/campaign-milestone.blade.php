@extends('emails.layout')

@section('content')
<h2 style="color: #333; margin-bottom: 20px;">🎯 Milestone Tercapai!</h2>

<p>Halo <strong>{{ $campaign->user->name }}</strong>,</p>

<p>Kabar gembira! Kampanye Anda <strong>"{{ $campaign->title }}"</strong> telah mencapai milestone <strong>{{ $milestone }}%</strong> dari target yang ditetapkan.</p>

<div class="campaign-info">
    <h3 style="margin-top: 0; color: #667eea;">{{ $campaign->title }}</h3>
    <p style="color: #666; margin-bottom: 15px;">{{ $campaign->short_desc }}</p>
    
    <div style="margin: 20px 0;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span><strong>Progress Saat Ini:</strong></span>
            <span style="color: #28a745; font-size: 18px; font-weight: bold;">{{ number_format(($campaign->collected_amount / $campaign->target_amount) * 100, 1) }}%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ min(100, ($campaign->collected_amount / $campaign->target_amount) * 100) }}%"></div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 14px; color: #666;">
            <span><strong>Rp {{ number_format($campaign->collected_amount, 0, ',', '.') }}</strong> terkumpul</span>
            <span>Target: <strong>Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</strong></span>
        </div>
    </div>
</div>

<div class="highlight-box">
    <h3 style="margin-top: 0; color: #667eea;">📊 Statistik Kampanye</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Total Donatur:</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">
                <span style="color: #28a745; font-weight: bold;">{{ $campaign->donors_count }} orang</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Rata-rata Donasi:</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">
                Rp {{ number_format($campaign->donors_count > 0 ? $campaign->collected_amount / $campaign->donors_count : 0, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Sisa Target:</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">
                Rp {{ number_format($campaign->target_amount - $campaign->collected_amount, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0;"><strong>Waktu Tersisa:</strong></td>
            <td style="padding: 8px 0; text-align: right;">
                @if($campaign->deadline)
                    {{ $campaign->deadline->diffForHumans() }}
                @else
                    Tidak terbatas
                @endif
            </td>
        </tr>
    </table>
</div>

@if($milestone == 50)
<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0;">
    <p style="margin: 0; color: #856404;"><strong>🎉 Selamat!</strong></p>
    <p style="margin: 5px 0 0 0; color: #856404;">Anda telah mencapai setengah dari target kampanye. Momentum yang luar biasa!</p>
</div>
@elseif($milestone == 75)
<div style="background-color: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; padding: 15px; margin: 20px 0;">
    <p style="margin: 0; color: #0c5460;"><strong>🚀 Hampir Sampai!</strong></p>
    <p style="margin: 5px 0 0 0; color: #0c5460;">Tinggal sedikit lagi untuk mencapai target. Terus semangat!</p>
</div>
@elseif($milestone == 100)
<div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin: 20px 0;">
    <p style="margin: 0; color: #155724;"><strong>🎊 TARGET TERCAPAI!</strong></p>
    <p style="margin: 5px 0 0 0; color: #155724;">Selamat! Kampanye Anda telah mencapai 100% dari target yang ditetapkan!</p>
</div>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/campaigns/' . $campaign->slug) }}" class="btn">
        Lihat Kampanye
    </a>
</div>

<h3 style="color: #333;">💡 Tips untuk Meningkatkan Donasi:</h3>
<ul style="color: #666; line-height: 1.8;">
    <li><strong>Update Kampanye:</strong> Bagikan progress dan ceritakan dampak yang telah dicapai</li>
    <li><strong>Share di Media Sosial:</strong> Ajak teman dan keluarga untuk ikut berdonasi</li>
    <li><strong>Terima Kasih Donatur:</strong> Berikan apresiasi kepada para donatur</li>
    <li><strong>Dokumentasi:</strong> Upload foto atau video terkait penggunaan dana</li>
</ul>

<p>Terima kasih telah mempercayai platform kami untuk menyalurkan kebaikan. Terus semangat dalam mencapai tujuan mulia Anda!</p>

<p style="margin-top: 30px;">
    Salam sukses,<br>
    <strong>Tim {{ config('app.name') }}</strong>
</p>
@endsection
