<?php

namespace App\Jobs;

use App\Data;
use App\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
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

    private function DataHandler($incoming)
    {
        foreach ($incoming as $key => $data) {
            switch ($key) {
                case "confirmed":
                case "deaths":
                case "recovered":
                    if ($this->canUpdate($data['last_updated'], $key))
                        foreach ($data['locations'] as $location) {
                            $item = Data::updateOrCreate($this->search($location), $this->LocationData($location, $key));
                            Bus::dispatch(new UpdateHistory($location, $item, $key));
                        }
                    break;
            }
        }
    }

    private function canUpdate($date, $k)
    {
        $c = "last_update_" . $k;
        $db = Settings::where(['key' => $c])->first();
        if ($db === null || strtotime($db->value) < strtotime($date)) {
            Settings::updateOrCreate(['key' => $c,], ['key' => $c, 'value' => date("Y-m-d H:i:s")]);
            return true;
        }
        return false;
    }

    private
    function search($location)
    {
        return [
            "country" => $location['country'],
            "country_code" => $location['country_code'],
            "province" => $location['province'],
            "lat" => $location['coordinates']['lat'],
            "long" => $location['coordinates']['long'],
        ];
    }

    private
    function LocationData($location, $key)
    {
        return [
            "country" => $location['country'],
            "country_code" => $location['country_code'],
            $key => $location['latest'],
            "province" => $location['province'],
            "lat" => $location['coordinates']['lat'],
            "long" => $location['coordinates']['long'],
        ];
    }
}
