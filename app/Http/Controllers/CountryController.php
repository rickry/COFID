<?php

namespace App\Http\Controllers;

use App\Data;
use App\DataHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function index(){
        $data = Data::orderBy('country_code')->get();
        $data->load('histories');

        return response()->json($data);
    }
}
