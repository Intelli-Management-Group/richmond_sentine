<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AdminLoginDetail extends Model
{
	  use SoftDeletes;
	  
	/**
     * Encrypt the admin user's password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = md5($value);
    }
}
