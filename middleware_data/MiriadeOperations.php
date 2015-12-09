<?php

set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__);

require_once("settings.php");

require_once(MD_FTPCONNECTION_DIR."/FTPConnection.php");
require_once(MD_PKGS_DIR."/PrestashopUpdate.php");
require_once(MD_LIBS_DIR."/FTPException.php");
require_once(MD_LIBS_DIR."/Logger.php");
require_once(MD_LIBS_DIR."/ErrorLogger.php");

class MiriadeOperations{

    private $logger = null;
    private $err_log = null;
    private $ftp_connection = null;
    private $update_prestashop = null;

    public function __construct(){
        $this->logger = new Logger();
        $this->err_log = new ErrorLogger();
        $this->ftp_connection = new FTPConnection();
        //$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__);
    }

    public function start(){
        $this->ftp_connection->connect();

        try{
            $result = $this->ftp_connection->handleSemaphore();
            if(!$result){
                $this->ftp_connection->revertCleanup();
                $this->logger->postMessage("Update process completed. No products have been processed.","INFO");
                return;
            }else if($result === "-1"){
                //no clean up operations here.
                $this->logger->postMessage("Update process completed. No products have been processed. (no cleanup)","INFO");
                return;
            }
        }catch(Exception $e){
            throw new FTPException("Connection is down. Revert process is completed.","ERROR");
        }

        try{
            $sems = $this->ftp_connection->getPSSemaphoresPath();

            $this->update_prestashop = new PrestashopUpdate();

            $this->update_prestashop->startUpdate(MD_LOG_FILE_DIR."/".$sems);
            $this->update_prestashop->firstStep();
            $this->logger->postMessage("Update $sems completed.","DEBUG");

        }catch(Exception $e){
            $this->err_log->postMessage($e);
            throw new Exception("Error exception. Please, see ErrorLog.txt in log_files directory.");
        }

        $this->ftp_connection->cleanUp();
        $this->logger->postMessage("Update process completed.","INFO");

    }

}

?>