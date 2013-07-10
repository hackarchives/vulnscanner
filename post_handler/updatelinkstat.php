<?php
if(isset($_POST['pid']))
{
  include('../db.php');
	$id = $_POST['pid'];
	$q = $db->prepare("SELECT * FROM spider_url WHERE pid = ?");
	$q->execute(array($id));
	echo $q->rowCount();
}
?>
