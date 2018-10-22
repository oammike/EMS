<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class FormBuilderElements extends Model
{
    protected $table='formBuilder_elements';

    protected $fillable = [
        'type',
    ];
}
