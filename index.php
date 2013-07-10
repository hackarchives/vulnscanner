<?php
/*
   Copyright [2013] [DHRUV JAIN aka hackarchives]

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
include('db.php');
?>
<html>
	<head>
	<script type="text/javascript">
	var t = 0;
	var f = 0;
	var id;
	var save = 1;
	var scanname,scanurl;
	</script>
	<?php
	include('includes/header.php'); 
	
	?>
	<script type="text/javascript">	
	$(document).ready(function(){
	function updateavailablelinks()
		{
				$.post("post_handler/updatelinkstat.php",{pid:id},
				function(data)
				{
					$("#tlinks").html("<td>Total Links: </td><td>" + data + "</td>");
				}
			);
		}
		$("#next").click(function(){
			if(t === 0)
			{
				$("#scantitle").html("Enter The URL And Click Next");
				$("#scanname").hide();
				$("#scanurl").show();
				scanname = $("#scanname").val();
				t = 1;
			}
			else
			if(t == 1)
			{
				$("#scantitle").html("Select appropriate options and Click start");
				$("#scanurl").hide();
				scanurl = $("#url").val();
				$("#scanoptions").show();
				$("#next").removeAttr("value");
				$("#next").attr("value","Start");
				t = 2;
			}
			else
			if(t == 2)
			{
				if(f == 1)
				{
					$("#scanname").hide();
					$("#scrape").html("");
					scanname = $("#scanname").val();
				}
				else
				if(f == 2)
				{
					$("#scanurl").hide();
					$("#scrape").html("");
					scanurl = $("#url").val();
				}
				$("#scantitle").html("Initializing scanner...");
				$("#next").hide();
				$("#scanoptions").hide();
			$.post( 
             "post_handler/scan_saver.php",
             { name: scanname, url: scanurl },
             function(data) {
				if(data == "blankname")
				{
					$("#scrape").html("<p style=\"color:red\">Scan Name Can't be blank.Please enter a unique scan name</p>");
					f = 1;
					$("#scanname").show();
					t = 2;
					$("#next").show();					
				}
				else
				if(data == "blankurl")
				{
					$("#scrape").html("<p style=\"color:red\">Scan URL Can't be blank.Please enter a proper URL</p>");
					f = 2;
					$("#scanurl").show();
					t = 2;
					$("#next").show();
				}
				else
				if(data == "nameerror" && save == 1)
				{
					$("#scrape").html("<p style=\"color:red\">Scan Name Already Exists.Please enter a unique scan name</p>");
					f = 1;
					$("#scanname").show();
					t = 2;
					$("#next").show();
				}
				else 
				if(data == "noaccess"  && save == 1)
				{
					$("#scrape").html("<p style=\"color:red\">We are unable to access the URL entered.Please check again</p>");
					f = 2;
					$("#scanurl").show();
					t = 2;
					$("#next").show();
				}
				else
				{
				function getnextlink(){
							$.post( 
							 "post_handler/getscanid.php",
							 { name: scanname, url: scanurl },
							 function(data2) {
								id = data2;
								 $.post( 
								 "scraper.php",
								 {getnext: 1 , pid:id},
								 function(data3) {
								 if(data3 == 0)
								 {
									$("#scrape").append("Crawling process Completed.<br />Initiating Scan For SQL Injections<br />");
									return;
								 }
								 var tmp,tid,turl;
								 tmp = data3.split("id@sep#sep");
								 tid = tmp[0];
								 turl = tmp[1];							
									  $.post( 
									 "scraper.php",
									 {tempid:tid},
									 function(data4) {$("#scrape").html("Current Process: Crawling<br />Current URL: " + turl + "<br />");
												$("#scrape").append("Links Found: " + data4);
												updateavailablelinks();
										getnextlink();
									 });							 
								 });
							 });
							 
}			
					$.post( 
							 "post_handler/getscanid.php",
							 { name: scanname, url: scanurl },
							 function(data2) 
							 {
								id = data2;
								$.post("post_handler/scanpoint.php",{pid:id},function(data){if(data == 0) 
								{
									save = 1;	
								}
								else
								{
									save = 0;
								}
								});
								if(save == 1)
								{	
									$("#scanstatus").show();
									$("#scanstatus").append("<center><br /><h3>Scan Status</h3>&#10004;<table id=\"status\"><tr><td>Scan Name: </td><td>" + scanname + "</td></tr><tr><td>URL:</td><td>" + scanurl + "</td></tr></table></center>");
									$("#scrape").html("Scan name and URL registered in the database<br />Scanning " + scanurl + " to find server information and other links<br />");
													$.post( 
									"post_handler/getservinfo.php",
									{url: scanurl,pid:id },
									function(data) {
										if(data.hasOwnProperty("Server"))
										{
											$("#scrape").append("Server: " + data.Server + '<br />');
											$("#status tr:last").after("<tr><td>Server: </td><td>" + data.Server + "</td></tr>");
										}
										$("#scrape").append("Content-Type: " + data["Content-Type"] + '<br />');
										$("#status tr:last").after("<tr><td>Content-Type: </td><td>" + data["Content-Type"] + "</td></tr>");
										if(data.hasOwnProperty("X-Powered-By"))
										{
											$("#scrape").append("X-Powered-By: " + data["X-Powered-By"] + '<br />');
											$("#status tr:last").after("<tr><td>X-Powered-By: </td><td>" + data["X-Powered-By"] + "</td></tr>");
										}
										$("#status tr:last").after("<tr id=\"tlinks\"></tr>");
										$("#scrape").html("Current Process: Crawling<br />Current URL: " + scanurl + "<br />");
										$.post( 
											"scraper.php",
											{ name: scanname, url: scanurl },
											function(data) {
												$("#scrape").append("Links Found: " + data);
												updateavailablelinks();
										 getnextlink();
											});	
									},"json");	
								}
								else
								{
									$("#scrape").append("Resuming the process...");
									getnextlink();
								}
							}
						);					


				}
             });
		  
			}
		});
	});
	</script>
	</head>
	<body>
		<div id="logo"><img src="images/logo.png"></div>
		<div id="clear"></div>
		<div id="main"><h3 id="scantitle">Enter the name of the scan and click start</h3><div id="scrape">
		</div>
		<br /><br /><input type="text" id="scanname" name="scanname"><div id="scanurl"><input type="text" name="url" id="url"></div>
		<div id="scanoptions">Options like crawler settings, robots.txt settings etc will be here</div>
		<br /><br /><input type="button" value="Next" id="next">
		
		</div>
		
		<div id="sidebar"><div id="scanstatus">
		</div>
		<h3>Recent Scans</h3><br />
		<?php
			$q = $db->prepare("SELECT * FROM scan ORDER BY id DESC LIMIT 10");
			$q->execute();
			if($q->rowCount() == 0)
			{
				echo 'No previous scans found';
			}
			else
			{
				while($f = $q->fetch(PDO::FETCH_ASSOC))
				{
					$teid = $f['id'];
					echo '<a href="?scanid='.$teid.'" title="'.$f['url'].'">'.$f['name'].'</a><br />';
				}
			}
		?>
		</div>
		<?php
		if(isset($_GET['scanid']))
		{
			$id = $_GET['scanid'];
			$q = $db->prepare("SELECT * FROM scan WHERE id = ?");
			$q->execute(array($id));
			if($q->rowCount() != 0)
			{
				while($f = $q->fetch(PDO::FETCH_ASSOC))
				{
					$q2 = $db->prepare("SELECT * FROM scaninfo WHERE pid=?");
					$q2->execute(array($id));
					if($q2->rowCount() != 0)
					{
						while($f2 = $q2->fetch(PDO::FETCH_ASSOC))
						{
							$server = $f2['server'];
							$date = $f2['date'];
							$time = $f2['time'];
							$content = $f2['content'];
							$lang = $f2['lang'];
						}
					}
					?>
					<script type="text/javascript">				
					$(document).ready(function(){
						function updateavailablelinks()
						{
								$.post("post_handler/updatelinkstat.php",{pid:id},
								function(data)
								{
									$("#tlinks").html("<td>Total Links: </td><td>" + data + "</td>");
								}
							);
						}
					id = <?php echo $id ?>;
					scanname = "<?php echo $f['name']; ?>";
					$("#scanname").val(scanname);
					scanurl = "<?php echo $f['url'] ?>";
					$("#scanstatus").append("<center><br /><h3>Scan Status</h3><table id=\"status\"><tr><td>Scan Name: </td><td>" + scanname + "</td></tr><tr><td>URL:</td><td>" + scanurl + "</td></tr></table></center>");
					if("<?php echo $server ?>" != "NULL")
					{
						$("#status tr:last").after("<tr><td>Server: </td><td>" + "<?php echo $server ?>" + "</td></tr>");
					}
					$("#status tr:last").after("<tr><td>Content: </td><td>" + "<?php echo $content ?>" + "</td></tr>");
					if("<?php echo $lang ?>" != "NULL")
					{
						$("#status tr:last").after("<tr><td>Language: </td><td>" + "<?php echo $lang ?>" + "</td></tr>");
					}
					$("#status tr:last").after("<tr id=\"tlinks\"></tr>");
					updateavailablelinks();
					$("#scanstatus").show();
					$("#url").val(scanurl);					
					$('#next').trigger('click');
					$("#scanname").hide();
					$("#scanurl").show();
					save = 0;
					$('#next').trigger('click');
					$("#scanoptions").append("<br />Current Scan Name : " + scanname + "<br />Current Scan URL : " + scanurl);
					});
					</script>
					<?php
				}			
			}
		}
		?>
	</body>
</html>
