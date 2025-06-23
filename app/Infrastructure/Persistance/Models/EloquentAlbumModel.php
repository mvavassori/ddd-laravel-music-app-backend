<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentAlbumModel extends Model {
    protected $table = 'albums';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'title',
        'image_url',
        'description'
    ];

    // relationships

    public function songs() {
        return $this->hasMany(EloquentSongModel::class); // searches for album_id in the songs table
    }

    public function contributions() {
        return $this->morphMany(EloquentContributionModel::class, 'contributable'); // searches for contributable_id in contributions table when the contributable_type is App\Models\Album
    }
}