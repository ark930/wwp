<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    const STATUS_INIT = 'init';
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    protected $hidden = [
        'deleted_at',
    ];

    public function writer()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function versions()
    {
        return $this->hasMany('App\Models\ArticleVersion');
    }
}
