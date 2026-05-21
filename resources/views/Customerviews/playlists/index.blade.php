@extends('layouts.app')

@section('title', 'Playlist Voting - Cafe Berco')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">🎵 Playlist Voting</h1>
        @if(auth()->check() && auth()->user()->is_admin)
            <a href="{{ route('admin.playlists.create') }}" class="btn btn-primary">+ Tambah Lagu</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($playlists->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">Tidak ada lagu di playlist saat ini</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($playlists as $playlist)
                <div class="card bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition">
                    @if($playlist->image_url)
                        <img src="{{ $playlist->image_url }}" alt="{{ $playlist->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center">
                            <span class="text-4xl">🎵</span>
                        </div>
                    @endif

                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2 line-clamp-2">{{ $playlist->title }}</h2>
                        <p class="text-gray-600 mb-2">{{ $playlist->artist }}</p>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $playlist->description }}</p>

                        <div class="flex items-center justify-between mb-4">
                            <div class="flex gap-4">
                                <span class="text-sm font-semibold">
                                    👍 <span class="upvote-count">{{ $playlist->votes()->where('vote_type', 'upvote')->count() }}</span>
                                </span>
                                <span class="text-sm font-semibold">
                                    👎 <span class="downvote-count">{{ $playlist->votes()->where('vote_type', 'downvote')->count() }}</span>
                                </span>
                            </div>
                        </div>

                        @if(auth()->check())
                            <div class="flex gap-2">
                                @php
                                    $userVote = $playlist->getUserVoteType(auth()->id());
                                @endphp
                                <button class="vote-btn flex-1 py-2 rounded transition text-sm font-semibold
                                    {{ $userVote === 'upvote' ? 'bg-green-500 text-white' : 'bg-gray-200 hover:bg-green-300' }}"
                                    onclick="vote({{ $playlist->id }}, 'upvote', this)">
                                    👍 Suka
                                </button>
                                <button class="vote-btn flex-1 py-2 rounded transition text-sm font-semibold
                                    {{ $userVote === 'downvote' ? 'bg-red-500 text-white' : 'bg-gray-200 hover:bg-red-300' }}"
                                    onclick="vote({{ $playlist->id }}, 'downvote', this)">
                                    👎 Tidak
                                </button>
                            </div>
                        @else
                            <a href="{{ route('testlogin') }}" class="w-full block text-center py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                Login untuk Vote
                            </a>
                        @endif

                        @if(auth()->check() && auth()->user()->is_admin)
                            <div class="mt-2 flex gap-1">
                                <a href="{{ route('admin.playlists.edit', $playlist) }}" class="flex-1 text-center text-xs py-1 bg-yellow-400 rounded hover:bg-yellow-500 transition">Edit</a>
                                <form action="{{ route('admin.playlists.destroy', $playlist) }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus lagu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-xs py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">Hapus</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
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
            // Update vote counts
            const card = button.closest('.card');
            const upvoteCount = card.querySelector('.upvote-count');
            const downvoteCount = card.querySelector('.downvote-count');
            const upvoteBtn = card.querySelectorAll('.vote-btn')[0];
            const downvoteBtn = card.querySelectorAll('.vote-btn')[1];

            upvoteCount.textContent = data.upvotes;
            downvoteCount.textContent = data.downvotes;

            // Reset button styles
            upvoteBtn.classList.remove('bg-green-500', 'text-white');
            downvoteBtn.classList.remove('bg-red-500', 'text-white');
            upvoteBtn.classList.add('bg-gray-200');
            downvoteBtn.classList.add('bg-gray-200');

            // Apply new style based on user vote
            if (data.userVote === 'upvote') {
                upvoteBtn.classList.remove('bg-gray-200');
                upvoteBtn.classList.add('bg-green-500', 'text-white');
            } else if (data.userVote === 'downvote') {
                downvoteBtn.classList.remove('bg-gray-200');
                downvoteBtn.classList.add('bg-red-500', 'text-white');
            }

            // Show notification
            showNotification(data.message, 'success');
        } else {
            showNotification(data.error || 'Terjadi kesalahan', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat voting', 'error');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-3 rounded text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } shadow-lg`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
