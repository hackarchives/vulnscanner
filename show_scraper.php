<?php
include('includes/header.php');
?>
   <script type="text/javascript" language="javascript">
  $(document).ready(function() {
      $("#start").click(function(){
          $.get( 
             "scraper.php",
             { url: $("#url").val() },
             function(data) {
                $('#scan').html(data);
             }

          );
      });
   });
   </script>
   <div id="scan">
   </div>
   <input type="text" id="url">
   <input type="button" id="start" value="Start Scan">
