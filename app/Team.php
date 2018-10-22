<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table='team';

    protected $fillable = [
        'user_id','campaign_id','immediateHead_Campaigns_id','floor_id'
    ];


    // public function campaigns(){
    	
    //     return $this->belongsToMany(Campaign::class,'team','floor_id','campaign_id');
    // }

    // public function seats(){
    //     return $this->belongsToMany(User::class,'team','floor_id','user_id');
    // }

    public function userInfo(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leader(){
        //return $this->belongsToMany(ImmediateHead::class,'immediateHead_Campaigns','immediateHead_Campaigns_id','immediateHead_id');
        return $this->belongsTo(ImmediateHead_Campaign::class,'immediateHead_Campaigns_id');

    }

}
