<?php

namespace App\Infrastructure\Persistance\Eloquent;

use Illuminate\Database\Eloquent\Model;

class AlbumModel extends Model {
    protected $fillable = [
        'title',
        'image_url',
        'description'
    ];

    // relationships //? beware that album doesn't have a genre anymore
}