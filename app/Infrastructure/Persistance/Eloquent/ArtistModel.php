<?php

namespace App\Infrastructure\Persistance;

use Illuminate\Database\Eloquent\Model;

class ArtistModel extends Model {
    protected $fillable = [
        'name',
        'bio',
        'image_url',
    ];
}