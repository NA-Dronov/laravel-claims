<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $primaryKey = 'response_id';

    protected $fillable = [
        'subject',
        'body',
        'user_id',
        'claim_id'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function claim()
    {
        return $this->belongsTo(Claim::class, 'claim_id');
    }

    /**
     * Get all of the response's files.
     */
    public function files()
    {
        return $this->morphMany(File::class, 'object');
    }
}
