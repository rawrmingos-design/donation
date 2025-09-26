@extends('emails.layout')

@section('content')
<h2 style="color: #333; margin-bottom: 20px;">🎉 Terima Kasih atas Donasi Anda!</h2>

<p>Halo <strong>{{ $donation->donor->name }}</strong>,</p>

<p>Terima kasih telah berdonasi untuk kampanye <strong>"{{ $donation->campaign->title }}"</strong>. Donasi Anda sangat berarti dan akan membantu mencapai tujuan mulia ini.</p>

<div class="highlight-box">
    <h3 style="margin-top: 0; color: #667eea;">Detail Donasi</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Jumlah Donasi:</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">
                <span class="amount">Rp {{ number_format($donation->amount, 0, ',', '.') }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Tanggal:</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">
                {{ $donation->created_at->format('d F Y, H:i') }} WIB
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>ID Donasi:</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;">
                #{{ $donation->id }}
            </td>
        </tr>
        @if($donation->message)
        <tr>
            <td style="padding: 8px 0;"><strong>Pesan:</strong></td>
            <td style="padding: 8px 0; text-align: right; font-style: italic;">
                "{{ $donation->message }}"
            </td>
        </tr>
        @endif
    </table>
</div>

<div class="campaign-info">
    <h3 style="margin-top: 0; color: #333;">Tentang Kampanye</h3>
    <h4 style="color: #667eea; margin: 10px 0;">{{ $donation->campaign->title }}</h4>
    <p style="color: #666; margin-bottom: 15px;">{{ $donation->campaign->short_desc }}</p>
    
    <div style="margin: 15px 0;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span><strong>Progress Kampanye:</strong></span>
            <span><strong>{{ number_format(($donation->campaign->collected_amount / $donation->campaign->target_amount) * 100, 1) }}%</strong></span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ min(100, ($donation->campaign->collected_amount / $donation->campaign->target_amount) * 100) }}%"></div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 5px; font-size: 14px; color: #666;">
            <span>Rp {{ number_format($donation->campaign->collected_amount, 0, ',', '.') }} terkumpul</span>
            <span>Target: Rp {{ number_format($donation->campaign->target_amount, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

@if($donation->message)
<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0;">
    <p style="margin: 0; color: #856404;"><strong>💝 Pesan Anda:</strong></p>
    <p style="margin: 5px 0 0 0; font-style: italic; color: #856404;">"{{ $donation->message }}"</p>
</div>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/campaigns/' . $donation->campaign->slug) }}" class="btn">
        Lihat Kampanye
    </a>
</div>

<div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin: 20px 0;">
    <p style="margin: 0; color: #155724;"><strong>✅ Status Pembayaran:</strong> Berhasil</p>
    <p style="margin: 5px 0 0 0; color: #155724; font-size: 14px;">Donasi Anda telah dikonfirmasi dan akan segera disalurkan untuk kampanye ini.</p>
</div>

<p>Sekali lagi, terima kasih atas kebaikan hati Anda. Dengan donasi ini, Anda telah menjadi bagian dari perubahan positif yang nyata.</p>

<p style="margin-top: 30px;">
    Salam hangat,<br>
    <strong>Tim {{ config('app.name') }}</strong>
</p>
@endsection
