<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class ImmediateHead_Campaign extends Model
{
    protected $table= 'immediateHead_Campaigns';
                       

    protected $fillable = [
        'immediateHead_id', 'campaign_id'
    ]; 


    public function immediateHeadInfo(){
    	return $this->belongsTo(ImmediateHead::class,'immediateHead_id');
        //return $this->hasOne(ImmediateHead::class,'immediateHead_id');
    } 

    public function campaign(){
    	return $this->belongsTo(Campaign::class,'id','campaign_id');
    } 
    public function campaignInfo(){
        return $this->belongsTo(Campaign::class,'id','campaign_id');
    } 

}
