<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EventDetail extends Model
{
    use SoftDeletes;

    /**
     * Get event category data.
     */

    function eventCategory()
    {
        return $this->hasOne('App\Models\EventCategory', 'id', 'categoryId');
    }

    /**
     * Get event category data.
     */

    function eventBusiness()
    {
        return $this->hasOne('App\Models\BusinessDetail', 'id', 'businessId');
    }
}
