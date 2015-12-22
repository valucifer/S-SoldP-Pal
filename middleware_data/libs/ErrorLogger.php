<?php

require_once("settings.php");
require_once("Analog.php");

class ErrorLogger{

    private $log_file = null;
    private $path = "";

    public function __construct(){
        $this->path = MD_ROOT."/log_files";
        if(!file_exists($this->path)){
            mkdir($this->path);
        }
        $this->log_file = $this->path."/ErrorLog.txt";
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