<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = [
        'title',
        'artist',
        'description',
        'spotify_url',
        'image_url',
        'status',
        'vote_count',
    ];

    public function votes()
    {
        return $this->hasMany(PlaylistVote::class);
    }

    public function getUserVoteType($userId)
    {
        return $this->votes()
            ->where('user_id', $userId)
            ->value('vote_type');
    }
}