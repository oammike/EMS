<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idnumber'
    ];
    
    public function user()
    {
        return $this->belongsTo('OAMPI_Eval\User','idnumber','id');
    }
}
