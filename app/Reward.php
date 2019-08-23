<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class Reward extends Model implements HasMedia
{
  use HasMediaTrait;
    protected $table = 'rewards';
    public function category()
    {
        return $this->hasOne('OAMPI_Eval\RewardCategory','id','category_id','preptime');
    }
    
    
    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
             ->setManipulations(['w' => 128, 'h' => 128, 'fm' => 'png'])
             ->performOnCollections('*')
             ->nonQueued();
    }
}
