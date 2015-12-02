<?php

require_once("Analog.php");
require_once("settings.php");

class ErrorLogger{

    private $log_file = null;

    public function __construct(){
        if(!file_exists("./log_files")){
            mkdir('./log_files');
        }
        $this->log_file = "./log_files/ErrorLog.txt";
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