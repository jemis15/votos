<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table = 'votes';

    public $incrementing = false;
    protected $primaryKey = null;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'election_id',
        'candidate_id',
    ];
}
