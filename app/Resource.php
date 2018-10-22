<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
   protected $table='resources';

    protected $fillable = [
        'name','description','link','user_id'
    ];


    public function uploader(){
    	
        return $this->hasOne(User::class,'user_id');
    }

    public function viewers(){
    	
        return $this->belongsToMany(User::class,'user_resource','resource_id','user_id');
    }

    
}
