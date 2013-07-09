<?php
if(isset($_POST['pid']))
{
  $id = $_POST['pid'];
	include('../db.php');
	$q = $db->prepare("SELECT * FROM scan WHERE id=?");
	$q->execute(array($id));
	if($q->rowCount() != 0)
	{
		while($f = $q->fetch(PDO::FETCH_ASSOC))
		{
			$status = $f['started'];
			echo $status;
		}
	}
}
?>
