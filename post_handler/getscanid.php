<?php
include('../db.php');
if(isset($_POST['name']) && isset($_POST['url']))
{
  $q = $db->prepare("SELECT * FROM scan WHERE name=? AND url=?");
	$q->execute(array($_POST['name'],$_POST['url']));
	while($f = $q->fetch(PDO::FETCH_ASSOC))
	{
		echo $f['id'];
	}
}
?>
