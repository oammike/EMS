<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Movement_ImmediateHead extends Model
{
    protected $table= 'movement_immediateHead';

    protected $fillable = [
        'movement_id', 'newFloor','oldFloor','imHeadCampID_old', 'imHeadCampID_new'
    ]; //'oldHead_id','newHead_id',

    public function oldCampaign(){
    	//return $this->hasManyThrough(Campaign::class, ImmediateHead::class, 'campaign_id','id','immediateHead_id_old');
        //return $this->hasManyThrough(Campaign::class, ImmediateHead_Campaign::class, 'campaign_id','id','immediateHead_id_old');
        //return $this->hasManyThrough(Campaign::class, Team::class, 'campaign_id','id','immediateHead_id_old');
       // return $this->belongsToMany(Campaign::class, ImmediateHead_Campaign::class,'campaign_id',)
    }

    public function info(){
    	return $this->belongsTo(Movement::class,'movement_id');
    }

   
}
