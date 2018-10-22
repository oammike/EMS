<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class FormBuilderSubtypes extends Model
{
    protected $table='formBuilderSubtypes';

    protected $fillable = [
        'name',
    ];
}
