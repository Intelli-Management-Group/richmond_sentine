<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AdvertisementDetail extends Model
{
    use SoftDeletes;

    /**
     * Get advertisement file data.
     */
    function advertisementFileDetail()
    {
        return $this->hasMany('App\Models\AdvertisementFileDetail', 'advertisementId','id');
    }
}
