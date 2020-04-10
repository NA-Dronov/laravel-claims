<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $primaryKey = 'claim_id';

    protected $fillable = [
        'subject',
        'body',
        'user_id',
        'manager_id',
        'status'
    ];

    protected static $filters = [
        'status',
        'viewed',
        'has_answer'
    ];

    protected static $default_sorting = [
        'sort_by' => 'created_at',
        'sort_order' => 'desc'
    ];

    protected static $sortings = [
        'claim_id',
        'subject',
        'status',
        'user',
        'manager',
        'created_at'
    ];

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
     * Claim manager
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function manager()
    {
        return $this->hasOne(User::class, 'user_id', 'manager_id');
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

    /**
     * 
     * Claim responses
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function responses()
    {
        return $this->hasMany(Response::class, 'claim_id');
    }

    /**
     * Get all of the claim's files.
     */
    public function files()
    {
        return $this->morphMany(File::class, 'object');
    }

    /**
     * 
     * Parse claim filters
     * 
     * @return array
     */
    public static function parseFilters(array $data)
    {
        return array_filter($data, function ($value, $key) {
            return in_array($key, static::$filters) && isset($value);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * 
     * Parse claim sorting
     * 
     * @return array
     */
    public static function parseSorting(array $data)
    {
        $data = array_merge(static::$default_sorting, $data);

        $sorting = [
            'sort_by' => in_array($data['sort_by'], static::$sortings) ? $data['sort_by'] : static::$default_sorting['sort_by'],
            'sort_order' => in_array($data['sort_order'], ['asc', 'desc']) ? $data['sort_order'] : static::$default_sorting['sort_order'],
        ];

        return $sorting;
    }

    public function markAsViewed(User $user)
    {
        return ClaimUserRelation::firstOrCreate([
            'user_id' => $user->user_id,
            'claim_id' => $this->claim_id,
            'relation_type' => ClaimUserRelation::VIEWED,
        ]);
    }

    public function markAsResponsed(User $user)
    {
        return ClaimUserRelation::firstOrCreate([
            'user_id' => $user->user_id,
            'claim_id' => $this->claim_id,
            'relation_type' => ClaimUserRelation::HAS_RESPONSE,
        ]);
    }
}
