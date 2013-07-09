<?php
if(isset($_POST['url']))
{
  $url = $_POST['url'];
	$id = $_POST['pid'];
	$headers = get_headers($url, 1);
	include('../db.php');
	$date = date("d-m-Y");
	$time = date("H:i:s");
	if(array_key_exists('X-Powered-By', $headers)) 
	{
		$q = $db->prepare("INSERT INTO scaninfo (pid,date,time,server,lang,content) VALUES (?,?,?,?,?,?)");
		$q->execute(array($id,$date,$time,$headers["Server"],$headers["X-Powered-By"],$headers["Content-Type"]));
	}
	else
	{
		$q = $db->prepare("INSERT INTO scaninfo (pid,date,time,server,lang,content) VALUES (?,?,?,?,?,?)");
		$q->execute(array($id,$date,$time,$headers["Server"],"NULL",$headers["Content-Type"]));		
	}
	$t = json_encode($headers);
	echo $t;
}
?>
