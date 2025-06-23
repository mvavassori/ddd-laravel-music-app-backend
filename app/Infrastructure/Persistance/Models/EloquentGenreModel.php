<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentGenreModel extends Model {
    protected $table = 'genres';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'name'
    ];

    public function songs() {
        return $this->hasMany(EloquentSongModel::class);
    }
}