<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Coffeeshop extends Model
{
    protected $table = 'coffeeshop';
    protected $fillable = [
        'barista_user', 'status'
        
    ];
}
