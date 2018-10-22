<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table= 'attribute';
    protected $fillable = [
        'name'
    ];
}
