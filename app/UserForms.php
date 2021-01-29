<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class UserForms extends Model
{
    protected $table = 'user_forms';
    protected $fillable = [
        'filename','isSigned','userUploader'
    ];


    
}
