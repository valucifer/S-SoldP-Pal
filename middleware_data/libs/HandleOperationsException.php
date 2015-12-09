<?php
require_once("settings.php");
require_once("Analog.php");
require_once("HandleFilesFolder.php");

interface IException
{
    /* Protected methods inherited from Exception class */
    public function getMessage();                 // Exception message
    public function getCode();                    // User-defined Exception code
    public function getFile();                    // Source filename
    public function getLine();                    // Source line
    public function getTrace();                   // An array of the backtrace()
    public function getTraceAsString();           // Formated string of trace

    /* Overrideable methods inherited from Exception class */
    public function __toString();                 // formated string for display
    public function __construct($message = null, $code = 0);
}

class HandleOperationsException extends Exception implements IException{

    private $log_file = null;

    public function __construct($message = "Unknown error", $code = 0){
        if(!file_exists(MD_LOG_FILE_DIR)){
            mkdir(MD_LOG_FILE_DIR);
        }
        $this->log_file = MD_LOG_FILE_DIR.'/Log.txt';
        if (!$message) 
            throw new $this('Unknown '. get_class($this));
        Analog::handler (Analog\Handler\File::init ($this->log_file));
        if(gettype($message) !== "string"){
            $message = "Unknown error";
        }
        parent::__construct($message, 10);
        Analog::log ($this->__toString(), Analog::ERROR);
        $handleFilesFolder = new HandleFilesFolder();
        $handleFilesFolder->handle();
        if( file_exists(MD_ROOT."/FTP_SEMAMPHORE.smph") ){
            unlink(MD_ROOT."/FTP_SEMAMPHORE.smph");
        }
    }

    public function __toString(){
        return get_class($this) . " '{$this->message}' in {$this->file}(line:{$this->line})\n"
            . "{$this->getTraceAsString()}";
    }

}

?>