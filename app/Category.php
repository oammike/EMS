<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table='categories';

    protected $fillable = [
        'name','description'
    ];

   public function resources()
    {
    	return $this->belongsToMany(Resource::class, 'resource_category', 'category_id', 'resource_id');

    }


   
}
