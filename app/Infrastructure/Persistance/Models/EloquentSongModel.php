<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentSongModel extends Model {
    protected $table = 'songs';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'genre_id', // different from previous app
        'album_id'
    ];

    // relationships

    public function album() {
        return $this->belongsTo(EloquentAlbumModel::class); // searches for album_id in its own table (songs)
    }

    public function contributions() {
        return $this->morphMany(EloquentContributionModel::class, 'contributable'); // searches for contributable_id in contributions table when the contributable_type is App\Models\Song
    }

    public function plays() {
        return $this->hasMany(EloquentPlayModel::class);
    }

    // many to many relationship. // songa can have (be part of) many playlists and playlists can have many songs.
    public function playlists() {
        return $this->belongsToMany(EloquentPlaylistModel::class, 'playlist_songs')
            ->withTimestamps();
    }

    public function genre() {
        return $this->belongsTo(EloquentGenreModel::class);
    }
}