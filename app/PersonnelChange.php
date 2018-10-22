<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class PersonnelChange extends Model
{
     protected $table= 'personnelChange';

    protected $fillable = [
        'name',
    ];
}
