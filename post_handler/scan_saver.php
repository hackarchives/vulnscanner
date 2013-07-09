<?php
if(isset($_POST['name']) && isset($_POST['url']))
{
  if($_POST['name'] == "")
	{
		echo "blankname";
	}
	else
	if($_POST['url'] == "")
	{
		echo "blankurl";
	}
	else
	{
include('../db.php');
		$q=$db->prepare("SELECT * FROM scan WHERE name=?");
		$q->execute(array($_POST['name']));
		if($q->rowCount() != 0)
		{
			echo "nameerror";
		}
		else
		{
			$url = $_POST['url'];
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_exec($ch);
			$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);	
			if($retcode == 200)
			{
				$q2 = $db->prepare("INSERT INTO scan (name,url) VALUES (?,?)");
				$q2->execute(array($_POST['name'],$_POST['url']));
			}
			else
			{
				echo "noaccess";
			}
		}
	}
}
?>
