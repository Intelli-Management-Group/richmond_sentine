<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserwiseArticleBookmarkDetail extends Model
{
    use SoftDeletes;

    /**
     * Get sub category data.
     */

    function articleDetail()
    {
        return $this->hasOne('App\Models\ArticleDetail', 'id', 'articleId');
    }
}
