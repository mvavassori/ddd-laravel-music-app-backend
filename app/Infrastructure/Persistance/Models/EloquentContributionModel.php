<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentContributionModel extends Model {
    protected $table = 'contributions';
    protected $fillable = ['artist_id', 'role_id', 'contribution_type', 'contribution_id'];


    public function role() {
        return $this->belongsTo(EloquentRoleModel::class); // searches for role_id in its own table
    }

    public function artist() {
        return $this->belongsTo(EloquentArtistModel::class); // searches for artist_id in its own table
    }

    public function contributable() {
        return $this->morphTo('contributable', 'contributable_type', 'contributable_id');
    }
}