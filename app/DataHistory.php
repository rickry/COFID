<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataHistory extends Model
{
    protected $guarded = [];

    public function scopeTotals($query)
    {
        return $query->select('date', DB::raw('SUM(confirmed) as confirmed'), DB::raw('SUM(deaths) as death'), DB::raw('SUM(recovered) as recovered'));
    }
}
