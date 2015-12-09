<?php
require_once("settings.php");

class HandleFilesFolder{
    
    public function __construct(){}
    
    public function handle(){
        if( file_exists(MD_FILES_DIR) ){
            $this->_deleteDirectory(MD_FILES_DIR);
        }
    }
    
    private function _deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->_deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }
    
}

?>