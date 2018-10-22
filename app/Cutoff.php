<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;

class Cutoff extends Model
{
    protected $table= 'cutoffs';
	protected $fillable = ['first', 'second', 'paydayInterval', 'month13th', 'day13th', 'synchMonth1', 'synchMonth2', 'synchDate1', 'synchDate2'];

	public function getOrdinalDate($i) { 
        if (($i == '11') || ($i == '12') || ($i =='13'))
                $ord= "th"; 
            else {
                $digit = substr($i, -1, 1);
                $ord = "th";
                switch($digit)
                {
                    case 1: $ord = "st"; break;
                    case 2: $ord = "nd"; break;
                    case 3: $ord = "rd"; break;
                    break;
                }
            }
            $n = $i.$ord;
            return $n;

    }

    public function startingPeriod(){

        $today = date('d');

        
        if ($today <= ($this->first + $this->paydayInterval)){
            if (date('m') === "01"){
                $from = (date('Y')-1)."-";

            }  else { 
                $from = date('Y')."-";

            }
            
            $from .= date('m',strtotime("last month"))."-";
            $from .= ($this->second+1);
            
           
            
        } else if (($today > $this->first)&&($today <= ($this->second + $this->paydayInterval))){
            $from = date('Y')."-";
            $from .= (date('m'))."-";
            $from .= ($this->first+1);
                        

        } else {

            $from = date('Y')."-";
            $from .= (date('m'))."-";
            $from .= ($this->second+1);            
            
        }

        $periodFrom = date("Y-m-d", strtotime($from));
        return $periodFrom;
    }


    public function endingPeriod(){

        $today = date('d');
        
        if ($today <= ($this->first + $this->paydayInterval)){
            
            
            $to = date('Y')."-";
            $to .= (date('m'))."-";
            $to .= ($this->first);
            
        } else if (($today > $this->first)&&($today <= ($this->second + $this->paydayInterval))){
            
            
            $to = date('Y')."-";
            $to .= (date('m'))."-";
            $to .= ($this->second);
            

        } else {

            
            $to = date('Y')."-";
            $to .= date('m',strtotime("next month"))."-";
            $to .= ($this->first);
            
        }

        $periodTo = date("Y-m-d", strtotime($to));
        return $periodTo;
    }

    public function getCurrentPeriod(){

        $today = date('d');
        
        if ($today <= ($this->first + $this->paydayInterval)){
            if (date('m') === "01"){
                $from = (date('Y')-1)."-";

            }  else { 
                $from = date('Y')."-";

            }
            
            $from .= date('m',strtotime("last month"))."-";
            $from .= ($this->second+1);
            
            $to = date('Y')."-";
            $to .= (date('m'))."-";
            $to .= ($this->first);
            
        } else if (($today > $this->first)&&($today <= ($this->second + $this->paydayInterval))){
            $from = date('Y')."-";
            $from .= (date('m'))."-";
            $from .= ($this->first+1);
            
            $to = date('Y')."-";
            $to .= (date('m'))."-";
            $to .= ($this->second);
            

        } else {

            $from = date('Y')."-";
            $from .= (date('m'))."-";
            $from .= ($this->second+1);
            
            $to = date('Y')."-";
            $to .= date('m',strtotime("next month"))."-";
            $to .= ($this->first);
            
        }

        $period = $from."_";
        $period .= $to;

        return $period;
    }



}


