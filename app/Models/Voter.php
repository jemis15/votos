<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    protected $fillable = ['user_id', 'event_id'];
    public $timestamps = false;
}
