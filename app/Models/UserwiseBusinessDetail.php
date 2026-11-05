<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserwiseBusinessDetail extends Model
{
    use SoftDeletes;

    /**
     * Get business photos data.
     */

    function imageDetail()
    {
        return $this->hasMany('App\Models\UserwiseBusinessPhotosDetail', 'businessId', 'id');
    }

    /**
     * Get business user data.
     */

    function userDetail()
    {
        return $this->hasOne('App\Models\BusinessUser', 'id', 'createdBy');
    }
}
