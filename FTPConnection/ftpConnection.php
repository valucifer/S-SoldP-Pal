<?php
/**
 * This class handles FTP connection to remote folder and create a local directory to ensure a better files processing.
 * Note that this class also handles a semaphore file uploaded on remote directory to create a sort of atomic informations processing.
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */
require("HandleOperationsException.php");
require("Logger.php");

class FTPConnection{

    private $ftp_server = "ftp14.ilbello.com";
    private $ftp_port = 2114;
    private $ftp_username = "pjgroup";
    private $ftp_password = "3nRvgCMDYv";
    private $ftp_folder_path = "/iloko";
    private $local_dir = "files";
    private $semaphore_name = "FTP_SEMAMPHORE.smph";
    private $connection = null;
    private $timestamps_name = array();
    private $semaphores_array = array();
    private $folders_name = array();
    private $logger = null;
    
    /**
	 * Initializes class.
	 *
	 * @params 
	 * @return 
	 */
    public function __construct(){}

    /**
	 * Handles connection to remote folder.
	 * @exception HandleOperationsException thrown when connection fails to be establish.
	 * @params 
	 * @return 
	 */
    public function connect(){
        if(is_null($this->connection)){
            $this->connection = ftp_connect($this->ftp_server,$this->ftp_port,3000) or die("Couldn't connect to server");
            if (@ftp_login($this->connection, $this->ftp_username, $this->ftp_password)) {
                echo "Connected as $this->ftp_username@$this->ftp_server<br/>";
            } else {
                throw new HandleOperationsException("Couldn't connect as $this->ftp_username");
            }
            ftp_chdir($this->connection, $this->ftp_folder_path);
            ftp_pasv($this->connection, true);
            $this->logger = new Logger();
            $this->handleSemaphore();
        }
    }

    /**
	 * Handles semaphore and copy remote files to local directory replicating the same folders' structure.
	 * @exception HandleOperationsException thrown when local semaphore cannot be uploaded to remote directory for some reason.
	 * @params 
	 * @return 
	 */

    private function handleSemaphore(){
        //retreive all remote files to check if semaphore exists
        $remote_contents = ftp_nlist($this->connection, $this->ftp_folder_path);

        if(!$remote_contents){
            $this->logger->postMessage("Destination directory is empty!","WARNING");
            return;
        }

        //if semaphore is still present on remote directory, we need to stop everything, 'cause probably another process is being performed
        if(in_array($this->ftp_folder_path."/".$this->semaphore_name, $remote_contents) ){
            $this->logger->postMessage("Ops...another process seems to be in place.", "ERROR");
            return;
        }else{ 
            //semaphore is not in remote folder, so we can proceed
            $semFileResource = fopen($this->semaphore_name,"w");
            fwrite($semFileResource, "1");
            fclose($semFileResource);
            //upload semaphore to remote path
            if( !ftp_put($this->connection, $this->semaphore_name, $this->semaphore_name, FTP_ASCII) ){
                throw new HandleOperationsException("Error uploading semaphore to remote directory.", "ERROR");
            }
            //delete semaphore on local dir
            unlink($this->semaphore_name);
        }

        //check if local dir to store the whole remote directory structure exists. If not, then we create it
        if(!file_exists($this->local_dir)){
            mkdir($this->local_dir);
        }

        //check if a remote semaphore exists.
        foreach ($remote_contents as &$file){
            $fileInfo = pathinfo($file);
            if(!isset($fileInfo["extension"]))
                continue;
            if($fileInfo["extension"] == "chk"){
                //there's a semaphore with chk extension, so we can proceed
                ftp_get($this->connection, $this->local_dir."/".$fileInfo["filename"].".".$fileInfo["extension"], $file, FTP_ASCII);
                array_push($this->semaphores_array, $fileInfo["basename"]);
            }
        }

        //we order semaphore files. So we can perform operations ordered by time
        if(count($this->semaphores_array) > 0){
            asort($this->semaphores_array, SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL);
        }else{
            ftp_delete ($this->connection , $this->ftp_folder_path."/".$this->semaphore_name);
            return;
        }

        //we isolate timestamps string, so we can get all files with a fixed timestamp
        foreach( $this->semaphores_array as &$sem ){    
            $tmp_array = explode("_", $sem);
            foreach( $remote_contents as &$remCon ){
                if( strpos($remCon, $tmp_array[0]) ){
                    $fileInfo = pathinfo($remCon);
                    if(!isset($fileInfo["extension"])){
                        //we encountered a folder. This folder(s) will be processed later
                        array_push($this->folders_name, $fileInfo["filename"]);
                        continue;
                    }
                    ftp_get($this->connection, $this->local_dir."/".$fileInfo["filename"].".".$fileInfo["extension"], $remCon, FTP_ASCII);
                }
            }
        }

        //subfolder copy
        if( count($this->folders_name) > 0 ){
            foreach( $this->folders_name as $dir ){
                mkdir($this->local_dir."/".$dir);
                ftp_chdir($this->connection, $this->ftp_folder_path."/".$dir);
                $dir_files = ftp_nlist($this->connection, $this->ftp_folder_path."/".$dir);
                foreach($dir_files as $file){
                    $fileInfo = pathinfo($file);
                    ftp_get($this->connection, $this->local_dir."/".$dir."/".$fileInfo["filename"].".".$fileInfo["extension"], $file, FTP_BINARY);
                }
            }
        }
    }

    /**
	 * Removes semaphore file 'cause performed operations are successfully executed.
	 * 
	 * @params 
	 * @return 
	 */
    public function cleanUp(){
        //delete semaphore, 'cause all the operations are ok.
        ftp_delete ($this->connection , $this->ftp_folder_path."/".$this->semaphore_name);
    }
    
    /**
	 * Handles class desctruction operations closing connection to remote folder.
	 * @exception HandleOperationsException thrown when connection cannot be closed for some reason.
	 * @params 
	 * @return 
	 */    
    public function __destruct(){
        if(!is_null($this->connection)){
            if( !ftp_close($this->connection) )
                throw new HandleOperationsException("Unable to close connection.", "ERROR");
        }
    }

}

?>