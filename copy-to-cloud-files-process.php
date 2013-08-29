<?php

// get name of progress file. 
    $progress = $_GET['progress'];


    $cmd = "php copy-to-cloud-files-worker.php $progress";
    $pipe = popen($cmd, 'r');

    if (empty($pipe)) {
    throw new Exception("Unable to open pipe for command '$cmd'");
    }

    stream_set_blocking($pipe, false);

    while (!feof($pipe)) {
    fread($pipe, 10240);
    sleep(1);

    flush();
    }


    pclose($pipe);


?>



