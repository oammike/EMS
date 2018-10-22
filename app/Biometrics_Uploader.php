<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Biometrics_Uploader extends Model
{
     protected $table= 'biometrics_uploader';

    protected $fillable = [
        'user_id','biometrics_id'
    ];
}
