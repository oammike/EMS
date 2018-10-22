<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class CampaignLogos extends Model
{
    protected $table='campaign_logos';

    protected $fillable = [
        'filename','campaign_id'
    ];

}
