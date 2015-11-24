<?php
require_once("Analog.php");

interface IFTPException
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

class FTPException extends Exception implements IFTPException{
    
    private $log_file = null;

    public function __construct($message = null, $code = 0){
        $this->log_file = './log_files/Log.txt';
        if (!$message) 
            throw new $this('Unknown '. get_class($this));
        Analog::handler (Analog\Handler\File::init ($this->log_file));
        parent::__construct($message, $code);
        Analog::log ($this->__toString(), Analog::ERROR);
        if( file_exists("FTP_SEMAMPHORE.smph") )
            unlink("FTP_SEMAMPHORE.smph");
    }
    
    public function __toString(){
        return get_class($this) . " '{$this->message}' in {$this->file}(line:{$this->line})\n"
                                . "{$this->getTraceAsString()}";
    }

}

?>