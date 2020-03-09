<?php

namespace App\Http\Controllers;

use App\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ConfirmedController extends Controller
{
    public function makeDonut()
    {
        $data = Data::select('country', 'country_code', DB::raw('SUM(confirmed) as total_confirmed'))
            ->groupBy('country_code')
            ->orderBy('total_confirmed', 'DESC')
            ->limit(6)
            ->get();

        return response()->json($data);
    }


}
