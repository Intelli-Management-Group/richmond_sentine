<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserwiseBusinessPhotosDetail extends Model
{
    /**
     * Get business data.
     */

    function businessDetail()
    {
        return $this->hasOne('App\Models\UserwiseBusinessDetail', 'id', 'businessId');
    }

}
