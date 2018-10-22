<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class FormSubmissions extends Model
{
    protected $table='form_submissions';

    protected $fillable = [
        'user_id','value',
    ];
}
