<?php

namespace App\Infrastructure\Persistance\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentRoleModel extends Model {
    protected $table = 'roles';
    protected $fillable = [
        'id',
        'name'
    ];

    public function contributions() {
        return $this->hasMany(EloquentContributionModel::class);
    }
}