<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserwiseBusinessCategoryDetail extends Model
{
    use SoftDeletes;

    /**
     * Get business data.
     */

    function businessDetail()
    {
        return $this->hasOne('App\Models\UserwiseBusinessDetail', 'id', 'businessId');
    }

    /**
     * Get business data.
     */

    function categoryDetail()
    {
        return $this->hasOne('App\Models\BusinessCategory', 'id', 'categoryId');
    }

}
