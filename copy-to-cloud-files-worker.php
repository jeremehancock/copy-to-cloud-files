<?php

// specify namespace
   namespace OpenCloud;

require_once("copy-to-cloud-files-config.php");

$date = date("M-d-Y-h:i:s");

$cur_dir = explode('/', getcwd());
$container = $url. "-" .$cur_dir[count($cur_dir)-1]. "-" . $date;

// get name of progress file. This will keep on demand backuups from colliding with auto backups
   $progress = $argv[1];
   $progress_file = "copy-to-cloud-files-" .$progress. ".php";

// update progress file
   file_put_contents($progress_file,"Setting up API Connection<br/>");
// sleep for 1 seconds.
   sleep(1);

shell_exec('wget https://github.com/jeremehancock/php-opencloud/archive/master.zip --no-check-certificate -O copy-to-cloud-files-api.zip; unzip copy-to-cloud-files-api.zip; mv php-opencloud-master copy-to-cloud-files-api; rm copy-to-cloud-files-api.zip');

// Set API Timeout
   define('RAXSDK_TIMEOUT', '3600');

// require Cloud Files API
   require_once("./copy-to-cloud-files-api/lib/php-opencloud.php");

// update progress file
   file_put_contents($progress_file,"Connecting to Cloud Files<br/>");
// sleep for 1 seconds.
   sleep(1);

try {
      define('AUTHURL', 'https://identity.api.rackspacecloud.com/v2.0/');
      $mysecret = array('username' => $username,'apiKey' => $key);

// establish our credentials
      $connection = new Rackspace(AUTHURL, $mysecret);

// now, connect to the ObjectStore service
      $ostore = $connection->ObjectStore('cloudFiles', "ORD");
   }

   catch (HttpUnauthorizedError $e) {
      echo "Failed to connect!";
}

// update progress file
   file_put_contents($progress_file,"Creating Container -- $container<br/>", FILE_APPEND);
// sleep for 1 seconds.
   sleep(1);

// create container if it doesn't already exist
   $cont = $ostore->Container();
   $cont->Create(array('name'=>"$container"));


// send backup to Cloud Files
$path = ".";

if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
        if ('.' === $file) continue;
        if ('..' === $file) continue;
        if ('copy-to-cloud-files-worker.php' === $file) continue;
        if ('copy-to-cloud-files-process.php' === $file) continue;
        if ('copy-to-cloud-files-setup.php' === $file) continue;
        if ('copy-to-cloud-files-config.php' === $file) continue;
        if ("$progress_file" === $file) continue;
        if ('copy-to-cloud-files-send.php' === $file) continue;
        if (is_dir($file)) continue;
        if ((filesize($file) > 5368709120)) {
   file_put_contents($progress_file,"$file is too big for transfer!<br/>", FILE_APPEND);
// sleep for 1 seconds.
   sleep(1);
   continue;
}

// update progress file
   file_put_contents($progress_file,"Sending $file...<br/>", FILE_APPEND);
// sleep for 1 seconds.
   sleep(1);
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$type = finfo_file($finfo, $file);
   $md5 = md5_file($file);
   $obj = $cont->DataObject();
   $obj->Create(array('name' => "$file", 'content_type' => "$type"), $filename="$file");
   $etag = $obj->hash;
if ($md5 != $etag) {
   file_put_contents($progress_file,"&nbsp;&nbsp;&nbsp;&nbsp;$file failed transfer!<br/>", FILE_APPEND);
   $obj->Delete(array('name'=>"$file"));
// sleep for 1 seconds.
   sleep(1);
}

else {
   file_put_contents($progress_file,"&nbsp;&nbsp;&nbsp;&nbsp;$file sent succesfully<br/>", FILE_APPEND);
// sleep for 1 seconds.
   sleep(1);
}

    }
    closedir($handle);
}

// update progress file
   file_put_contents($progress_file,"Done!<br/>", FILE_APPEND);
      sleep(3);
      shell_exec("rm $progress_file");  
      shell_exec("rm copy-to-cloud-files-worker.php");
      shell_exec("rm copy-to-cloud-files-process.php");
      shell_exec("rm copy-to-cloud-files-send.php");
      shell_exec("rm copy-to-cloud-files-setup.php");
      shell_exec("rm copy-to-cloud-files-config.php");
      shell_exec("rm -rf ./copy-to-cloud-files-api");

?>

