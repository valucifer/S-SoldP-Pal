<?php

    /**
    * PHP class update the tmp custom table use to verify the change between
    * the last import and the new import of images of a product 
    * @package    UpdateProduct
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

    include "connection.php";
    
    class ImageUpdate{
        
        public function __construct(){
        }
        
        /** 
        *Function that update information of images 
        *@params string $material, string $size, string $color, string $colorAnalisys, string $md5Digest, string $imgPath
        */
        public function updateImageInformation($material, $size, $color,$colorAnalisys, $md5Digest,$imgPath){
            if($this->_ifProductExist($material,$size,$color)==true){
                $connection = connectionServer();
                $sql = "UPDATE ps_tmp_product SET color_analisys ='".$colorAnalisys."' ,md5_digest = '".$md5Digest.'" ,                     img_paht ="'.$imgPaht."' WHERE ( material = '".$material."' AND color= '".$color."' AND size=                               '".$size."')";
                if(mysql_query($sql,$connection))
                    echo "Update of record successfully";
            }else{
                $connection = connectionServer();
                $sql = "INSERT INTO ps_tmp_product ( material, size, color, color_analisys,md5_digest, img_paht) VALUES 
                    ( '".$material."','".$color."' , '".$size."','".$imgPaht."')";
                if(mysql_query($sql,$connection))
                    echo "New record created successfully";
            } 
            closeConnectionServer($connection);
        }
        
        
        /**
        * Private function that check if the product exist into the tmp table searching by the key of product 
        *@params string $material, string $size, string $color
        */
        private function _ifProductExist($material, $size, $color){
            $connection = connectionServer();
            $sql = "SELECT * FROM  ps_tmp_product WHERE ( material = '".$material."' AND color= '".$color."' AND size=                  '".$size."')";
            $result = mysql_query($sql,$connection);
            if (mysql_num_rows($result) > 0){
                closeConnectionServer($connection);
                return true;
            }
            else {
                closeConnectionServer($connection);
                return false;
            }
        }
        
        /**
        *Function that check if there is a color analisys and md5 digest information into the DB for some material size
         and color 
        *
        *@params string $material, string $size, string $color
        *@return true if exist the material in DB whit color analisys and amd5 digest information, false otherwise
        *
        **/
        public function ifImageExist($material, $size, $color){
            $connection = connectionServer();
            $sql = "SELECT * FROM  ps_tmp_product WHERE ( material = '".$material."' AND color= '".$color."' AND size=                  '".$size."')";
            $result = mysql_query($sql,$connection);
            if (mysql_num_rows($result) > 0){
                while($row = mysql_fetch_array( $result )){
                    if(($row[3] === '') || ($row[4] === '')){
                        closeConnectionServer($connection);
                        return false;
                    }else{
                        closeConnectionServer($connection);
                        return true;
                    }
                }
            }
            else {
                closeConnectionServer($connection);
                return false;
            }
        }
            
        /**
        *Function that return images information from db
        *@params string $material, string $size, string $color
        *@return an associative array with colorAnalisys anda md5Digest information
        **/
        public function getImageInformation($material, $size, $color){
            $connection = connectionServer();
            $sql = "SELECT * FROM  ps_tmp_product WHERE ( material = '".$material."' AND color= '".$color."' AND                     size= '".$size."')";
            $result = mysql_query($sql,$connection);
            while($row = mysql_fetch_array( $result )){
                $toReturn = array("colorAnalisys"=>$row[3], "md5Digest"=>$row[4]);
            }
              closeConnectionServer($connection);
            return $toReturn;
        }
    } 
    
?>