<?php

namespace App\Http\Controllers;

use App\Confirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfirmedController extends Controller
{
    public function makeDonut(){
        $data = Confirmed::select('country', 'country_code', DB::raw('SUM(latest) as total_confirmed'))
            ->groupBy('country_code')
            ->orderBy('total_confirmed', 'DESC')
            ->limit(6)
            ->get();

        return response()->json($data);
    }
}
