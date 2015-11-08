<?php
/**
 * This class handles FTP connection to remote folder and create a local directory to ensure a better files processing
 * 
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */

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

    public function __construct(){
        echo "Class ".__CLASS__." is created!<br/>";
    }

    public function connect(){
        if(is_null($this->connection)){
            $this->connection = ftp_connect($this->ftp_server,$this->ftp_port,3000) or die("Couldn't connect to server");
            if (@ftp_login($this->connection, $this->ftp_username, $this->ftp_password)) {
                echo "Connected as $this->ftp_username@$this->ftp_server<br/>";
            } else {
                throw new Exception("Couldn't connect as $this->ftp_username<br/>");
            }
            ftp_chdir($this->connection, $this->ftp_folder_path);
            ftp_pasv($this->connection, true);
            $this->handleSemaphore();
        }
    }
    
    /**
	 * Handles semaphore and copy remote files to local file replicating the same folders' structure.
	 *
	 * @params 
	 * @return 
	 */

    private function handleSemaphore(){
        //retreive all remote files to check if semaphore exists
        $remote_contents = ftp_nlist($this->connection, $this->ftp_folder_path);

        if(!$remote_contents){
            echo "Destination directory is empty!<br/>";
            return;
        }
        
        //if semaphore is still present on remote directory, we need to stop everything, 'cause probably another process is being performed
        if(in_array($this->ftp_folder_path."/".$this->semaphore_name, $remote_contents) ){
            echo "Ops...another process seems to be in place. Please, retry!<br/>";
            return;
        }else{ 
            //semaphore is not in remote folder, so we can proceed
            $semFileResource = fopen($this->semaphore_name,"w");
            fwrite($semFileResource, "1");
            fclose($semFileResource);
            //upload semaphore to remote path
            if( !ftp_put($this->connection, $this->semaphore_name, $this->semaphore_name, FTP_ASCII) ){
                throw new Exception("Error uploading semaphore to remote directory.");
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
            echo "no semaphore files<br/>";
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
	 * Removes semaphore file 'cause performed operations are executed
	 *
	 * @params 
	 * @return 
	 */

    public function cleanUp(){
        //delete semaphore, 'cause all the operations are ok.
        ftp_delete ($this->connection , $this->ftp_folder_path."/".$this->semaphore_name);
    }

    public function __destruct(){
        if(!is_null($this->connection)){
            if( ftp_close($this->connection) )
                echo "ftp connection closed";
        }
    }

}

?>