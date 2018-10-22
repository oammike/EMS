<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class AgentScheds extends Model
{
    protected $table='agent_sched';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'campaign_id', 'timestamp', 'event', 'duration'
    ];
}
