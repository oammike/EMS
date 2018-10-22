<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class TempUpload extends Model
{
    protected $table= 'temp_uploads';

    protected $fillable = [
    	'employeeNumber','productionDate','logTime','logType'
         
    ];
}
