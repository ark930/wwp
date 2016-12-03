<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public function writer()
    {
        return $this->belongsTo('App\Models\User');
    }
}
