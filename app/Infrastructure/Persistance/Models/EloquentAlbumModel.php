<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentAlbumModel extends Model {
    protected $fillable = [
        'title',
        'image_url',
        'description'
    ];

    // relationships //? beware that album doesn't have a genre anymore
}