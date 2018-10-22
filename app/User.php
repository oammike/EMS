<?php

namespace OAMPI_Eval;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = "users";
    protected $fillable = [
        'name', 'email','external','employeeNumber','accesscode', 'password','firstname','middlename','lastname','position_id', 'dateHired','userType_id','status_id','hascoverphoto','leadOverride'
        //'immediateHead_id','campaign_id','immediateHead_Campaigns_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function approvers()
    {
        return $this->belongsToMany(ImmediateHead_Campaign::class,'user_leaders','user_id','immediateHead_Campaigns_id');
    }

    public function availableVL()
    {
        return $this->hasMany(User_VLcredits::class,'user_id');
    }

    

    public function campaign(){
        return $this->belongsToMany(Campaign::class,'team','user_id','campaign_id')->select('name','campaign.id')->orderBy('team.id','DESC');
        //return $this->belongsToMany(ImmediateHead_Campaign::class,'team','user_id','immediateHead_Campaigns_id');//->select('name', 'campaign.id');
        //return $this->belongsTo(Campaign::class)->select('name', 'id');
        //return $this->belongsTo(Campaign::class,'immediateHead_Campaigns_id')->select('name', 'id');
    }

     public function evals()
    {
        return $this->hasMany(EvalForm::class,'user_id');
    }

      public function evaluations()
    {
        return $this->hasMany(EvalForm::class, 'user_id');
    }


    public function fixedSchedule()
    {
        return $this->hasMany(FixedSchedules::class,'user_id');
    }

    public function floor()
    {
        return $this->belongsToMany(Floor::class,'team','user_id','floor_id')->orderBy('team.id','DESC');
    }

    public function isAleader()
    {
        return $this->hasOne(ImmediateHead::class,'employeeNumber','employeeNumber');
    }

     public function logs()
    {
        return $this->hasMany(Logs::class,'user_id');
    }

     public function monthlySchedules()
    {
        return $this->hasMany(MonthlySchedules::class,'user_id');
    }

    public function movements(){
        return $this->hasMany(Movement::class);
    }

    public function notifications(){
         return $this->hasMany(User_Notification::class,'user_id');
        //return $this->belongsToMany(Notification::class,'user_Notification','user_id','notification_id');
    }

    public function notifInfo(){
        
        return $this->belongsToMany(Notification::class,'user_Notification','user_id','notification_id');
    }

    public function position(){
        return $this->belongsTo(Position::class, 'position_id')->select('name');
    }

    public function restdays()
    {
        return $this->hasMany(Restday::class,'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class,'userType_roles','userType_id', 'role_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class,'user_id');
    }

    public function slCredits()
    {
        return $this->hasMany(User_SLcredits::class,'user_id');
    }

    public function status(){
        return $this->belongsTo(Status::class)->select('name');
    }

     public function supervisor()
    {
        //return $this->belongsTo(ImmediateHead::class, 'immediateHead_id')->select('firstname','lastname','id');       
        //return $this->belongsToMany(ImmediateHead::class,'immediateHead_Campaigns','immediateHead_id', 'immediateHead_id');
        //return $this->belongsTo(ImmediateHead::class, 'immediateHead_Campaigns_id')->select('firstname','lastname','id');
        //return $this->belongsToMany(ImmediateHead_Campaign::class,'team','user_id','immediateHead_Campaigns_id')->orderBy('team.id', 'DESC'); //>select('firstname','lastname','id');
        return $this->hasOne(Team::class,'user_id');
    }

    public function team(){
        return $this->hasOne(Team::class,'user_id');
        //return $this->belongsTo(Team::class,'user_id');
    }

    public function userType(){
        return $this->belongsTo(UserType::class, 'userType_id');
    }

    public function viewedResources()
    {
        return $this->hasMany(User_Resource::class, 'user_id');
    }


    public function vlCredits()
    {
        return $this->hasMany(User_VLcredits::class,'user_id');
    }


    // public function teamMovements(){
    //     return $this->hasManyThrough(Movement_ImmediateHead::class,'team','user_id','team_id');
    // }

   

    // public function leaders()
    // {
       
    //     return $this->belongsToMany(ImmediateHead::class,'team','user_id','immediateHead_id');//->select('firstname','lastname','immediateHead.id');
    // }


     
    
}
