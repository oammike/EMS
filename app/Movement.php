<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $table= 'movement';

    protected $fillable = [
        'user_id', 'withinProgram', 'fromPeriod', 'effectivity', 'isApproved','isDone','isNotedFrom','isNotedTo', 'requestedby', 'dateRequested', 'notedBy', 'dateRequested', 'notedBy', 'personnelChange_id'
    ]; //'oldHead_id','newHead_id',

    public function type(){
    	return $this->belongsTo(PersonnelChange::class, 'personnelChange_id');
    }

    public function employee(){
    	return $this->belongsTo(User::class);
    } 

    public function immediateHead_details()
    {
        return $this->hasOne(Movement_ImmediateHead::class,'movement_id');
    }

    

    public function position_details()
    {
        return $this->hasOne(Movement_Positions::class,'movement_id');
    }

    public function status_details()
    {
        return $this->hasOne(Movement_Status::class,'movement_id');
    }


    public function campaign(){
    	return $this->hasManyThrough(ImmediateHead::class,Campaign::class,  'id', 'campaign_id', 'newHead_id');
    } 
// select `campaign`.*, `immediateHead`.`campaign_id` from `campaign` inner join `immediateHead` on `immediateHead`.`id` = `campaign`.`newHead_id` where `immediateHead`.`campaign_id` = 3
// select `immediateHead`.*, `campaign`.`campaign_id` from `immediateHead` inner join `campaign` on `campaign`.`id` = `immediateHead`.`newHead_id` where `campaign`.`campaign_id` = 3    
// select `immediateHead`.*, `campaign`.`newHead_id` from `immediateHead` inner join `campaign` on `campaign`.`id` = `immediateHead`.`campaign_id` where `campaign`.`newHead_id` = 3
}
