<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Paycutoff extends Model
{
     protected $table= 'paycutoffs';

    protected $fillable = [
        'fromDate','toDate'

    ];
}
