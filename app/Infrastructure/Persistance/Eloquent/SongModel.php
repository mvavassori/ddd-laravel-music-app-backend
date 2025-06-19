<?php

namespace App\Infrastructure\Persistance\Eloquent;

use Illuminate\Database\Eloquent\Model;

class SongModel extends Model {
    protected $table = 'songs';

    protected $fillable = [
        'title',
        'genre_id', // different from previous app
        'album_id'
    ];

    // relationships
}