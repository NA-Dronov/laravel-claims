<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    const CLAIM = 'C';
    const RESPONSE = 'R';

    protected $primaryKey = 'file_id';

    protected $fillable = [
        'object_id',
        'object_type',
        'original_name',
        'stored_name'
    ];

    public function claim()
    {
        return $this->hasOne(Claim::class);
    }
}
