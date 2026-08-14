<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class NotificationDetail extends Model
{
    use SoftDeletes;

    function userWiseBusinessDetail()
    {
        return $this->hasOne('App\Models\UserwiseBusinessDetail', 'id', 'userBusinessId');
    }

    function businessDetail()
    {
        return $this->hasOne('App\Models\BusinessDetail', 'id', 'businessId');
    }

    function userDetail()
    {
        return $this->hasOne('App\Models\BusinessUser', 'id', 'businessUserId');
    }
}
