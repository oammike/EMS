<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Gallery_User extends Model
{
    protected $table='gallery_user';

    protected $fillable = [
        'user_id','gallery_id','link'
    ];
}
