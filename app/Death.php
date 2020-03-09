<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Death extends Model
{
    protected $guarded = [];

    public function histories(){
        return $this->hasMany(DeathHistory::class);
    }
}
