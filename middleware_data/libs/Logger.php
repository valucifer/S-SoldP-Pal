<?php

require_once("Analog.php");

class Logger{
    
    private $log_file = null;
    
    public function __construct(){
        $this->log_file = 'handleOperationsLog.txt';
        Analog::handler (Analog\Handler\File::init ($this->log_file));
    }
    
    public function postMessage($message, $type = "DEBUG"){
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
            default:
        }
        
    }
    
}

?>