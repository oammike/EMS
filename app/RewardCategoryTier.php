<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class RewardCategoryTier extends Model
{
    protected $table = 'reward_category_tiers';
    public function category_parent()
    {
        return $this->hasOne('OAMPI_Eval\RewardCategory', 'id', 'category_id');
    }

}
