<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'idnumber', 'name_from_sensor_data'
    ];
    
    public function customer()
    {
        return $this->hasOne('OAMPI_Eval\User','id','user_id');
    }
    
    public function item()
    {
        return $this->hasOne('OAMPI_Eval\Reward','id','reward_id');
    }
    
    
}
