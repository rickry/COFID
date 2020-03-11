<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Data extends Model
{
    use Notifiable;

    protected $guarded = [];

    public function histories(){
        return $this->hasMany(DataHistory::class);
    }
    public function scopeTotals($query)
    {
        return $query->select(DB::raw('SUM(confirmed) as confirmed'), DB::raw('SUM(deaths) as death'), DB::raw('SUM(recovered) as recovered'));
    }
}
