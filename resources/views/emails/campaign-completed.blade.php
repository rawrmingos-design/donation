@extends('emails.layout')

@section('content')
<h2 style="color: #333; margin-bottom: 20px;">🎊 Selamat! Target Kampanye Tercapai!</h2>

<p>Halo <strong>{{ $campaign->user->name }}</strong>,</p>

<p>Kami dengan bangga mengumumkan bahwa kampanye Anda <strong>"{{ $campaign->title }}"</strong> telah berhasil mencapai 100% dari target yang ditetapkan!</p>

<div style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; border-radius: 10px; text-align: center; margin: 30px 0;">
    <h2 style="margin: 0 0 10px 0; font-size: 32px;">🏆 TARGET TERCAPAI!</h2>
    <p style="margin: 0; font-size: 18px; opacity: 0.9;">Kampanye Anda telah sukses 100%</p>
</div>

<div class="campaign-info">
    <h3 style="margin-top: 0; color: #667eea;">{{ $campaign->title }}</h3>
    <p style="color: #666; margin-bottom: 15px;">{{ $campaign->short_desc }}</p>
    
    <div style="margin: 20px 0;">
        <div class="progress-bar">
            <div class="progress-fill" style="width: 100%"></div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 16px; color: #333;">
            <span><strong>Rp {{ number_format($campaign->collected_amount, 0, ',', '.') }}</strong> terkumpul</span>
            <span>Target: <strong>Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}</strong></span>
        </div>
    </div>
</div>

<div class="highlight-box">
    <h3 style="margin-top: 0; color: #667eea;">📊 Ringkasan Kampanye</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #eee;"><strong>Total Dana Terkumpul:</strong></td>
            <td style="padding: 10px 0; border-bottom: 1px solid #eee; text-align: right;">
                <span style="color: #28a745; font-weight: bold; font-size: 18px;">Rp {{ number_format($campaign->collected_amount, 0, ',', '.') }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #eee;"><strong>Total Donatur:</strong></td>
            <td style="padding: 10px 0; border-bottom: 1px solid #eee; text-align: right;">
                <span style="color: #667eea; font-weight: bold;">{{ $campaign->donors_count }} orang</span>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #eee;"><strong>Rata-rata Donasi:</strong></td>
            <td style="padding: 10px 0; border-bottom: 1px solid #eee; text-align: right;">
                Rp {{ number_format($campaign->donors_count > 0 ? $campaign->collected_amount / $campaign->donors_count : 0, 0, ',', '.') }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #eee;"><strong>Durasi Kampanye:</strong></td>
            <td style="padding: 10px 0; border-bottom: 1px solid #eee; text-align: right;">
                {{ $campaign->created_at->diffInDays(now()) }} hari
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0;"><strong>Status:</strong></td>
            <td style="padding: 10px 0; text-align: right;">
                <span style="background-color: #28a745; color: white; padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: bold;">SELESAI</span>
            </td>
        </tr>
    </table>
</div>

<div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; margin: 25px 0;">
    <h3 style="margin: 0 0 10px 0; color: #155724;">🎯 Apa Selanjutnya?</h3>
    <ul style="margin: 10px 0; padding-left: 20px; color: #155724;">
        <li style="margin-bottom: 8px;"><strong>Update Donatur:</strong> Bagikan kabar sukses ini kepada para donatur</li>
        <li style="margin-bottom: 8px;"><strong>Dokumentasi:</strong> Upload foto/video penggunaan dana untuk transparansi</li>
        <li style="margin-bottom: 8px;"><strong>Laporan Akhir:</strong> Buat laporan penggunaan dana yang detail</li>
        <li style="margin-bottom: 8px;"><strong>Penarikan Dana:</strong> Ajukan penarikan dana melalui dashboard</li>
    </ul>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ url('/campaigns/' . $campaign->slug) }}" class="btn" style="margin-right: 10px;">
        Lihat Kampanye
    </a>
    <a href="{{ url('/dashboard/campaigns') }}" class="btn" style="background: linear-gradient(135deg, #28a745, #20c997);">
        Kelola Kampanye
    </a>
</div>

<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; margin: 25px 0;">
    <h3 style="margin: 0 0 10px 0; color: #856404;">💝 Pesan dari Tim {{ config('app.name') }}</h3>
    <p style="margin: 0; color: #856404; font-style: italic;">
        "Terima kasih telah mempercayai platform kami untuk mewujudkan misi kebaikan Anda. 
        Kesuksesan kampanye ini adalah bukti nyata bahwa kebaikan masih ada dan bisa menyebar luas. 
        Kami bangga menjadi bagian dari perjalanan mulia ini!"
    </p>
</div>

<h3 style="color: #333; margin-top: 30px;">🙏 Ucapan Terima Kasih</h3>
<p>Pencapaian luar biasa ini tidak akan mungkin terjadi tanpa:</p>
<ul style="color: #666; line-height: 1.8;">
    <li><strong>{{ $campaign->donors_count }} donatur</strong> yang telah mempercayai kampanye Anda</li>
    <li><strong>Dedikasi Anda</strong> dalam mengelola kampanye dengan baik</li>
    <li><strong>Transparansi</strong> yang Anda jaga sepanjang kampanye</li>
    <li><strong>Komunitas</strong> yang mendukung penyebaran kampanye ini</li>
</ul>

<p style="margin-top: 30px;">
    Sekali lagi, selamat atas pencapaian yang luar biasa ini! Semoga dana yang terkumpul dapat memberikan manfaat yang maksimal sesuai dengan tujuan kampanye.
</p>

<p style="margin-top: 30px;">
    Dengan penuh rasa bangga,<br>
    <strong>Tim {{ config('app.name') }}</strong>
</p>

<div style="text-align: center; margin: 40px 0; padding: 20px; background-color: #f8f9fa; border-radius: 8px;">
    <p style="margin: 0; color: #6c757d; font-size: 14px;">
        <strong>Ingin membuat kampanye lagi?</strong><br>
        Kami siap membantu Anda mewujudkan misi kebaikan berikutnya!
    </p>
    <a href="{{ url('/campaigns/create') }}" style="display: inline-block; margin-top: 10px; padding: 8px 20px; background-color: #667eea; color: white; text-decoration: none; border-radius: 20px; font-size: 14px;">
        Buat Kampanye Baru
    </a>
</div>
@endsection
