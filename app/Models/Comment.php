<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function article()
    {
        return $this->belongsTo('App\Models\Article');
    }
}
