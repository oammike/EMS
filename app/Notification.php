<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table= 'notification';

    protected $fillable = [
        'relatedModelID','type','from'
        

    ];

    public function info(){
        return $this->belongsTo(NotifType::class,'type');
    }
}
