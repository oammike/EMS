<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Manpower extends Model
{
    protected $table= 'manpower';

    protected $fillable = [
        'user_id', 'campaign_id','manpower_reasonID', 'manpower_typeID', 'howMany', 'manpower_sourceID','position_id','status_id', 'manpower_foreignStatus_id', 'trainingStart', 'approved','notes','progress'
    ]; //'oldHead_id','newHead_id',

/*

MANPOWER_REASON:
* Additional
* Replacement

MANPOWER_TYPE:
- Billable
- Non Billable

MANPOWER_SOURCE:
- Internal
- External
- Both

MANPOWER_STATUSES
- status id

MANPOWER_FOREIGNSTATUS

*/

}
