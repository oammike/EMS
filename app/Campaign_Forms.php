<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Campaign_Forms extends Model
{
    protected $table='campaign_forms';

    protected $fillable = [
        'campaign_id','formBuilder_id',
    ];
}
