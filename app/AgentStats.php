<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class AgentStats extends Model
{
    protected $table='agent_stats';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'campaign_id', 'timestamp', 'pause_duration', 'dead_duration', 'wait_duration', 'talk_duration', "dispo_duration", 'customer_duration', 'status', 'pause_code'
    ];
}
