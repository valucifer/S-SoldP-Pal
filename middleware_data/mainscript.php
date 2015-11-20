<?php

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs/color_comparison');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs/db_manager');
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/ps_manager');
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/image_checker');
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/ftp_connection');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/libs');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/libs/color_lib');
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/files');


require_once("FTPConnection.php");
require_once("PrestashopUpdate.php");
$ftp_connection = new FTPConnection();
$ftp_connection->connect();
$ftp_connection->handleSemaphore();
$sems = $ftp_connection->getPSSemaphoresPath();
$update_prestashop = new PrestashopUpdate();
foreach ($sems as $sem){
    echo"SAM $sem";
    $update_prestashop-> startUpdate("./files/".$sem);
    $update_prestashop-> updatePsProduct();
}
$ftp_connection->cleanUp();

?>