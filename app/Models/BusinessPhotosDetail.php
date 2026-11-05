<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BusinessPhotosDetail extends Model
{
    /**
     * Get business data.
     */

    function businessDetail()
    {
        return $this->hasOne('App\Models\BusinessDetail', 'id', 'businessId');
    }

}
