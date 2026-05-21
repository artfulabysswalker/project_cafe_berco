@extends('layouts.app')

@section('title', $playlist->title . ' - Cafe Berco')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('playlists.index') }}" class="text-blue-500 hover:underline mb-4 inline-block">← Kembali ke Playlist</a>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-8">
            <!-- Cover Art -->
            <div>
                @if($playlist->image_url)
                    <img src="{{ $playlist->image_url }}" alt="{{ $playlist->title }}" class="w-full rounded-lg shadow-md">
                @else
                    <div class="w-full aspect-square bg-gradient-to-br from-purple-400 to-pink-400 rounded-lg flex items-center justify-center">
                        <span class="text-6xl">🎵</span>
                    </div>
                @endif
            </div>

            <!-- Details -->
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $playlist->title }}</h1>
                <p class="text-xl text-gray-600 mb-4">🎤 {{ $playlist->artist }}</p>
                
                @if($playlist->description)
                    <p class="text-gray-700 mb-6">{{ $playlist->description }}</p>
                @endif

                <!-- Voting Stats -->
                <div class="bg-gray-100 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-green-500">{{ $playlist->votes()->where('vote_type', 'upvote')->count() }}</div>
                            <div class="text-sm text-gray-600">Suka</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-700">{{ $playlist->votes()->count() }}</div>
                            <div class="text-sm text-gray-600">Total Vote</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-500">{{ $playlist->votes()->where('vote_type', 'downvote')->count() }}</div>
                            <div class="text-sm text-gray-600">Tidak</div>
                        </div>
                    </div>
                </div>

                <!-- Voting Buttons -->
                @if(auth()->check())
                    <div class="flex gap-3 mb-6">
                        @php
                            $userVote = $userVote ?? $playlist->getUserVoteType(auth()->id());
                        @endphp
                        <button class="flex-1 py-3 rounded-lg font-semibold transition text-lg
                            {{ $userVote === 'upvote' ? 'bg-green-500 text-white' : 'bg-gray-200 hover:bg-green-300 text-gray-800' }}"
                            onclick="vote({{ $playlist->id }}, 'upvote', this)">
                            👍 Suka
                        </button>
                        <button class="flex-1 py-3 rounded-lg font-semibold transition text-lg
                            {{ $userVote === 'downvote' ? 'bg-red-500 text-white' : 'bg-gray-200 hover:bg-red-300 text-gray-800' }}"
                            onclick="vote({{ $playlist->id }}, 'downvote', this)">
                            👎 Tidak Suka
                        </button>
                    </div>
                @else
                    <a href="{{ route('testlogin') }}" class="w-full block text-center py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-semibold mb-6">
                        🔐 Login untuk Vote
                    </a>
                @endif

                <!-- Links -->
                @if($playlist->spotify_url)
                    <a href="{{ $playlist->spotify_url }}" target="_blank" class="w-full block text-center py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition mb-3">
                        🎵 Dengarkan di Spotify
                    </a>
                @endif

                @if(auth()->check() && auth()->user()->is_admin)
                    <div class="flex gap-2">
                        <a href="{{ route('admin.playlists.edit', $playlist) }}" class="flex-1 text-center py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                            ✏️ Edit
                        </a>
                        <form action="{{ route('admin.playlists.destroy', $playlist) }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus lagu ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                🗑️ Hapus
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Voters -->
        <div class="border-t bg-gray-50 p-8">
            <h2 class="text-xl font-bold mb-4">📊 Vote Terbaru</h2>
            <div class="space-y-2">
                @forelse($playlist->votes()->latest()->limit(10)->get() as $vote)
                    <div class="flex items-center gap-3 py-2">
                        <span class="text-lg">{{ $vote->vote_type === 'upvote' ? '👍' : '👎' }}</span>
                        <span class="text-gray-700">{{ $vote->user->name }}</span>
                        <span class="text-sm text-gray-500">{{ $vote->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-gray-500">Belum ada vote</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
async function vote(playlistId, voteType, button) {
    @if(!auth()->check())
        window.location.href = '{{ route('testlogin') }}';
        return;
    @endif

    try {
        const response = await fetch(`/playlists/${playlistId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ vote_type: voteType })
        });

        const data = await response.json();

        if (data.success) {
            // Reload page to show updated stats
            location.reload();
        } else {
            alert(data.error || 'Terjadi kesalahan');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat voting');
    }
}
</script>
@endsection
