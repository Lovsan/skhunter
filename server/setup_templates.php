<?php
function createEnumSet($min,$max){
  $set="(";
  for ($j=$min;$j<=$max;$j++){
    $set.="'".$j."'";
    if ($j!=$max) $set.=",";
  }
  $set.=")";
  return $set;
}
function setVariables($data){
  $buffer=file("hunter_config.php");
  $dataFile = fopen( "hunter_config.php", "w+" ) ;
  foreach($buffer as $lineNR=>$line){
      foreach($data as $variable=>$value){
        $pattern="/^\\$".$variable."=/";
        if(preg_match($pattern, $line)){
          if(is_string($value)){
            $buffer[$lineNR]="$".$variable."=\"".$value."\";\n";
          } else if (is_array($value)) {
            $_value=var_export($value,ture);
            $_value=preg_replace('/\n/','',$_value);
            $buffer[$lineNR]="$".$variable."=".$_value.";\n";
          } else if (is_int($value)) {
            $buffer[$lineNR]="$".$variable."=".$value.";\n";
          } else if (is_bool($value)) {
            $buffer[$lineNR]="$".$variable."=".(int)$value.";\n";
          }

        }
      } 
      if (fwrite($dataFile, $buffer[$lineNR]) === FALSE) {
        debugSQL("Cannot write to file ('hunter_config.php')");
        exit;
      }
  }
}


function debugSQL($msg,$title="warning"){
echo "
 <table class='".$title."'>
 <tr><th>".$title.":</th><td>".$msg."</td><tr>
 </table>
";
}


function doSQLTable($tableName,$x_min=0,$x_max=0,$y_min=0,$y_max=0){
  if($x_min > 0) {
  $query_string="
CREATE TABLE `%s` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `x` enum".createEnumSet($x_min,$x_max)." NOT NULL default '1',
  `y` enum".createEnumSet($y_min,$y_max)." NOT NULL default '1',
  `date` mediumint(7) unsigned NOT NULL default '1',
  `data` text,
  PRIMARY KEY  (`id`)
);
";
 } else if ($x_min < 0) {
  $query_string="DELETE FROM `%s`";
 } else {
  $query_string="DROP TABLE `%s` ";
 }
  require_once("SafeSQL.class.php");
  $safesql = new SafeSQL_MySQL;
  $query_string = $safesql->query($query_string, array($tableName));
  ob_start();
  if(mysql_query($query_string)){
    ob_end_clean();
    debugSQL($query_string,$title="success");
    return true;
  } else {
    ob_end_clean();
    debugSQL(mysql_errno().": ".mysql_error());
    return false;
  }

}

function echoHeader(){
echo "
<html>
<head>
<title>SK HUNTER SERVER SETUP</title>
<link href='a_main.css' rel='stylesheet' type='text/css'>
</head>
<body>
";
}


		//<tr><th colspan='2'>Access Logs</th><th colspan='2'>SK HUNTER constants</th></tr>	
		//<tr><td>kingdom name</td><td>IP</td><td>last access</td></tr>

		
function echoAccessLog($accessLogTable = NULL,$database = "",$user = "",$password = "",$host = ""){
	if (isset($accessLogTable)){
	} else {
		echo " 
		<form action='".$_SERVER['PHP_SELF']."' method='POST'>	
		<button  type='submit' name='command' value='createAccessLog' title=''>ENABLE ACCESS LOG</button>
		</form>
		";		
	}
}

function echoDatabaseSettings($database,$user,$password,$host,$tableName,$x_min,$x_max,$y_min,$y_max,$server,$databaseRunning,$MySQLDatabase,$MySQLUser){

if($server == "s1"){
  $selected_s1="selected";
  $selected_s2="";
} else {
  $selected_s1="";
  $selected_s2="selected";
}

$disabled = ($databaseRunning) ? "disabled " : "";

if (!$MySQLUser || !$MySQLDatabase || $databaseRunning) {
$disabled2 = "disabled ";
} else {
$disabled2 = "";
}

$disabled3 = ($MySQLUser) ? "" : "disabled ";

echo " 
<form action='".$_SERVER['PHP_SELF']."' method='POST'>
<table class='complex'>
<tr><th colspan='2'>MySQL database setting</th><th colspan='2'>SK HUNTER constants</th></tr>
<tr><td>database</td><td><input title='Database name. You must create a new database or if you have one you must enter the name here.' ".$disabled."name='database[0]' type='text' value='".$database."'/></td>
<td>table name: <input ".$disabled." title='If you have connected to your database you must invent a table name. Once it is created do not change it.' class='fr' size='5' name='database[4]' type='text' value='".$tableName."'/></td><td> </td>
<td colspan='1'><button  class='fr' ".$disabled3." type='submit' name='command' value='deleteData' title='Resets all sector data to zero. Database will continue working normally.'>PURGE <br>SECTOR SCANS</button></td>
</tr>
<tr><td>user</td><td><input title='Database username. You should get this from your host.' ".$disabled." name='database[1]' type='text' value='".$user."'/></td>
<td>Galaxy range:  min <input class='fr' ".$disabled." title='please make sure this settings match the game parameters otherwise database may corrupt' size='5' name='database[5]' type='text' value='".$x_min."'/></td>
<td title='please make sure this settings match the game parameters otherwise database may corrupt' class='small'>max <input size='5' class='small' ".$disabled."  name='database[6]' type='text' value='".$x_max."'/></td></tr>
<tr><td>password</td><td><input title='Database password. You should should get this from your host.' ".$disabled." name='database[2]' type='text' value='".$password."'/></td>
<td>Sector range:  min <input class='fr' ".$disabled." title='please make sure this settings match the game parameters otherwise database may corrupt' size='5' name='database[7]' type='text' value='".$y_min."'/></td><td class='small'>max <input size='5' class='small' ".$disabled."  name='database[8]' type='text' value='".$y_max."'/></td></tr>
<tr><td>host</td><td><input title='Your database IP or name. You should get this from your host.' ".$disabled." name='database[3]' type='text' value='".$host."'/></td><td>SK server:<select class='fr' ".$disabled."name='database[9]'><option value='s1' ".$selected_s1.">server 1</option><option value='s2' ".$selected_s2.">server 2</option></select></td></tr>
<tr><td colspan='1'><button  ".$disabled." type='submit'>save changes</button></td>
";

if (!$MySQLDatabase || $databaseRunning) {
$disabled = "disabled ";
} else {
$disabled = "";
}

echo "
<td colspan='1'><button ".$disabled." type='submit' name='command' value='createTable'>CREATE NEW<br>TABLE IN DATABASE.</button></td>
<td align='center' colspan='2'><button title='All data will be lost and players will not be able to connect.' ".$disabled2." type='submit' name='command' value='deleteTable'>DELETE TABLE <br>FROM DATABASE</button></td>
";

if($databaseRunning){
echo "
<td colspan='1'><button type='submit' name='command' value='toggleAdvancedSettings' title='Security switch for advanced database operation. Enable this to make changes to the database settings.'>ENABLE <br>ADVANCED SETTINGS </button></td>
";
} else {
echo "
<td colspan='1'><button type='submit' name='command' value='toggleAdvancedSettings' title='Security switch for advanced database operations. Lock changes to the database settings.'>LOCK <br>ADVANCED SETTINGS </button></td>
";
}

echo "
</tr>
</table>
</form>
";
}

function echoHunterSettings($maxHoursInDatabase,$serverMSG,$serverMSGcolor,$HNPassword,$useAccessWhiteList,$accessWhiteList,$MSGisLink,$MSGlinkURL){
if($MSGisLink) {
  $checkbox=" checked";
} else {
  $checkbox="";
}
echo "
<form action='".$_SERVER['PHP_SELF']."' method='POST'>
<table class='complex'>
<tr><th colspan='4'>SK HUNTER run-time settings</th></tr>
<tr><td>network access password</td><td><input  class='big' name='hunter[0]' type='text' value='".$HNPassword."'/></td><td >store data up to </td><td><input class='small' name='hunter[1]' type='text' value='".$maxHoursInDatabase."'/> hours</td></tr>
<tr><td>server message</td><td><input class='big' name='hunter[2]' type='text' value='".$serverMSG."'/></td><td>server message color</td><td><input class='small' name='hunter[3]' type='text' value='".$serverMSGcolor."'/></td></tr>
<tr><td colspan='2'></td><td style='background-color:black;color:".$serverMSGcolor."'>current color</td><tr>
<tr><td><input ".$checkbox." style='width:2em;' type='checkbox' name='link'>server message is link? URL: </input></td><td><input class='big' name='linkURL' type='text' value='".$MSGlinkURL."'/></td></tr>
<tr><td colspan='2'><button type='submit' name='hunterAction' value='save'>save changes</button></td></tr>
</table>
</form>
";
//
if($useAccessWhiteList) {
  $disabled="enabled";
  $enable="disable";
  $commValue="disableAccessList";
  $displayLike="info";
} else {
  $disabled="disabled";
  $enable="enable";
  $commValue="enableAccessList";
  $displayLike="warning";
} 
echo "
<form action='".$_SERVER['PHP_SELF']."' method='POST'>
<table class='complex'>
<tr><th colspan='5'>SK HUNTER network access whitelist</th></tr>
<tr><td ><button type='submit' name='access' value='".$commValue."'/>".$enable." access white list</button></td><td>kingdom name</td><td>e-mail (or unique<br>presonal password)</td><td>Galaxy</td><td>Sector</td></tr>
<tr><td colspan='3' class='".$displayLike."'>access white list is ".$disabled."<td></tr>
";
$i=0;
if(!empty($accessWhiteList)){
  foreach ($accessWhiteList as $userName => $userData){
    echo "
<tr>
<td><input style='width:2em;' type='checkbox' name='affectedUsers[".$i."]'> select user</input></td>
<td><input name='accessName[".$i."]' type='text' value='".$userName."'/></td>
<td><input class='big' name='accessPass[".$i."]' type='text' value='".$userData[0]."'/></td>
<td><input class='small' name='accessX[".$i."]' type='text' value='".$userData[1]."'/></td>
<td><input class='small' name='accessY[".$i."]' type='text' value='".$userData[2]."'/></td>
</tr>
         ";
    $i++;
  }
}
echo "
<tr>
<td><button type='submit' name='access' value='save'>save changes</button></td>
<td ><button type='submit' name='access' value='new'/>add new user</button></td>
<td></td>
<td></td>
<td><button type='submit' name='access' value='delete'/>delete selected users</button></td>
</tr>
<tr>
<td>e-mail title: </td>
<td colspan='4'><input title='requires email server of your hosting plan' name='messageHeader' type='text' size=80 value='SK Hunter Admin Email'></td>
</tr>
<tr>
<td>e-mail message: </td><td><button title='requires email server of your hosting plan' type='submit' name='access' value='mail'>send email to selected users</button></td>
</tr>
<tr>
<td colspan='5'><textarea name='messageBody' rows='4' cols='80'>Hello, your Hunter has been activated.</textarea></td>
</tr>
</table>
</form>
";
}

function echoNewPassword($msg="",$adminPassword="",$adminEmail=""){
global $adminPassword;
echo "
<form action='".$_SERVER['PHP_SELF']."' method='POST'>
<table>
<tr><th colspan='2'>SK HUNTER admin password setting</th></tr>
<tr><td >".$msg."</td><td><input name='setAdminPassword' type='text' value='".$adminPassword."'/></td></tr>
<tr><td >admin e-mail (optional)</td><td><input name='setAdminEmail' type='text' value='".$adminEmail."'/></td></tr>
<tr><td colspan='2'><button type='submit'>save changes</button></td></tr>
</table>
</form>
";
}

function echoAdminLogin($msg="Enter Password:"){
echo "
<form action='".$_SERVER['PHP_SELF']."' method='POST'>
<table>
<tr><th colspan='2'>SK HUNTER admin login</th></tr>
<tr><td>".$msg."</td><td><input name='LoginAdminPassword' type='text' /></td></tr>
<tr><td colspan='2'><button type='submit'>submit</button></td></tr>
</table>
</form>
";
}

function echoConfirm($command,$msg,$param=""){
echo "
<form action='".$_SERVER['PHP_SELF']."' method='POST'>
<table class='warning'>
<tr><th colspan='2'>Confirmation:</th></tr>
<tr><td>".$msg."</td></tr>
<tr>
<td><button type='submit' name='confirm' value='".$command."'>YES</button></td>
<td><button type='submit' name='confirm' value='no'>NO</button></td>
<input type='hidden' name='command' value='".$command."'/>
</tr>
";
if(isset($param)) {
  echo "
  <input type='hidden' name='commandParam' value='".$param."'/>
";
}
echo "
</table>
</form>
";
}

function echoLogout(){
echo "
<form action='".$_SERVER['PHP_SELF']."' method='POST'>
<table>
<tr><td><input type='submit' name='logout' value='logout'\></tr></td>
</table>
</form>
";
}

function echoFooter(){
echo "
</body>
</html>
";
}
