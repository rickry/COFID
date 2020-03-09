<?php

namespace App\Jobs;

use App\Confirmed;
use App\ConfirmedHistory;
use App\Death;
use App\DeathHistory;
use App\Recovered;
use App\RecoveredHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class UpdateDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */

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
            if ($item = Death::create($this->LocationData($location)))
                $item->histories()->createMany($this->HistoryData($location['history']));
        }
    }

    public function SetRecovered($data)
    {
        foreach ($data['locations'] as $location) {
            if ($item = Recovered::create($this->LocationData($location)))
                $item->histories()->createMany($this->HistoryData($location['history']));
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
