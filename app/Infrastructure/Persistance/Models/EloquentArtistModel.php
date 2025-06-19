<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentArtistModel extends Model {
    protected $fillable = [
        'name',
        'bio',
        'image_url',
    ];
}