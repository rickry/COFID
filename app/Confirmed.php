<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Confirmed extends Model
{
    protected $guarded = [];

    public function histories(){
        return $this->hasMany(ConfirmedHistory::class);
    }
}
