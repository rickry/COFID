<?php

namespace App\Jobs;

use App\Data;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateHistory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $location;
    private $item;
    private $key;


    public function __construct($location, Data $item, $key)
    {
        $this->location = $location;
        $this->item = $item;
        $this->key = $key;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->location['history'] as $date => $persons) {
            $d = date('Y-m-d', strtotime($date));
            $this->item->histories()->updateOrCreate(
                ["date" => $d],
                [
                    "date" => $d,
                    $this->key => $persons,
                ]);
        }
    }
}
