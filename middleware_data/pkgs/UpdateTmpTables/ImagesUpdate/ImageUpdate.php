<?php

    /**
    * PHP class update the tmp custom table use to verify the change between
    * the last import and the new import of images of a product 
    * @package    UpdateProduct
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

    include "connection.php";
    require ("./HandleOperationsException.php");
    require ("./Logger.php");
    
    class ImageUpdate{
        $logger=null;
        
        public function __construct(){
            $this->logger = new Logger();
        }
        
        /** 
        *Function that updates images informations
        *@params int $psIdImage, int $psIdImage, string $coloranalysis, string $md5Digest,
        *string $imgPath
        */
        public function updateImageInformation($psIdProduc,$psIdImage,$colorAnalysis, $md5Digest,$imgPath){
            $productExist = $this-> _ifProductExist($psIdProduc);
            $imageExist = $this-> ifImageExist($psIdImage);

            if(!$productExist){
                $this->logger->postMessage("Il prodotto $psIdProduc non esiste","WARNING");
                return ;
            }else
                if($imageExist===true){
                    $oldPath = $this->getImageInformation($psIdImage);
                    $connection = connectionServer();
                    $sql = "UPDATE ps_tmp_image SET color_analysis ='".$colorAnalysis."' ,md5_digest = '".$md5Digest."' ,                     new_path = '".$imgPath."',old_path = '".$oldPath["newPath"]."', status = '1' WHERE ( ps_id =  
                    '".$psIdImage."')";
                    $res = mysql_query($sql,$connection)
                    if(res){
                        $this->logger->postMessage("Il prodotto $psIdProduc e' stato aggiornato correttamente ");
                    }
                    else{
                        $errno = mysql_errno($connection);
                        $error = mysql_error($connection);
                        switch ($errno) {
                           case MYSQL_DUPLICATE_KEY_ENTRY:
                throw new HandleOperationsException($error, "ERROR");
                           break;
                           default:
                           throw MySQLException($error, $errno);
                           break;
                        }
            } else {
                echo "l'immagine non esiste </br>";
                $connection = connectionServer();
                $sql = "INSERT INTO ps_tmp_image ( ps_id, color_analysis,md5_digest, new_path,status,fk_ps_id)
                VALUES('".$psIdImage."','".$colorAnalysis."','".$md5Digest."','".$imgPath."','1','".$psIdProduc."')";
                $res = mysql_query($sql,$connection)
                if($res)
                    echo "New record created successfully <br/>";
                else{
                    else{
                    $errno = mysql_errno($connection);
                    $error = mysql_error($connection);
                    switch ($errno) {
                        case MYSQL_DUPLICATE_KEY_ENTRY:
                throw new HandleOperationsException($error, "ERROR");
                        break;
                        default:
                        throw MySQLException($error, $errno);
                        break;
                        }
                    
                }

            }
            closeConnectionServer($connection);
        }
        
        /** 
        *Function that inserts images informations
        *@params int $psIdImage, int $psIdImage, string $coloranalysis, string $md5Digest,
        *string $imgPath
        */
        public function insertImageInformation($psIdProduc,$psIdImage,$colorAnalysis, $md5Digest,$imgPath){
            $connection = connectionServer();
            $sql = "INSERT INTO ps_tmp_image ( ps_id, color_analysis,md5_digest, new_path,status,fk_ps_id)
                VALUES('".$psIdImage."','".$colorAnalysis."','".$md5Digest."','".$imgPath."','1','".$psIdProduc."')";
            if(mysql_query($sql,$connection))
                echo "New record created successfully <br/>";
            closeConnectionServer($connection);
        }
        
        
        /**
        *Function that checks if there is a images into DB
        *@params string $psId
        *@return true if exist the image in DB , false otherwise
        *
        **/
        public function ifImageExist($psIdImage){
            $connection = connectionServer();
            $sql = "SELECT * FROM  ps_tmp_image WHERE ( ps_id = '".$psIdImage."')";
            $result = mysql_query($sql,$connection);
            if (mysql_num_rows($result) > 0){
                closeConnectionServer($connection);
                return TRUE;
            } else {
                closeConnectionServer($connection);
                return FALSE;
            }
        }
            
        /**
        *Function that returns images informations from db
        *@params int $psIdImage
        *@return an associative array with coloranalysis anda md5Digest information
        **/
        public function getImageInformation($psIdImage){
            $connection = connectionServer();
            $sql = "SELECT * FROM  ps_tmp_image WHERE ( ps_id = '".$psIdImage."')";
            $result = mysql_query($sql,$connection);
            while($row = mysql_fetch_array( $result )){
                $toReturn = array("colorAnalysis"=>$row[4], "md5Digest"=>$row[3],"oldPath"=>$row[1],"newPath"=>$row[2]);
            }
              closeConnectionServer($connection);
            return $toReturn;
        }
        
        
         /**
        * Private function that checks if the product exists into the tmp table searching by product key
        *@params string $psId
        @return true if exist the product in DB , false otherwise
        */
        public function _ifProductExist($psId){
            $connection = connectionServer();
            $sql = "SELECT * FROM  ps_tmp_products WHERE ( ps_id = '".$psId."')";
            $result = mysql_query($sql,$connection);
            if (mysql_num_rows($result) > 0){
                closeConnectionServer($connection);
                return TRUE;
            } else {
                closeConnectionServer($connection);
                return FALSE;
            }
        }
        
    } 
    
?>