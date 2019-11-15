<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class FormSubmissionsReviewer extends Model
{
    protected $table='form_submissions_reviewer';

    protected $fillable = [
        'user_id','submission_id','oldStatus','newStatus'
    ];
}
