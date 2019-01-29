<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Categorytag extends Model
{
    protected $table='categorytags';

    protected $fillable = [
        'value','label'
    ];
}
