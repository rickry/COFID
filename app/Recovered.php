<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recovered extends Model
{
    protected $guarded = [];

    public function histories(){
        return $this->hasMany(RecoveredHistory::class);
    }
}
