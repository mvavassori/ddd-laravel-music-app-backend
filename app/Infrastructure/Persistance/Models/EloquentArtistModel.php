<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentArtistModel extends Model {
    protected $table = 'artists';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'bio',
        'image_url',
    ];

    public function contributions(){
        return $this->hasMany(EloquentContributionModel::class);
    }
}