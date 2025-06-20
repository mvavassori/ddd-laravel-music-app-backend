<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EloquentPlayModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'song_id'
    ];

    public function song() {
        return $this->belongsTo(EloquentSongModel::class);
    }
    public function user() {
        return $this->belongsTo(EloquentUserModel::class);
    }
}
