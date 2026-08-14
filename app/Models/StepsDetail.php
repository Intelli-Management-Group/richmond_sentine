<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StepsDetail extends Model
{
    use SoftDeletes;

    /**
     * Get user data.
     */

    function userDetail()
    {
        return $this->hasOne('App\Models\AppUserDetail', 'id', 'userId');
    }

}
