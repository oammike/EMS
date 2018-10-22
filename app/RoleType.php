<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class RoleType extends Model
{
    protected $table= 'roleTypes';

    protected $fillable = [
        'name',
    ];
}
