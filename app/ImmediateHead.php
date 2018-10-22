<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class ImmediateHead extends Model
{
    protected $table= 'immediateHead';

    protected $fillable = [
        'firstname','lastname','employeeNumber'
        //'campaign_id'
    ];

    public function subordinates(){
        //return $this->belongsToMany(User::class,'team','immediateHead_id','user_id');
    	//return $this->hasMany(User::class, 'immediateHead_Campaigns_id');
        //return $this->belongsToMany(User::class,'immediateHead_Campaigns','immediateHead_id', 'campaign_id')->select('name');
        return $this->hasManyThrough(Team::class,ImmediateHead_Campaign::class,'immediateHead_id', 'immediateHead_Campaigns_id');//->select('name');
        
    } 

    // public function campaign(){
    // 	return $this->belongsTo(Campaign::class);
    // }

    public function campaigns(){
        //return $this->belongsToMany(Campaign::class,'immediateHead_Campaigns','immediateHead_id', 'campaign_id');
        return $this->belongsToMany(Campaign::class,'immediateHead_Campaigns','immediateHead_id', 'campaign_id')->withPivot('immediateHead_id');
        //return $this->belongsToMany(Campaign::class,'team','immediateHead_id','immediateHead_id', 'campaign_id');
    
    }

    public function userData(){
        return $this->belongsTo(User::class,'employeeNumber', 'employeeNumber');
    }

    public function myCampaigns()
    {
        return $this->hasMany(ImmediateHead_Campaign::class,'immediateHead_id');
    }

    
}
