<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ArticleDetail extends Model
{
    use SoftDeletes;

    /**
     * Get author data.
     */

    function authorDetail()
    {
        return $this->hasOne('App\Models\AuthorDetail', 'id', 'authorId');
    }

    /**
     * Get category data.
     */

    function categoryDetail()
    {
        return $this->hasOne('App\Models\CategoryDetail', 'id', 'categoryId');
    }

    /**
     * Get article file data
     */

    function articleFileDetail()
    {
        return $this->hasMany('App\Models\ArticleFileDetail', 'articleId', 'id');
    }
}
