<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AdvertisementFileDetail extends Model
{
    //use SoftDeletes;

    /**
     * Get advertisement data.
     */

    function advertisementDetail()
    {
        return $this->hasOne('App\Models\AdvertisementDetail', 'id', 'advertisementId');
    }
}
