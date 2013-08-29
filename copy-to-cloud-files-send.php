<?php
// generate hash to create progress file
$progress_hash = substr(hash("sha512",rand()),0,12); // Reduces the size to 12 chars
$progress_file = "copy-to-cloud-files-" .$progress_hash. ".php";
?>

<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script type="text/javascript">
  function checkForData ( ) {
    $.post('<?php echo $progress_file; ?>',false,function(data){
      if(data.length){
        // Display the current progress
        document.getElementById('progress').innerHTML = data;
      }else{
        // No need to show anything if there isn't anything happening
      }
    });
  }
  // Start the timer when the page is done loading:
  $(function(){
    // First Check 
    checkForData();
// Start Timer
    var refreshIntervalId = setInterval('checkForData()',1000); // 1 Second Intervals

$.post('copy-to-cloud-files-process.php?progress=<?php echo $progress_hash; ?>', function(data) {
  $('.result').text(data);
clearInterval(refreshIntervalId);

});

  });
</script>
<link href="css/style.css" rel="stylesheet" type="text/css">
<span id="progress"></span>


