<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class FormBuilderElem_Values extends Model
{
    protected $table='formBuilderElem_values';

    protected $fillable = [
        'label','value','selected'
    ];
}
