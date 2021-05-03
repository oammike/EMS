<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table= 'announcement';

    protected $fillable = [
        'template','user_id','title','message_body','isDraft','publishDate','publishExpire','showAlways','hidden'
    ];
}
