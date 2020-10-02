<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class RewardExclusive extends Model implements HasMedia
{
  use HasMediaTrait;
    protected $table = 'rewards_exclusives';
    public function campaign()
    {
        return $this->hasOne('OAMPI_Eval\Campaign','id','campaign_id');
    }


    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
             ->setManipulations(['w' => 128, 'h' => 128, 'fm' => 'png'])
             ->performOnCollections('*')
             ->nonQueued();
    }
}
