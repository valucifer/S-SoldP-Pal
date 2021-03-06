<?php
require_once("settings.php");
require_once("Analog.php");
require_once("settings.php");

class Logger{

    private $log_file = null;
    private $path = "";

    public function __construct(){
        $this->path = MD_ROOT."/log_files";
        if(!file_exists($this->path)){
            mkdir($this->path);
        }
        $this->log_file = $this->path."/Log.txt";
        if( file_exists($this->log_file) )
            if( filesize($this->log_file) >= MD_LOG_FILE_SIZE )
                unlink($this->log_file);
        Analog::handler (Analog\Handler\File::init ($this->log_file));
    }

    public function postMessage($message, $type = "DEBUG"){
        if(MD_LOGGER_LEVEL === 'DEBUG'){
            switch($type){
                case "DEBUG":
                    Analog::log ($message, Analog::DEBUG);
                    break;
                case "ERROR":
                    Analog::log ($message, Analog::ERROR);
                    break;
                case "WARNING":
                    Analog::log ($message, Analog::WARNING);
                    break;
                case "INFO":
                    Analog::log ($message, Analog::INFO);
                    break;
                default:
            }
        }else{
            switch($type){
                case "ERROR":
                    Analog::log ($message, Analog::ERROR);
                    break;
                case "WARNING":
                    Analog::log ($message, Analog::WARNING);
                    break;
                case "INFO":
                    Analog::log ($message, Analog::INFO);
                    break;
                default:
            }
        }
    }

}

?>