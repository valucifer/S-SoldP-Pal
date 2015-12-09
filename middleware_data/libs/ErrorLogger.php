<?php

require_once("settings.php");
require_once("Analog.php");

class ErrorLogger{

    private $log_file = null;

    public function __construct(){
        if(!file_exists(MD_LOG_FILE_DIR)){
            mkdir(MD_LOG_FILE_DIR);
        }
        $this->log_file = MD_LOG_FILE_DIR."/ErrorLog.txt";
        if( file_exists($this->log_file) )
            if( filesize($this->log_file) >= MD_LOG_FILE_SIZE )
                unlink($this->log_file);
        Analog::handler (Analog\Handler\File::init ($this->log_file));
    }

    public function postMessage($message){
        Analog::log ($message, Analog::ERROR);
    }

}

?>