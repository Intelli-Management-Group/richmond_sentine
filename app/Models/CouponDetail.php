<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CouponDetail extends Model
{
    use SoftDeletes;

    /**
     * Get parent category data.
     */

    function categoryDetail()
    {
        return $this->hasOne('App\Models\CategoryDetail', 'id', 'categoryId');
    }
}
