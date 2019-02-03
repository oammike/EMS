<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Categorytag extends Model
{
    protected $table='categoryTags';

    protected $fillable = [
        'value','label'
    ];
}
