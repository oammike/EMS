<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Engagement_Flags extends Model
{
    protected $table= 'engagement_flags';
    protected $fillable = [
        'user_id','engagement_entryID','reason'
    ];
}
