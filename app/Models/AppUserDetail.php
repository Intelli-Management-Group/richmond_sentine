<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AppUserDetail extends Model
{
  	use SoftDeletes;
    use Notifiable;

    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    /**
     * Get steps data.
     */

    function stepDetail()
    {
        return $this->hasOne('App\Models\UserwiseStepsDetail', 'userId', 'id');
    }
}
