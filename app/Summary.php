<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected $table= 'summary';

    protected $fillable = [
        'heading'
    ];

    public function columns()
    {
    	return $this->belongsToMany(Columntable::class,'summary__Columntable','summary_id','columntable_id');
    }
    public function rows()
    {
    	return $this->belongsToMany(Rowtable::class,'summary__Rowtable','summary_id','rowtable_id');
    }
}
