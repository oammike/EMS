<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class FormBuilder extends Model
{
    protected $table='formBuilder';

    protected $fillable = [
        'createdBy','title','description'
    ];
}
