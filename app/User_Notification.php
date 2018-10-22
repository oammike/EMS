<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_Notification extends Model
{
     protected $table= 'user_Notification';

    protected $fillable = [
        'user_id','notification_id','seen'

    ];

    public function detail()
    {
    	return $this->belongsTo(Notification::class,'notification_id');
    }

    
}
