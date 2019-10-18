<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Engagement_Comment extends Model
{
    protected $table= 'engagement_comment';

    public function replies()
    {
    	return $this->hasMany(Engagement_Reply::class,'comment_id');
    }

}
