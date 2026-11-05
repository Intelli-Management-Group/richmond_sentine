<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CategoryDetail extends Model
{
    use SoftDeletes;

    /**
     * Get parent category data.
     */

    function parentCategoryDetail()
    {
        return $this->hasOne('App\Models\CategoryDetail', 'id', 'parentId');
    }
}
