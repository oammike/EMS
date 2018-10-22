<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table= 'roles';

    protected $fillable = [
        'name', 'roleType_id', 'label'
    ];
}
