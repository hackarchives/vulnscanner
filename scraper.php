<?php
include('db.php');
	include('dom.php');
if(isset($_POST['name']) && isset($_POST['url']))
{
	$url = $_POST['url'];
	$name = $_POST['name'];
	$url = str_replace("'","",$url);
	$q = $db->prepare("SELECT * FROM scan WHERE url=? AND name=?");
	$q->execute(array($url,$name));
	while($f = $q->fetch(PDO::FETCH_ASSOC))
	{
		$id = $f['id'];
	}
	$realurl = parse_url($url);
	$html = file_get_html($url);
	$ignore = array("#","/","./","javascript:void(0)","javascript:void()");
	$count = 0;
	$list = array();
	foreach($html->find('a') as $link)
	{
		if(!in_array($link->href,$ignore) && $link->href[0] != "#")
		{
			if(strpos($link->href,"http://") !== FALSE || strpos($link->href,"https://") !== FALSE)
			{
				if(parse_url($link->href)['host'] == $realurl['host'])
				{
					if($link->href != $url)
					{
						if(!in_array($link->href,$list))
						{
							$list[$count] = $link->href;
							$count++;
						}	
					}
				}
			}
			else
			{	
				if(strpos($link->href,"mailto:") === FALSE)
				{
					$tmp = $realurl['scheme'].'://'.$realurl['host'];
					if($link->href[0] != "/")
					{
						$tmp .= "/";
					}
					$tmp .= $link->href;
					if($tmp != $url)
					{
						if(!in_array($tmp,$list))
						{
							$list[$count] = $tmp;
							$count++;
						}
					}
				}
			}
			
		}
	}
	$count = 0;
	foreach($list as $item)
	{
		$q = $db->prepare("SELECT * FROM spider_url WHERE pid = ? AND url=?");
		$q->execute(array($id,$item));
		if($q->rowCount() == 0)
		{
			$q2 = $db->prepare("INSERT INTO spider_url (pid,url,status) VALUES (?,?,?)");
			$q2->execute(array($id,$item,0));
			$count++;
		}
	}
	$q = $db->prepare("UPDATE scan SET started=1 WHERE id=?");
	$q->execute(array($id));
	echo $count;
}
else
if(isset($_POST['getnext']) && isset($_POST['pid']))
{
	$q = $db->prepare("SELECT * FROM spider_url WHERE pid = ? AND status=0 LIMIT 1");
	$q->execute(array($_POST['pid']));
	if($q->rowCount()== 0)
	{
		echo 0;
	}
	else
	{
		while($f = $q->fetch(PDO::FETCH_ASSOC))
		{
			echo $f['id'].'id@sep#sep'.$f['url'];
		}
	}
}
else
if(isset($_POST['tempid']))
{
	$st = rand(0,10);
	sleep($st);
	$q = $db->prepare("SELECT * FROM spider_url WHERE id=?");
	$q->execute(array($_POST['tempid']));
	while($f = $q->fetch(PDO::FETCH_ASSOC))
	{
		$url = $f['url'];
		$pid = $f['pid'];
	}
	$realurl = parse_url($url);
	$html = file_get_html($url);
	$ignore = array("#","/","./","javascript:void(0)","javascript:void()");
	$count = 0;
	$list = array();
	foreach($html->find('a') as $link)
	{
		if(!in_array($link->href,$ignore) && $link->href[0] != "#")
		{
			if(strpos($link->href,"http://") !== FALSE || strpos($link->href,"https://") !== FALSE)
			{
				if(parse_url($link->href)['host'] == $realurl['host'])
				{
					if($link->href != $url)
					{
						if(!in_array($link->href,$list))
						{
							$list[$count] = $link->href;
							$count++;
						}	
					}
				}
			}
			else
			{			
				$tmp = $realurl['scheme'].'://'.$realurl['host'];
				if($link->href[0] != "/")
				{
					$tmp .= "/";
				}
				$tmp .= $link->href;
				if($tmp != $url)
				{
					if(!in_array($tmp,$list))
					{
						$list[$count] = $tmp;
						$count++;
					}
				}
			}
			
		}
	}
	$count = 0;
	foreach($list as $item)
	{
		$q = $db->prepare("SELECT * FROM spider_url WHERE pid = ? AND url=?");
		$q->execute(array($pid,$item));
		if($q->rowCount() == 0)
		{
			$q2 = $db->prepare("INSERT INTO spider_url (pid,url,status) VALUES (?,?,?)");
			$q2->execute(array($pid,$item,0));
			$count++;
		}
	}
	$q = $db->prepare("UPDATE spider_url SET status=1 WHERE id=?");
	$q->execute(array($_POST['tempid']));
	echo $count;
}
?>
