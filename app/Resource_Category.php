<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Resource_Category extends Model
{
    protected $table='resource_category';

    protected $fillable = [
        'category_id','resource_id'
    ];


   
}
