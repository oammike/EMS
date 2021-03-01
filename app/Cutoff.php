<?php

namespace OAMPI_Eval;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

        //$today = date('d');
        $today = Carbon::now('GMT+8')->format('d');
        $ngayon = Carbon::now('GMT+8');

        
        if ($ngayon->format('d') <= ($this->first)){ // + $this->paydayInterval
            if (date('m') === "01"){
                $from = (date('Y')-1)."-";

            }  else { 
                $from = $ngayon->format('Y')."-"; //date('Y')."-";

            }
            
            $from .=  Carbon::now('GMT+8')->subMonth()->format('m')."-"; //date('m',strtotime("last month"))."-";
            $from .= ($this->second+1);


            
            $to = date('Y')."-";
            $to .= (date('m'))."-";
            $to .= ($this->first);
            
        } else if (($today > $this->first)&&($today <= $this->second) || $today == ($this->first + $this->paydayInterval) ) //+ $this->paydayInterval
        {
            $from = $ngayon->format('Y')."-"; //date('Y')."-";
            $from .= $ngayon->format('m')."-"; //(date('m'))."-";
            $from .= ($this->first+1);


            
            $to = $ngayon->format('Y')."-"; //date('Y')."-";
            $to .= $ngayon->format('m')."-"; //(date('m'))."-";
            $to .= ($this->second);
            
            
            
            

        } else {
            $mo = $ngayon->format('m');
            $from = date('Y')."-";
            $from .=  $mo."-";//(date('m'))."-";
            $from .= ($this->second+1);

            if($mo == 12 ){
                $to = (date('Y')+1)."-";
                //$to .= date('m',strtotime("next month"))."-";
                $to .= Carbon::now('GMT+8')->addMonth(1)->format('m')."-";
                $to .= ($this->first);
            }
            else{

                if($ngayon->format('d')== '31')
                {
                    $to = date('Y')."-";
                    //$m = date('m')+1;
                    $m = Carbon::now('GMT+8')->addMonth(1)->format('m');
                    $to .= $m."-";
                    $to .= ($this->first);
                
                }else
                {
                    $to = date('Y')."-";
                    $em = Carbon::now('GMT+8')->format('m')+1;
                    //$to .= date('m',strtotime("next month"))."-";
                    $to .= $em ."-";
                    $to .= ($this->first);
                }
            }
            
            
            
        }

        $period = $from."_";
        $period .= $to;

        return $period;
        //return $today;
    }



}


