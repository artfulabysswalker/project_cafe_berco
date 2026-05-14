@extends('layouts.web')

@section('title', 'Riwayat Penukaran - Berco Cafe')

@section('content')
<div class="container" style="max-width: 1000px; margin: 0 auto; padding: 30px 0;">
    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 2rem; color: #333; margin-bottom: 8px;">Riwayat Penukaran Voucher</h1>
            <p style="color: #555;">Semua voucher yang sudah Anda tukarkan dengan EXP.</p>
        </div>
        <a href="{{ route('redeem.index') }}" style="text-decoration: none; background: #bf4f08; color: white; padding: 12px 18px; border-radius: 12px;">Kembali ke Voucher</a>
    </div>

    @if($redemptions->isEmpty())
        <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 12px 30px rgba(0,0,0,0.05); text-align: center; color: #555;">
            <p style="margin: 0;">Belum ada penukaran voucher. Kumpulkan EXP dan lakukan penukaran.</p>
        </div>
    @else
        <div style="background: white; border-radius: 16px; box-shadow: 0 12px 30px rgba(0,0,0,0.05); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f9fafb; color: #555;">
                    <tr>
                        <th style="padding: 16px; text-align: left;">Voucher</th>
                        <th style="padding: 16px; text-align: left;">EXP Digunakan</th>
                        <th style="padding: 16px; text-align: left;">Diskon</th>
                        <th style="padding: 16px; text-align: left;">Tanggal</th>
                        <th style="padding: 16px; text-align: left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($redemptions as $item)
                        <tr style="border-top: 1px solid #eee;">
                            <td style="padding: 16px;">{{ $item->reward->name }}</td>
                            <td style="padding: 16px;">{{ number_format($item->exp_used) }} EXP</td>
                            <td style="padding: 16px;">{{ $item->reward->discount_percentage }}%</td>
                            <td style="padding: 16px;">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td style="padding: 16px; color: #065f46; font-weight: 700;">{{ ucfirst($item->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
