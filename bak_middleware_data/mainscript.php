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
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/settings');


require_once("FTPConnection.php");
require_once("PrestashopUpdate.php");
require_once("settings.php");
require_once("FTPException.php");
require_once("Logger.php");
require_once("ErrorLogger.php");

$logger = new Logger();
$error_log = new ErrorLogger();
$ftp_connection = new FTPConnection();
$ftp_connection->connect();
try{
    $result = $ftp_connection->handleSemaphore();
    if(!$result){
        $ftp_connection->revertCleanup();
        $logger->postMessage("Update process completed. No products have been processed.","INFO");
        return;
    }else if($result === "-1"){
        //no clean up operations here.
        $logger->postMessage("Update process completed. No products have been processed. (no cleanup)","INFO");
        return;
    }
}catch(Exception $e){
    throw new FTPException("Connection is down. Revert process is completed.","ERROR");
}

try{
    $sems = $ftp_connection->getPSSemaphoresPath();

    $update_prestashop = new PrestashopUpdate();
    
    $update_prestashop->startUpdate("./files/".$sems);
    $update_prestashop->firstStep();
    $logger->postMessage("Update $sems completed.","DEBUG");
    
}catch(Exception $e){
    $error_log->postMessage($e);
    throw new Exception("Error exception. Please, see ErrorLog.txt in log_files directory.");

}

$ftp_connection->cleanUp();
$logger->postMessage("Update process completed.","INFO");

?>