<?php

require_once("settings.php");
require_once(MD_LIBS_DIR."/HandleOperationsException.php");
require_once(MD_LIBS_DIR."/FTPException.php");
require_once(MD_LIBS_DIR."/Logger.php");
/**
 * This class handles FTP connection to remote folder and create a local directory to ensure a better files processing.
 * Note that this class also handles a semaphore file uploaded on remote directory to create a sort of atomic informations processing.
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */

class FTPConnection{

    private $local_dir = MD_ROOT."/files";
    private $connection = null;
    private $timestamps_name = array();
    private $semaphores_array = array();
    private $folders_name = array();
    private $logger = null;
    private $dir_size = 0;
    private $local_size = 0;
    private $semaphores_array_size = 0;
    private $remote_contents = null;

    /**
	 * Initializes class.
	 *
	 * @params 
	 * @return 
	 */
    public function __construct(){
        $this->logger = new Logger();
    }

    /**
	 * Handles connection to remote folder.
	 * @exception HandleOperationsException thrown when connection fails to be establish.
	 * @params 
	 * @return 
	 */
    public function connect(){
        if(is_null($this->connection)){
            $this->connection = ftp_connect(MD_FTP_SERVER, MD_FTP_PORT, 90);
            if(!$this->connection)
                throw new FTPException("ftp connection failed!");
            if (@ftp_login($this->connection, MD_FTP_USERNAME, MD_FTP_PASSWORD)) {
                $this->logger->postMessage("Connected as ".MD_FTP_USERNAME."@".MD_FTP_SERVER,"INFO");
            } else {
                throw new FTPException("Couldn't connect as ".MD_FTP_USERNAME);
            }
            ftp_pasv($this->connection, true);
        }
    }

    /**
	 * Handles semaphore and copy remote files to local directory replicating the same folders' structure.
	 * @exception HandleOperationsException thrown when local semaphore cannot be uploaded to remote directory for some reason.
	 * @params 
	 * @return 
	 */
    public function handleSemaphore(){
        //if semaphore is still present on local directory, we need to stop everything, 'cause probably another process is being performed
        if(file_exists(MD_ROOT."/FTP_SEMAMPHORE.smph")){
            $this->logger->postMessage("Ops...another process seems to be in place. A semaphore file is present.", "WARNING");
            return "-1"; //returns -1 because this return does not need to clean local semaphore
        }

        $sem_file_resource = fopen(MD_ROOT."/FTP_SEMAMPHORE.smph","w");
        fwrite($sem_file_resource, "1");
        fclose($sem_file_resource);

        //retreive all remote files to check if semaphore exists
        $this->remote_contents = ftp_nlist($this->connection, MD_FTP_ROOT_DIR);
        
        if(!$this->remote_contents){
            $this->logger->postMessage("Error checking remote directory! Maybe it's empty...","WARNING");
            return false;
        }

        //check if a remote semaphore exists.
        foreach ($this->remote_contents as $file){
            $file_info = pathinfo($file);
            if(!isset($file_info["extension"]))
                continue;
            if($file_info["extension"] == "chk"){
                //there's a semaphore with chk extension, so we can proceed
                array_push($this->semaphores_array, $file_info["basename"]);
            }
        }

        //we order semaphore files. So we can perform operations ordered by time
        if(count($this->semaphores_array) > 0){
            natcasesort($this->semaphores_array);
            $this->semaphores_array_size = sizeof($this->semaphores_array)-1;
        }else{
            $this->logger->postMessage("Remote directory is empty. Update process aborted.","WARNING");
            unlink(MD_ROOT."/FTP_SEMAMPHORE.smph");
            return false;
        }
        
        //check if local dir to store the whole remote directory structure exists. If not, then we create it
        if(!file_exists($this->local_dir)){
            mkdir($this->local_dir);
        }

        //check size of remote folde
        $this->logger->postMessage("Calculating remote folder size...","INFO");
        $this->dir_size = $this->getRemoteDirSize(MD_FTP_ROOT_DIR, explode("_",$this->semaphores_array[$this->semaphores_array_size])[0]);
        $this->logger->postMessage("Size is $this->dir_size.","INFO");

        foreach ($this->remote_contents as $file){
            $file_info = pathinfo($file);
            if(!isset($file_info["extension"]))
                continue;
            if($file_info["extension"] === "chk" && strpos($file_info["basename"], $this->semaphores_array[$this->semaphores_array_size]) !== false ){
                //there's a semaphore with chk extension, so we can proceed
                if(!ftp_get($this->connection, $this->local_dir."/".$file_info["filename"].".".$file_info["extension"], $file, FTP_ASCII))
                    throw new FTPException("Connection process is down. Can't download files from remote host. Please retry.");
            }
        }

        //we isolate timestamps string, so we can get all files with a fixed timestamp
        $tmp_array = explode("_", $this->semaphores_array[$this->semaphores_array_size]);
        foreach( $this->remote_contents as $remCon ){
            if( strpos($remCon, $tmp_array[0]) !== false ){
                $file_info = pathinfo($remCon);
                if(!isset($file_info["extension"])){
                    //we encountered a folder. This folder(s) will be processed later
                    array_push($this->folders_name, $file_info["filename"]);
                    continue;
                }
                if(!ftp_get($this->connection, $this->local_dir."/".$file_info["filename"].".".$file_info["extension"], $remCon, FTP_ASCII))
                    throw new FTPException("Connection process is down. Can't download files from remote host. Please retry");
                $this->local_size += filesize($this->local_dir."/".$file_info["filename"].".".$file_info["extension"]);
                $percentage = round(($this->local_size * 100)/$this->dir_size, 1);
                $this->logger->postMessage("Download percentage: $percentage%", "INFO");
            }
        }

        //subfolder copy
        if( count($this->folders_name) > 0 ){
            foreach( $this->folders_name as $dir ){
                $tmp_array = explode("_", $this->semaphores_array[$this->semaphores_array_size]);
                if( strpos($dir, $tmp_array[0]) !== false ){
                    mkdir($this->local_dir."/".$dir);
                    ftp_chdir($this->connection, MD_FTP_ROOT_DIR.$dir);
                    $dir_files = ftp_nlist($this->connection, MD_FTP_ROOT_DIR.$dir);
                    foreach($dir_files as $file){
                        $file_info = pathinfo($file);
                        if(!ftp_get($this->connection, $this->local_dir."/".$dir."/".$file_info["filename"].".".$file_info["extension"], $file, FTP_BINARY))
                            throw new FTPException("Connection process is down. Can't download files from remote host. Please retry");
                        $this->local_size += filesize($this->local_dir."/".$dir."/".$file_info["filename"].".".$file_info["extension"]);
                        $percentage = round(($this->local_size * 100)/$this->dir_size,1);
                        $this->logger->postMessage("Download percentage: $percentage%","INFO");
                    }
                }
            }
        }
        if(!is_null($this->connection)){
            if( !ftp_close($this->connection) )
                throw new FTPException("Unable to close connection.", "ERROR");
            $this->connection = null;
        }
        return true;
    }

    /**
	 * Gets all semaphores names from remote folder.
	 * 
	 * @params 
	 * @return $semaphore_array that contains semaphore's names
	 */
    public function getPSSemaphoresPath(){
        return $this->semaphores_array[$this->semaphores_array_size];
        //return $this->semaphores_array;
    }

    /**
	 * Deletes semaphore in remote folder. This method is called when an exception occur, before operations are completed.
	 * 
	 * @params 
	 * @return
	 */
    public function revertCleanup(){
        if(file_exists(MD_ROOT."/FTP_SEMAMPHORE.smph"))
            unlink(MD_ROOT."/FTP_SEMAMPHORE.smph");
    }

    /**
	 * Removes semaphore file and all contents from remote folder. This method also removes middleware semaphore file, so another upload process can be performed.
	 * 
	 * @params 
	 * @return 
	 */
    public function cleanUp(){
        $folder_to_remove = array();
        //delete semaphore, 'cause all the operations are ok.
        unlink(MD_ROOT."/FTP_SEMAMPHORE.smph");
        
        //delete downloaded files
        $this->_deleteDirectory($this->local_dir);

        $this->connect();
        
        //$this->remote_contents = ftp_nlist($this->connection, MD_FTP_ROOT_DIR);
        
        foreach($this->semaphores_array as $sem){
            $sem_to_search = explode("_", $sem);
            foreach($this->remote_contents as $remSource){
                if(strpos($remSource, $sem_to_search[0]) !== false){
                    $file_info = pathinfo($remSource);
                    //we need to delete this file/folder
                    if(!isset($file_info["extension"])){
                        //we encountered a folder. This folder(s) will be processed later
                        array_push($folder_to_remove, trim($remSource));
                        continue;
                    }
                    $bool = ftp_delete($this->connection, trim($remSource));
                }
            }
        }

        foreach($folder_to_remove as $folder){
            ftp_chdir($this->connection, $folder);
            $folder_contents = ftp_nlist($this->connection, $folder);
            foreach($folder_contents as $fold){
                $bool = ftp_delete($this->connection, $fold);
            }
            ftp_chdir($this->connection, MD_FTP_ROOT_DIR);
            ftp_rmdir($this->connection, $folder);
        }
        
        if(!is_null($this->connection)){
            if( !ftp_close($this->connection) )
                throw new FTPException("Unable to close connection.", "ERROR");
            $this->connection = null;
        }
    }

    private function getRemoteDirSize($dir, $sem_name){ 
        $size = 0;
        foreach ($this->remote_contents as &$file){
            if( strpos($file, $sem_name) !== false ){
                $file_info = pathinfo($file);
                if(!isset($file_info["extension"])){
                    $size += $this->getRemoteSubFolderSize($file);
                }
                $size += ftp_size($this->connection, $file);
            }
        }
        return $size; 
    }
    
    private function getRemoteSubFolderSize($dir){
        $size = 0;
        $subfolder_files = ftp_nlist($this->connection, $dir);
        ftp_chdir($this->connection, $dir);
        foreach($subfolder_files as $file){
            $size += ftp_size($this->connection, $file);
        }
        return $size;
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