<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Data extends Model
{
    use Notifiable;

    protected $guarded = [];

    public function histories(){
        return $this->hasMany(DataHistory::class);
    }
    public function routeNotificationForDiscord()
    {
        return $this->discord_channel;
    }
}
