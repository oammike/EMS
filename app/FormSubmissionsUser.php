<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class FormSubmissionsUser extends Model
{
    protected $table='form_submissions_users';

    protected $fillable = [
        'user_id','formBuilder_id',
    ];
}
