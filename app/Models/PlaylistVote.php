<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaylistVote extends Model
{
    protected $fillable = [
        'user_id',
        'playlist_id',
        'vote_type',
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}