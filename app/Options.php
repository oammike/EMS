<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Options extends Model
{
    protected $table='options';

    protected $fillable = [
        'value','label', 'order'
    ];
}
