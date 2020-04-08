<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['role_id'];
    protected $primaryKey = 'role_id';

    public function abilities()
    {
        return $this->belongsToMany(Ability::class, null, 'role_id', 'ability_id');
    }

    public function allowTo($ability)
    {
        $this->abilities()->syncWithoutDetaching($ability);
    }
}
