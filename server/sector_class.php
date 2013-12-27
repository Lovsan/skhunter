<?php

class SKdate {
  var $int;
  function SKdate($month,$day,$hour,$min){
    //number if total minutes passed in previous months
    //leap year is not taken into account
    $monthNames = array(
      "January" => 0, 
      "February" => 44640,
      "March" => 44640+40320,
      "April" => 44640+40320+44640,
      "May" => 44640+40320+44640+43200,
      "June" => 44640+40320+44640+43200+44640, 
      "July" => 44640+40320+44640+43200+44640+43200, 
      "August" => 44640+40320+44640+43200+44640+43200+44640, 
      "September" => 44640+40320+44640+43200+44640+43200+44640+44640, 
      "October" => 44640+40320+44640+43200+44640+43200+44640+44640+43200, 
      "November" => 44640+40320+44640+43200+44640+43200+44640+44640+43200+44640, 
      "December" => 44640+40320+44640+43200+44640+43200+44640+44640+43200+44640+43200
    );
	//MAGNUS: Commented this in order to fix sufix problem will see tonight at midnight if it works ^^
	
    //if ($hour==12) $hour=0;
    //if ($sufix=="PM") $hour+=12;
    if (isset($monthNames[$month])){
      $this->int=$min+$hour*60+($day-1)*1440+$monthNames[$month];
    } else {
      $this->int=-1;
    }
  }
}

class Sector {
  var $kingdoms;
  var $alliance;
  var $counter;
  
  function Sector($alliance,$name_arr,$status_arr,$type_arr,$land_arr,$networth_arr,$honout_arr){
    $this->alliance = $alliance;
	$this->counter = count($name_arr);
	
    for($i=1;$i<$this->counter+1;$i++){
      $this->kingdoms[$i]['n']=$name_arr[$i];
      $this->kingdoms[$i]['s']=$status_arr[$i];
      $this->kingdoms[$i]['l']=$land_arr[$i];
      $this->kingdoms[$i]['w']=$networth_arr[$i];
      $this->kingdoms[$i]['o']=$honout_arr[$i];
    }
  }
}
