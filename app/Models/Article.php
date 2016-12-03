<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    protected $hidden = [
        'deleted_at',
    ];

    public function writer()
    {
        return $this->belongsTo('App\Models\User');
    }
}
