<?php

namespace App\Http\Controllers;

use App\Data;
use App\DataHistory;
use App\Jobs\UpdateDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use NotificationChannels\Discord\Discord;
use NotificationChannels\Discord\DiscordMessage;

class DataController extends Controller
{
    //http://coronavirus-tracker-api.herokuapp.com/all

    public function index()
    {
        $this->dispatch(new UpdateDatabase());

        return "Done.";
    }

    public function totals(){
        $data = Data::select(DB::raw('SUM(confirmed) as confirmed'), DB::raw('SUM(deaths) as death'), DB::raw('SUM(recovered) as recovered'))->first();
        return ["totals" => $data];
    }

    public function donut()
    {
        $data = Data::select('country', 'country_code', DB::raw('SUM(confirmed) as total_confirmed'))
            ->groupBy('country_code')
            ->orderBy('total_confirmed', 'DESC')
            ->limit(6)
            ->get();

        return ["donut" => $data];
    }

    public function all()
    {
        return array_merge($this->totals(),$this->donut(), $this->graph());
    }

    public function colors(){
        $data = Data::select('country_code', 'country')->groupBy('country_code')->get();
        return ["codes" => $data];
    }

    public function graph($month = null){
        if ($month == null)
            $month = date('m');
        $prev = DataHistory::select('date', DB::raw('SUM(confirmed) as confirmed'), DB::raw('SUM(deaths) as death'), DB::raw('SUM(recovered) as recovered'))
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->whereMonth('date',(int)$month-1)
            ->first();

        $data = DataHistory::select('date', DB::raw('SUM(confirmed) as confirmed'), DB::raw('SUM(deaths) as death'), DB::raw('SUM(recovered) as recovered'))
            ->groupBy('date')
            ->whereMonth('date',$month)
            ->get();

        foreach ($data as $item){
            $out[] = [
                "day" => (int)date('d', strtotime($item->date)),
                "date" => $item->date,
                "confirmed" => $item->confirmed -= $prev->confirmed,
                "death" => $item->death -= $prev->death,
                "recovered" => $item->recovered -= $prev->recovered,
            ];
        }
        return ["graph" => ["month" => (int)$month, "data" => $out]];
    }
}
