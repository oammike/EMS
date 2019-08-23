<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class RewardCategory extends Model
{
    protected $table = 'reward_categories';
    public function tiers()
    {
        return $this->hasMany('OAMPI_Eval\RewardCategoryTier', 'category_id', 'id');
    }
    
    public function rewards()
    {
        return $this->hasMany('OAMPI_Eval\Reward','category_id','id');
    }
}
