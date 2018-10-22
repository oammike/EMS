<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class FormBuilder_Items extends Model
{
    protected $table='formBuilder_items';

    protected $fillable = [
        'label','className','required','name','placeholder','description'
    ];
}
