<?php

namespace App\Http\Controllers;

use App\Confirmed;
use App\Death;
use App\Jobs\UpdateDatabase;
use App\Recovered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DataController extends Controller
{
    public function index()
    {
        $this->dispatch(new UpdateDatabase());
    }

    public function handle()
    {
        $response = Http::get('http://coronavirus-tracker-api.herokuapp.com/all');

        if ($response->ok()) {
            $this->DataHandler($response->json());
        }

        //return response()->json($response->json());
    }

    public function DataHandler($incoming)
    {
        foreach ($incoming as $key => $data)
            switch ($key) {
                case "confirmed":
                    $this->SetConfirmed($data);
                    break;

//                case "deaths":
//                    $this->SetDeaths($data);
//                    break;
//
//                case "recovered":
//                    $this->SetRecovered($data);
//                    break;
            }
    }

    public function SetConfirmed($data)
    {
        foreach ($data['locations'] as $location) {
            if ($item = Confirmed::updateOrCreate($this->search($location), $this->LocationData($location)))
                foreach ($location['history'] as $date => $persons) {
                    $d = date('Y-m-d', strtotime($date));
                    $item->histories()->updateOrCreate(
                        [
                            "date" => $d
                        ],
                        [
                            "date" => $d,
                            "persons" => $persons,
                        ]);
                }
        }
    }

    public function SetDeaths($data)
    {
        foreach ($data['locations'] as $location) {
            if ($item = Death::updateOrCreate($this->search($location), $this->LocationData($location)))
                foreach ($location['history'] as $date => $persons) {
                    $d = date('Y-m-d', strtotime($date));
                    $item->histories()->updateOrCreate(
                        [
                            "date" => $d
                        ],
                        [
                            "date" => $d,
                            "persons" => $persons,
                        ]);
                }
        }
    }

    public function SetRecovered($data)
    {
        foreach ($data['locations'] as $location) {
            if ($item = Recovered::updateOrCreate($this->search($location), $this->LocationData($location)))
                foreach ($location['history'] as $date => $persons) {
                    $d = date('Y-m-d', strtotime($date));
                    $item->histories()->updateOrCreate(
                        [
                            "date" => $d
                        ],
                        [
                            "date" => $d,
                            "persons" => $persons,
                        ]);
                }
        }
    }

    private function search($location)
    {
        return [
            "lat" => $location['coordinates']['lat'],
            "long" => $location['coordinates']['long']
        ];
    }

    public function LocationData($location)
    {
        return [
            "country" => $location['country'],
            "country_code" => $location['country_code'],
            "latest" => $location['latest'],
            "province" => $location['province'],
            "lat" => $location['coordinates']['lat'],
            "long" => $location['coordinates']['long'],
        ];
    }

    public function HistoryData($item, $histories)
    {
        $data = [];
        foreach ($histories as $date => $persons) {
            $d = date('Y-m-d', strtotime($date));
            $item->histories()->updateOrCreate(
                [
                    "date" => $d
                ],
                [
                    "date" => $d,
                    "persons" => $persons,
                ]);
        }

        return $data;
    }
}
