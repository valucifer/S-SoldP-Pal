<?php

$connection = ftp_connect("ftp14.ilbello.com") or die("Couldn't connect to $ftp_server"); ;
//ftp_login($conn, "pjgroup", "3nRvgCMDYv");
//ftp_pasv($conn, true);
//ftp_chdir($conn, "./");

// try to login
if (@ftp_login($connection, "pjgroup", "3nRvgCMDYv")) {
    echo "Connected as $ftp_user@$ftp_server\n";
} else {
    echo "Couldn't connect as $ftp_user\n";
}

// close the connection
ftp_close($conn_id);  

/*//post functions
$files = ftp_nlist($conn, ".");
srand ((float)microtime()*1000000);
shuffle($files);

//get functions
$filetoget = array_pop($files);
ftp_get($conn, $filetoget, $filetoget, FTP_BINARY);
ftp_close($conn);*/

?>