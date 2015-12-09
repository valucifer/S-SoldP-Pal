<?php
require_once("settings.php");
require_once (MD_DBMANAGER_DIR."/connection.php");
require_once (MD_LIBS_DIR."/HandleOperationsException.php");
require_once (MD_LIBS_DIR."/Logger.php");

/**
    * PHP class to map brands before store into buffer table
    * @package    db_manager
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

class BrandMapping{
    private $logger=null;

    public function __construct(){
        $this->logger = new Logger();
    }
    
      /**
        *Returns the name of the brand mapping into table sap_brand_name
        *@params int $brand_name
        *@return return if exists the mapping name of a brand
        **/
    public function getNameBrand($brand_name){
        $connection = connectionServer();
        $sql = "SELECT * FROM  brand_mapping WHERE ( sap_brand_name = '".trim($brand_name)."')";
        $result = mysql_query($sql,$connection);
        if (mysql_num_rows($result) > 0){
            while($row = mysql_fetch_array( $result )){
                $toReturn = $row[1];
            }
            closeConnectionServer($connection);
            return $toReturn;
        }
        else {
            closeConnectionServer($connection);
            return 0;
        }
    }
}
?>