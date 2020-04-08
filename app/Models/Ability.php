<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    protected $guarded = ['ability_id'];
    protected $primaryKey = 'ability_id';

    public function roles()
    {
        return $this->belongsToMany(Role::class, null, 'ability_id', 'role_id');
    }
}
