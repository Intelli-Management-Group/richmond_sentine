<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ArticleFileDetail extends Model
{
    /**
     * Get article data.
     */

    function articleDetail()
    {
        return $this->hasOne('App\Models\ArticleDetail', 'id', 'articleId');
    }
   
}
