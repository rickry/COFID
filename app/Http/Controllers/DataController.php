<?php

namespace App\Http\Controllers;

use App\Data;
use App\DataHistory;
use App\Jobs\UpdateDatabase;
use App\Settings;
use Carbon\Carbon;
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

    public function all()
    {
        return [
            "totals" => $this->totals(),
            "graph" => $this->graph(),
            "countries" => $this->countries(),
            "lastUpdate" => $this->lastUpdate(true),
        ];
    }

    public function totals()
    {
        $data = Data::totals()->first();
        return [
            "confirmed" => $this->formatNumber($data->confirmed),
            "death" => $this->formatNumber($data->death),
            "recovered" => $this->formatNumber($data->recovered)
        ];
    }

    private function formatNumber($x)
    {
        return number_format($x, 0, ',', '.');
    }

    public function lastUpdate($forHumans)
    {
        $db = Settings::orderBy('value', 'DESC')->first();
        if (isset($db->value))
            if ($forHumans) {
                return Carbon::parse($db->value)->diffForHumans();
            } else {
                return $db->value;
            }
        return null;

    }

    public function colors()
    {
        $data = Data::select('country_code', 'country')->groupBy('country_code')->get();
        return ["codes" => $data];
    }

    public function graph($month = null)
    {
        if ($month == null)
            $month = date('m');
        $prev = DataHistory::totals()
            ->groupBy('date')
            ->orderBy('date', 'DESC')
            ->whereMonth('date', (int)$month - 1)
            ->first();
        if ($prev == null) {
            $confirmed = 0;
            $death = 0;
            $recovered = 0;
        } else {
            $confirmed = $prev->confirmed;
            $death = $prev->death;
            $recovered = $prev->recovered;
        }

        $data = DataHistory::totals()
            ->groupBy('date')
            ->whereMonth('date', $month)
            ->get();

        $out = [];
        foreach ($data as $item) {
            $out[] = [
                "day" => (int)date('d', strtotime($item->date)),
                "date" => $item->date,
                "confirmed" => $item->confirmed -= $confirmed,
                "death" => $item->death -= $death,
                "recovered" => $item->recovered -= $recovered,
            ];
        }
        return ["month" => (int)$month, "data" => $out];
    }

    public function countries(){
        $data = Data::select('country', 'country_code', DB::raw('SUM(confirmed) as confirmed'))
            ->groupBy('country_code')
            ->orderBy('confirmed', 'DESC')
            ->get();

        return $data;
    }
}
