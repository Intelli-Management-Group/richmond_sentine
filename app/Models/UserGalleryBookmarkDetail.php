<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGalleryBookmarkDetail extends Model
{
    use SoftDeletes;

    /**
     * Get sub category data.
     */

    function galleryDetail()
    {
        return $this->hasOne('App\Models\GalleryDetail', 'id', 'galleryId');
    }
}
