@extends('layouts.web')

@section('title', 'Voucher Ditukar - Berco Cafe')

@section('content')
<div class="container" style="max-width: 700px; margin: 0 auto; padding: 30px 0;">
    <div style="background: white; border-radius: 16px; box-shadow: 0 14px 40px rgba(0,0,0,0.08); padding: 30px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <i class="fas fa-gift" style="font-size: 46px; color: #bf4f08;"></i>
            <h1 style="margin: 18px 0 8px; font-size: 2rem;">Voucher Berhasil Ditukar</h1>
            <p style="color: #555;">Terima kasih telah menggunakan EXP untuk menukarkan voucher Anda.</p>
        </div>

        <div style="display: grid; gap: 16px;">
            <div style="background: #fdf7e7; border: 1px solid #f4e1b8; border-radius: 14px; padding: 18px;">
                <h2 style="margin: 0 0 10px; font-size: 1.1rem; color: #b45309;">Voucher</h2>
                <p style="margin: 0; font-size: 1.2rem; font-weight: 700; color: #222;">{{ $redemption->reward->name }}</p>
                <p style="margin: 8px 0 0; color: #555;">{{ $redemption->reward->description }}</p>
            </div>

            <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <div style="flex: 1; background: #fff; border: 1px solid #ececec; border-radius: 14px; padding: 18px;">
                    <div style="color: #666; margin-bottom: 8px;">EXP yang Dipakai</div>
                    <div style="font-size: 1.6rem; font-weight: 700; color: #222;">{{ number_format($redemption->exp_used) }} EXP</div>
                </div>
                <div style="flex: 1; background: #fff; border: 1px solid #ececec; border-radius: 14px; padding: 18px;">
                    <div style="color: #666; margin-bottom: 8px;">Diskon</div>
                    <div style="font-size: 1.6rem; font-weight: 700; color: #222;">{{ $redemption->reward->discount_percentage }}%</div>
                </div>
            </div>

            <div style="background: #f0fdf4; border: 1px solid #a7f3d0; border-radius: 14px; padding: 18px;">
                <p style="margin: 0 0 8px; color: #166534; font-weight: 700;">Status Penukaran</p>
                <p style="margin: 0; color: #166534;">{{ ucfirst($redemption->status) }} pada {{ $redemption->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div style="margin-top: 28px; display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('redeem.index') }}" style="flex: 1; text-decoration: none; background: #bf4f08; color: white; padding: 14px 18px; border-radius: 12px; text-align: center; font-weight: 700;">Kembali ke Voucher</a>
            <a href="{{ route('redeem.history') }}" style="flex: 1; text-decoration: none; background: #ffffff; color: #333; border: 1px solid #ddd; padding: 14px 18px; border-radius: 12px; text-align: center;">Lihat Riwayat Penukaran</a>
        </div>
    </div>
</div>
@endsection
