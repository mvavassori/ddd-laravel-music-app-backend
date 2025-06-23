<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentPlaylistModel extends Model {
    protected $table = 'playlists';
    protected $fillable = [
        'id',
        'name',
        'description',
        'type',
        'user_id'
    ];

    public $incrementing = false;

    const TYPES = [
        'custom' => 'Custom',
        'daily_mix' => 'Daily Mix',
    ];

    // a playlist belongs to one user only
    public function user() {
        return $this->belongsTo(EloquentUserModel::class);
    }

    // many to many relationship. // songs can have (be part of) many playlists and playlists can have many songs.
    public function songs() {
        return $this->belongsToMany(EloquentSongModel::class, 'playlist_songs')
            ->orderBy('pivot_created_at') // Order by when song was added to playlist
            ->withTimestamps();
    }
}