@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Notifikasi Saya</h1>
        @if($notifications->count() > 0)
            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                    Tandai semua sebagai dibaca
                </button>
            </form>
        @endif
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-4">
            @foreach($notifications as $notification)
                <div class="bg-white rounded-lg shadow p-6 border-l-4 {{ $notification->is_read ? 'border-gray-300' : 'border-blue-500' }}">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $notification->title }}
                                </h3>
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                                    @if($notification->type === 'comeback_reminder')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($notification->type === 'voucher_offered')
                                        bg-green-100 text-green-800
                                    @else
                                        bg-blue-100 text-blue-800
                                    @endif
                                ">
                                    @switch($notification->type)
                                        @case('comeback_reminder')
                                            Pengingat Kembali
                                            @break
                                        @case('voucher_offered')
                                            Voucher Promo
                                            @break
                                        @default
                                            Promosi
                                    @endswitch
                                </span>
                            </div>
                            <p class="text-gray-600 mt-2">{{ $notification->message }}</p>
                            <div class="flex items-center gap-4 mt-4">
                                <p class="text-sm text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                                @if($notification->related_url)
                                    <a href="{{ $notification->related_url }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Lihat Selengkapnya →
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-2 ml-4">
                            @if(!$notification->is_read)
                                <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Tandai sebagai dibaca" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L9 5.414V17a1 1 0 102 0V5.414l6.293 6.293a1 1 0 001.414-1.414l-7-7z"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus" class="text-red-400 hover:text-red-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-gray-50 rounded-lg p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Notifikasi</h3>
            <p class="text-gray-600">Anda akan menerima notifikasi tentang promosi dan pengingat dari kami di sini.</p>
        </div>
    @endif
</div>
@endsection
