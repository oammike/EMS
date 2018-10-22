<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
     protected $table='campaign';

    protected $fillable = [
        'name'
    ];

    // public function leaders(){
    // 	return $this->hasMany(ImmediateHead::class);
    // }

    public function logo(){
        return $this->hasOne(CampaignLogos::class, 'campaign_id');
    }

    public function leaders(){
    	return $this->belongsToMany(ImmediateHead::class, 'immediateHead_Campaigns', 'campaign_id', 'immediateHead_id');
        //return $this->belongsToMany(ImmediateHead::class,'team','campaign_id','immediateHead_id');//,ImmediateHead_Campaign::class,'immediateHead_Campaigns_id','id'
    }

    // public function members(){
    //     return $this->belongsToMany(User::class,'team','campaign_id','user_id');
    // }

    // // public function locations(){
    // //     return $this->belongsToMany(Floor::class,'team','campaign_id','floor_id');
    // // }

    public function teams(){
        //return $this->hasMany(Team::class);
        return $this->hasManyThrough(Team::class, ImmediateHead_Campaign::class,'campaign_id','immediateHead_Campaigns_id');
    }
}
