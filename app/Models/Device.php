<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public function articles()
    {
        return $this->hasMany('App\Models\Article');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function createDevice()
    {
        $device = new Device();
        $device->save();

        return $device;
    }
}
