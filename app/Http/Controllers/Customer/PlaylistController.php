<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\PlaylistVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource (Active playlists)
     */
    public function index()
    {
        $playlists = Playlist::where('status', 'active')
            ->orderByRaw('(SELECT COUNT(*) FROM playlist_votes WHERE playlist_id = playlists.id AND vote_type = "upvote") - (SELECT COUNT(*) FROM playlist_votes WHERE playlist_id = playlists.id AND vote_type = "downvote") DESC')
            ->get();

        return view('playlists.index', compact('playlists'));
    }

    /**
     * Show the form for creating a new resource (Admin Only)
     */
    public function create()
    {
        return view('playlists.create');
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'description' => 'nullable|string',
            'spotify_url' => 'nullable|url',
            'image_url' => 'nullable|url',
        ]);

        Playlist::create($validated);

        return redirect()->route('playlists.index')
            ->with('success', 'Lagu berhasil ditambahkan ke playlist!');
    }

    /**
     * Display the specified resource
     */
    public function show(Playlist $playlist)
    {
        $userVote = null;
        if (Auth::check()) {
            $userVote = $playlist->getUserVoteType(Auth::id());
        }

        return view('playlists.show', compact('playlist', 'userVote'));
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(Playlist $playlist)
    {
        return view('playlists.edit', compact('playlist'));
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, Playlist $playlist)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'description' => 'nullable|string',
            'spotify_url' => 'nullable|url',
            'image_url' => 'nullable|url',
            'status' => 'in:active,inactive,completed',
        ]);

        $playlist->update($validated);

        return redirect()->route('playlists.show', $playlist)
            ->with('success', 'Lagu berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(Playlist $playlist)
    {
        $playlist->delete();

        return redirect()->route('playlists.index')
            ->with('success', 'Lagu berhasil dihapus dari playlist!');
    }

    /**
     * User vote untuk lagu (Upvote/Downvote)
     */
    public function vote(Request $request, Playlist $playlist)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Harus login untuk memberikan vote'], 401);
        }

        $validated = $request->validate([
            'vote_type' => 'required|in:upvote,downvote',
        ]);

        $userId = Auth::id();

        // Check apakah user sudah pernah vote lagu ini
        $existingVote = PlaylistVote::where('user_id', $userId)
            ->where('playlist_id', $playlist->id)
            ->first();

        if ($existingVote) {
            // Jika vote type sama, hapus vote (toggle)
            if ($existingVote->vote_type === $validated['vote_type']) {
                $existingVote->delete();
                $message = 'Vote berhasil dibatalkan!';
                $voted = false;
            } else {
                // Jika berbeda, ubah vote type
                $existingVote->update(['vote_type' => $validated['vote_type']]);
                $message = 'Vote berhasil diubah!';
                $voted = true;
            }
        } else {
            // Tambah vote baru
            PlaylistVote::create([
                'user_id' => $userId,
                'playlist_id' => $playlist->id,
                'vote_type' => $validated['vote_type'],
            ]);
            $message = 'Terima kasih sudah memberikan vote!';
            $voted = true;
        }

        // Update vote count di playlist
        $playlist->update([
            'vote_count' => $playlist->votes()->count()
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'voted' => $voted,
                'upvotes' => $playlist->votes()->where('vote_type', 'upvote')->count(),
                'downvotes' => $playlist->votes()->where('vote_type', 'downvote')->count(),
                'userVote' => $playlist->getUserVoteType($userId),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get top voted playlists (API)
     */
    public function topVoted()
    {
        $topPlaylists = Playlist::where('status', 'active')
            ->withCount(['votes' => function ($query) {
                $query->where('vote_type', 'upvote');
            }])
            ->orderByDesc('votes_count')
            ->limit(10)
            ->get();

        return response()->json($topPlaylists);
    }

    /**
     * Get current playing playlist
     */
    public function currentPlaying()
    {
        $currentPlaylist = Playlist::where('status', 'active')
            ->orderByDesc('updated_at')
            ->first();

        return response()->json($currentPlaylist);
    }
}
