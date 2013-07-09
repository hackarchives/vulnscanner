<?php
if(file_exists("lock"))
{
  echo '<meta http-equiv="Refresh" content="0; url=install.php">';
	exit;
}
include('db.php');
?>
<html>
	<head>
	<?php include('includes/header.php'); ?>
	<script type="text/javascript">
	
	$(document).ready(function(){
	var t = 0;
	var f = 0;
	var id;
	var scanname,scanurl;
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
			if(t === 1)
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
				if(data == "nameerror")
				{
					$("#scrape").html("<p style=\"color:red\">Scan Name Already Exists.Please enter a unique scan name</p>");
					f = 1;
					$("#scanname").show();
					t = 2;
					$("#next").show();
				}
				else 
				if(data == "noaccess")
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
									return;
								 }
								 var tmp,tid,turl;
								 tmp = data3.split("id@sep#sep");
								 tid = tmp[0];
								 turl = tmp[1];
								 $("#scrape").append("Scanning " + turl + " to find other links<br />");	
									  $.post( 
									 "scraper.php",
									 {tempid:tid},
									 function(data4) {
										$("#scrape").append(data4 + " links found and added to scanning list<br />");
										getnextlink();
									 });							 
								 });
							 });
							 
}			
					$.post( 
							 "post_handler/getscanid.php",
							 { name: scanname, url: scanurl },
							 function(data2) {
								id = data2;
					$("#scrape").html("Scan name and URL registered in the database<br />Scanning " + scanurl + " to find server information and other links<br />");
									$.post( 
					"post_handler/getservinfo.php",
					{url: scanurl,pid:id },
					function(data) {
						$("#scrape").append("Server: " + data.Server + '<br />');
						$("#scrape").append("Content-Type: " + data["Content-Type"] + '<br />');
						if(data.hasOwnProperty("X-Powered-By"))
						{
							$("#scrape").append("X-Powered-By: " + data["X-Powered-By"] + '<br />');
						}
						$.post( 
							"scraper.php",
							{ name: scanname, url: scanurl },
							function(data) {
								$("#scrape").append(data + " links found and added to scanning list<br />");
						 getnextlink();
							});	
					},"json");	});					


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
		<div id="sidebar">
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
					echo '<a href="#" title="'.$f['url'].'">'.$f['name'].'</a><br />';
				}
			}
		?>
		</div>
	</body>
</html>