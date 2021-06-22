<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_PTextension extends Model
{
    protected $table= 'user_ptextension';

    protected $fillable = [
        'user_id','productionDate','filed_hours','timeStart','timeEnd', 'personnel'

    ];
}
