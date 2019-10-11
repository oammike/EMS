<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Engagement extends Model
{
    protected $table= 'engagement';

    protected $fillable = [
        'name','description','startDate','endDate','active','withVoting','fairVoting','multipleEntry','multipleVote','body'
    ];

    
}
