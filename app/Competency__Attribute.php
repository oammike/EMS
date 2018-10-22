<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Competency__Attribute extends Model
{
   protected $table= 'competency__Attribute';

    protected $fillable = [
        'competency_id','attribute_id'
    ];

     public function competency()
    {
        return $this->belongsTo(Competency::class,'competency_id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class,'attribute_id');
    }
}
