<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use NotificationChannels\Discord\Discord;
use NotificationChannels\Discord\DiscordMessage;

class DataController extends Controller
{
    public function index()
    {
        $this->dispatch(new UpdateDatabase());
    }
}
