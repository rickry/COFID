<?php

namespace App\Http\Controllers;

use App\Data;
use App\Jobs\UpdateDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use NotificationChannels\Discord\Discord;
use NotificationChannels\Discord\DiscordMessage;

class DataController extends Controller
{
    public function index()
    {
        $this->dispatch(new UpdateDatabase());
    }

    public function getConfirmed(){
        $data = Data::select(DB::raw('SUM(confirmed) as confirmed'), DB::raw('SUM(deaths) as death'), DB::raw('SUM(recovered) as recovered'))->get();
        return ["total" => $data];
    }

    public function makeDonut()
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
        return [
            $this->makeDonut(),
            $this->getConfirmed()
            ];
    }
}
