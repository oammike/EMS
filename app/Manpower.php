<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Manpower extends Model
{
    protected $table= 'manpower';

    protected $fillable = [
        'user_id', 'reason', 'type', 'howMany', 'source','campaign_id','position_id','status', 'foreignStatus', 'trainingStart', 'approved'
    ]; //'oldHead_id','newHead_id',

/*

REASONS:
* Additional
* Replacement

TYPES:
- Billable
- Non Billable

SOURCE:
- Internal
- External
- Both

*/

}
