<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class NotifType extends Model
{
   protected $table= 'notifType';

    protected $fillable = [
        'title','icon', 'description'

    ];
   
}
