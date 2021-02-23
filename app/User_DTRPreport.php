<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class User_DTRPreport extends Model
{
    protected $table = "user_dtrpReports";
    protected $fillable = [
         'user_id', 'productionDate', 'actualLog','approvedBy','dateApproved','logType_id','notes','verifiedBy','remarks'
    ];
}
