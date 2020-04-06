<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimStatus extends Model
{
    const OPEN = 'O';
    const PROCESSED = 'P';
    const CLOSED = 'C';

    protected $primaryKey = 'claim_statuses_d';
}
