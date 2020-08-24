<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class Donation extends Model implements HasMedia
{
    use HasMediaTrait;
    protected $table = 'donation_catalog';    
    
    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
             ->setManipulations(['w' => 128, 'h' => 128, 'fm' => 'png'])
             ->performOnCollections('*')
             ->nonQueued();
    }
}
