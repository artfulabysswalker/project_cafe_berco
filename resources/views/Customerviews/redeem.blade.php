@extends('Customerviews.layouts.web')

@section('title', 'Tukar Voucher - Berco Cafe')

@section('content')
<div class="container" style="max-width: 1100px; margin: 0 auto; padding: 20px 0;">
    <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 16px; flex-wrap: wrap; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 2rem; color: #333; margin-bottom: 10px;">Tukar EXP dengan Voucher</h1>
            <p style="color: #555;">Pilih voucher diskon yang ingin Anda tukarkan menggunakan EXP yang sudah dikumpulkan.</p>
        </div>

        <div style="background: #fff7e0; border: 1px solid #f1d9b5; padding: 16px 20px; border-radius: 12px; min-width: 240px;">
            <h2 style="margin: 0 0 10px; font-size: 1rem; color: #bf4f08;">EXP Anda</h2>
            <p style="margin: 0; font-size: 2rem; font-weight: bold;">{{ auth()->user()->exp }}</p>
            <p style="margin: 6px 0 0; color: #666; font-size: 0.95rem;">EXP akan dikurangi saat penukaran.</p>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 18px; border-radius: 10px; margin-bottom: 18px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; padding: 14px 18px; border-radius: 10px; margin-bottom: 18px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
        @forelse($rewards as $reward)
            <div style="background: white; border: 1px solid #ececec; border-radius: 16px; padding: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 18px;">
                    <div>
                        <h2 style="margin: 0 0 8px; font-size: 1.2rem; color: #222;">{{ $reward->name }}</h2>
                        <p style="margin: 0; color: #666; font-size: 0.95rem;">{{ $reward->description }}</p>
                    </div>
                    <span style="background: #fff0c7; color: #b45309; padding: 8px 12px; border-radius: 999px; font-weight: bold; font-size: 0.9rem;">{{ $reward->discount_percentage }}% OFF</span>
                </div>

                <div style="margin-bottom: 20px; color: #555;">
                    <p style="margin: 0 0 6px;">Biaya EXP: <strong>{{ number_format($reward->exp_cost) }} EXP</strong></p>
                    <p style="margin: 0;">Status: <strong>{{ $reward->available ? 'Tersedia' : 'Habis' }}</strong></p>
                </div>

                <form method="POST" action="{{ route('redeem.redeem', $reward) }}">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 12px 18px; background: {{ $reward->available ? '#bf4f08' : '#ccc' }}; color: white; border: none; border-radius: 12px; cursor: {{ $reward->available ? 'pointer' : 'not-allowed' }}; font-weight: bold;" {{ $reward->available ? '' : 'disabled' }}>
                        {{ $reward->available ? 'Tukar Sekarang' : 'Voucher Habis' }}
                    </button>
                </form>
            </div>
        @empty
            <div style="grid-column: 1 / -1; padding: 30px; background: white; border: 1px solid #ececec; border-radius: 16px; text-align: center;">
                <p style="margin: 0; color: #555;">Belum ada voucher tersedia saat ini. Coba lagi nanti.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px; display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route('menu.index') }}" style="text-decoration: none; padding: 12px 20px; background: #ffffff; color: #333; border: 1px solid #ddd; border-radius: 12px;">Kembali ke Menu</a>
        <a href="{{ route('order.history') }}" style="text-decoration: none; padding: 12px 20px; background: #bf4f08; color: white; border-radius: 12px;">Lihat Riwayat Pesanan</a>
    </div>
</div>
@endsection
