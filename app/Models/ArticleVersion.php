<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleVersion extends Model
{
    public $timestamps = false;

    public function article()
    {
        return $this->belongsTo('App\Models\Article');
    }
}
