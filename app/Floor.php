<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $table='floor';

    protected $fillable = [
        'name','description','PEZA'
    ];


    public function campaigns(){
    	
        return $this->belongsToMany(Campaign::class,'team','floor_id','campaign_id');
    }

    public function occupants(){
        return $this->belongsToMany(User::class,'team','floor_id','user_id');
    }



    
}
