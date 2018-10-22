<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    protected $table= 'memos';

    protected $fillable = [
        'user_id','type','title', 'body' //memo types: slider, modal
    ];
}
