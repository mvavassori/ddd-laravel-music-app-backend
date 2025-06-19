<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentSongModel extends Model {
    protected $table = 'songs';

    protected $fillable = [
        'title',
        'genre_id', // different from previous app
        'album_id'
    ];

    // relationships
}