<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_PUBLISHED_WITH_DRAFT = 'published_with_draft';
    const STATUS_TRASHED = 'trashed';

    protected $hidden = [
        'deleted_at',
    ];

    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }

//    public function writer()
//    {
//        return $this->belongsTo('App\Models\User', 'user_id');
//    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

//    public function versions()
//    {
//        return $this->hasMany('App\Models\ArticleVersion');
//    }

    public function publishedVersion()
    {
        return $this->belongsTo('App\Models\ArticleVersion', 'publish_version_id');
    }

    public function draftVersion()
    {
        return $this->belongsTo('App\Models\ArticleVersion', 'draft_version_id');
    }
}
