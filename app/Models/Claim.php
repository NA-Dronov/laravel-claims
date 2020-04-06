<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $primaryKey = 'claim_id';

    /**
     * Claim relations
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relations()
    {
        return $this->hasMany(ClaimUserRelation::class, 'claim_id');
    }

    /**
     * 
     * Claim author
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 
     * Claim status
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function claim_status()
    {
        return $this->belongsTo(ClaimStatus::class, 'status', 'code');
    }
}
