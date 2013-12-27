<?php
//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate'); 
header('Cache-Control: post-check=0, pre-check=0', FALSE); 
header('Pragma: no-cache');
header("Content-type: text/plain");
ob_start();

include_once('setup_templates.php');
require("hunter_config.php");

// version: 1.5 beta
// 1.5 
// 1.4b removed refenrences in foreach loop (as originally in v1.4) not all servers handled references well see line 226


$serverVersion = "v1.5 Mar 2010";
//59 for newbie mode!!! 72 hours database collection
$maxInterval=50;
$neededHunterVersion=13;

//function list
function debug($msg=""){
	ob_end_clean();
	echo('/*ERROR*/ and message /*'.$msg.'*/');
	die;
}

function dropHunter(){
  ob_end_clean();
  echo('/*OK*/');
  echo('/*DROP*/');
  die;
}

function echoServerMesage($serverMSG,$serverMSGcolor,$MSGisLink,$MSGlinkURL){
  echo('srvmsg = "'.$serverMSG.'";');
  echo('srvmsg_color = "'.$serverMSGcolor.'";');
  if($MSGisLink) echo('srvmsg_link = "'.$MSGlinkURL.'";'); else echo('srvmsg_link = null;');
}

function getDifference($new,$old){
	$dif_min=(int)$old-(int)$new;
	$h = (int)($dif_min/60);
	$min = $dif_min % 60;
	if ($h > 0)$str = $h."h";
	$str .= " ".$min."min";
	return $str;
}

if(isset($_GET['hello']) && $_GET['aPWD'] == $HNPassword ){	
$T_access = false; 
$T_database = false;
$T_table = false;


$conn = NULL;
$count = NULL;
  
$T_message = array (
		"access" => array (true => "success", false => "failure"),
		"database" => array(true => "is alive", false => "is dead"),
		"table" => array(true => " and ready", false => ", needs attention"),
		"useAccessWhiteList" => array(0 => "off", 1 => "on")
);
  

  $conn = mysql_connect($host,$user,$password);
  if(!$conn) {
		$T_access = false; 

  } else if (!mysql_select_db($database)) {
		$T_access = true;   
		$T_database = false;  

  } else {
  		$T_access = true;   
		$T_database = true;  

		require_once("SafeSQL.class.php");
		$safe = new SafeSQL_MySQL;
		$query_string = $safe->query("SELECT COUNT(*) as NUM FROM `%s`;", array($tableName));
		$result=mysql_query($query_string);
		if(!$result){
			$T_table = false;
		} else {
			$T_table = true;		
			$count = mysql_result($result,0,"NUM");
		}
  }
	ob_end_clean();
	$_output = "";
	
	$_output .= "Server Status: ";
	$_output .= "running";
	$_output .= "\n";	

	if(isset($_GET['connection'])){
	
	$_output .= "Database Connection: ";
	$_output .= $T_message['access'][$T_access];
	$_output .= "\n";	
		
	}	
	
	$_output .= "Database Status: ";
	$_output .= $T_message['database'][$T_database];
	$_output .= $T_message['table'][$T_table];
	$_output .= "\n";	
	
	if(isset($_GET['wil'])){
	
	$_output .= "User Access WhiteList: ";	
	$_output .= $T_message['useAccessWhiteList'][$useAccessWhiteList];
	$_output .= "\n";	
	
	}
	
	if(isset($_GET['ver'])){
	
	$_output .= "Server Version: ";	
	$_output .= $serverVersion;
	$_output .= "\n";	

	}
	
	echo $_output;
	if($conn) mysql_close($conn);
	exit;

}

// this request will not be handled further.
if(!isset($_POST['aPWD'])){
	ob_end_clean();
	$_output = "";

	$_output .= "Server Status: ";
	$_output .= "running";
	$_output .= "\n";	

	echo $_output;

	exit;
}

// check for accessibility
//TODO MAGNUS: FIX THIS SH1T
//$serverURL=$_POST['l'];
//$pattern='terranova';
// $pattern="http://wwwstarkingdoms.com/game/terranova/";
//if(!preg_match($pattern, $serverURL)) debug($serverURL,$pattern);
//if(!preg_match($pattern, $serverURL)) debug('Wrong server imputed, server is '.$serverURL.' and pattern is '.$pattern);
$serverURL=1;
$pattern=1;

if(preg_match($pattern, $serverURL)) debug('Wrong server imputed, server is '.$serverURL.' and pattern is '.$pattern);

$passwordPHP=$_POST['aPWD'];
if ($passwordPHP != $HNPassword) debug('You need to configure your Firefox extension');
$kingdomName=$_POST['kdnm'];
if (empty($kingdomName)) debug('You need to configure your Firefox extension');
$kingdomEmail=$_POST['email'];
$kingdomX=(int)$_POST['kdx'];
if (($kingdomX < $x_min) || ($kingdomX > $x_max)) debug('You need to configure your Firefox extension');
$kingdomY=(int)$_POST['kdy'];
if (($kingdomY < $y_min) || ($kingdomY > $y_max)) debug('You need to configure your Firefox extension');
$galaxyX=(int)$_POST['x'];
if (($galaxyX < $x_min) || ($galaxyX > $x_max)) dropHunter();
$galaxyY=(int)$_POST['y'];
if (($galaxyY < $y_min) || ($galaxyY > $y_max)) dropHunter();

$check=false;
if($useAccessWhiteList){
  foreach($accessWhiteList as $allowedKindom => $data){
    if(($allowedKindom == $kingdomName) && ($kingdomEmail == $data[0]) && ($kingdomX == $data[1]) && ($kingdomY == $data[2])){
      $check=true;
      break;
    }
  }
  if(!$check) debug('Something went wrong. Please contact the server admin.');
}

//Hunter version check
$hunter_ver = $_POST['hver'];
if (empty($hunter_ver) || ($neededHunterVersion < 12)) debug('Upgrade your Hunter extension.');


//read POST data
$tcap=(int)$_POST['tcap'];
if($tcap<=0) $tcap=60; else $tcap=$tcap*60;
$month=$_POST['m'];
$day=$_POST['d'];
$hour=$_POST['hour'];
$min=$_POST['min'];
//$sufix=$_POST['s'];
//TODO MAGNUS FIX THIS SHIT, DATE ERROR. Here the issue is with sufix, all the other values are recovered without problem.
//if (empty($month) || !isset($day) || !isset($hour) || !isset($min) || empty($sufix)) debug('date issue, mont is '.$month.', day is '.$day.', hour is '.$hour.', min is '.$min.', and sufix is '.$sufix.'');
if (empty($month) || !isset($day) || !isset($hour) || !isset($min)) debug('date');
$alliance=$_POST['ali'];
$dn = $_POST['name'];
$ds = $_POST['sts'];
$dt = $_POST['type'];
$dl = $_POST['land'];
$dw = $_POST['nw'];
$dh = $_POST['h'];

// build data
require("sector_class.php");
//$captureTime = new Skdate($month,$day,$hour,$min,$sufix); <== Tried to go forward without fixing suffix 8)
$captureTime = new Skdate($month,$day,$hour,$min);
$postSector = new Sector($alliance,$dn,$ds,$dt,$dl,$dw,$dh);




//now check if we are in our own secotor
$selfFound = false;
for($i=1;$i<$postSector->counter+1;$i++){
	if ($postSector->kingdoms[$i]['s'] == "you"){
		$selfFound = true;
		$selfKingdom = $postSector->kingdoms[$i]['n'];
	}
}

//prevent own sector page from being fed with random data. 
//Leave this until implemented on client side. 
if (($kingdomX == $galaxyX) && ($kingdomY == $galaxyY)) { 

  dropHunter();

} else {
//well check also if sector matches x/y Hunter client settings	
	if($selfFound){
		//woops
		//unset x/y from whileList if user exists - other wise its pub access anyway

		$accessWhiteList_clone = $accessWhiteList;
		foreach ($accessWhiteList_clone as $player => $values){
			if ($player == $selfKingdom){
				unset($accessWhiteList_clone[$player][1]);
				unset($accessWhiteList_clone[$player][2]);
				//unset($values[1]);
				//unset($values[2]);
			}
		}
			
		setVariables(array('useAccessWhiteList'=>$useAccessWhiteList,'accessWhiteList'=>$accessWhiteList_clone));
		
		
		
		//bye
		dropHunter();
	}
}








// class for sanitizing mysql querries
require("SafeSQL.class.php");
$safesql = new SafeSQL_MySQL;

// connect to database
$conn = mysql_connect($host,$user,$password) or debug("server fucked up on line 240");//failure();
mysql_select_db($database) or debug("server fucked up on line 241");//failure();

//prepare variables for storing our results 
$times = array();
$landchange = array();		
$nwchange = array();
$hchange = array();
$times_count=0;
$fetchTime=0;
$dateCap = $captureTime->int - $tcap;

// load sectors' data from database
$query_string = $safesql->query("SELECT * FROM %s WHERE (x=%i AND y=%i AND date > %i) ORDER by date", array($tableName,$galaxyX,$galaxyY,$dateCap));
$result=mysql_query($query_string) or die;//failure();
while($row = mysql_fetch_array( $result )) {
	$fetchSector = unserialize($row['data']);
	$fetchTime = $row['date'];
	$times[$times_count] = getDifference($fetchTime,$captureTime->int);

	for ($i=1;$i<count($dn)+1;$i++){
	   $landchange[$times_count][] = $postSector->kingdoms[$i]['l'] - $fetchSector->kingdoms[$i]['l'];
	   $nwchange[$times_count][] = $postSector->kingdoms[$i]['w'] - $fetchSector->kingdoms[$i]['w'];
	   $hchange[$times_count][] = $postSector->kingdoms[$i]['o'] - $fetchSector->kingdoms[$i]['o'];
        }
	$times_count++;
}

//write recieved sector data
if($captureTime->int > $fetchTime + $maxInterval){
  $query_string = $safesql->query("INSERT INTO %s (id,x,y,date,data) VALUES ('','%i','%i','%i','%s')", array($tableName,$galaxyX,$galaxyY,$captureTime->int,serialize($postSector)));
  $result=mysql_query($query_string) or debug("server fuck up on line 271");//failure();

  //Prune old sectors' data
  if(rand(0,90) == 0){
    $pruneCap = $captureTime->int - $maxHoursInDatabase*60;
    $query_string = $safesql->query("DELETE FROM `%s` WHERE date < %i", array($tableName,$pruneCap));
    $result=mysql_query($query_string) or debug("server fucked up on line 277");
    if (mysql_affected_rows() > 0) {
    $query_string = $safesql->query("OPTIMIZE TABLE `%s`", array($tableName));
      $result=mysql_query($query_string) or debug("server fucked up 280");
    }
  }
}
if($conn) mysql_close($conn);

//build response message
ob_end_clean();
echo('/*OK*/');
echoServerMesage($serverMSG,$serverMSGcolor,$MSGisLink,$MSGlinkURL);
echo('slotsNR=20;times = new Array(');
for ($k=0;$k<$times_count;$k++){
   echo('"'.$times[$k].'"');
   if($k!=$times_count-1)echo(',');
}
echo(");");
echo('landchange= new Array('.$times_count.');nwchange= new Array('.$times_count.');hchange = new Array('.$times_count.');');
for ($j=0;$j<$times_count;$j++){
	echo('landchange['.$j.']= new Array (20);');
	echo('nwchange['.$j.']= new Array (20);');
	echo('hchange['.$j.']= new Array (20);');
	for ($i=0;$i<21;$i++){	
		echo('landchange['.$j.']['.$i.']='.(int)$landchange[$j][$i].';');
		echo('nwchange['.$j.']['.$i.']='.(int)$nwchange[$j][$i].';');
		echo('hchange['.$j.']['.$i.']='.(int)$hchange[$j][$i].';');
	}
}
echo('/*EOT*/');
?>
