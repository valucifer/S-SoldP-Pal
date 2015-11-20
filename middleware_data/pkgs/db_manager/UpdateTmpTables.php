
<?php
require_once ("ImageChecker.php");
require_once ("ImageUpdate.php");
require_once ("ViewManager.php");
require_once ("Logger.php");

    /**
    * PHP class that provides functions to update the informations of the tmp product table 
    * @package    UpdateProduct
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

class UpdateTmpTables{ 

    private $logger = null;
    public function __construct(){
        $this->logger = new Logger();
    }

    /**
    *Checks if the new images of product are the same, if not than update image path and
    *information into the DB tmp table
    *@params string $imagePath, int $psIdProduct, int $psIdImage
    **/
    public function updateImageField ($imagePath ,$psIdProduct, $psIdImage){
        $toUpdate = new ImageUpdate();
        $comparator = new ImageChecker();
        if($toUpdate->ifImageExist($psIdImage)){
            $imgInformation = $toUpdate->getImageInformation($psIdImage);
            $differences = $comparator->areImagesDifferent($imagePath,  40, 1, $imgInformation['md5Digest'], $imgInformation['colorAnalysis'] );
            if(gettype($differences)==="string"){//if $differences is a string the two images are equals
                return false;
            }
            $toUpdate->updateImageInformation($psIdProduct, $psIdImage, $differences[0], $differences[1], $imagePath);
            return true;
        }
    }

    /**
    *Inserts image path and information into the DB tmp table
    *@params string $imagePath, int $psIdProduct, int $psIdImage
    **/
    public function insertImageField ($imagePath, $psIdProduct, $psIdImage){
        $toUpdate = new ImageUpdate();
        $comparator = new ImageChecker();
        $differences = $comparator->areImagesDifferent($imagePath, 40, 1," ", " " );
        $toUpdate->insertImageInformation($psIdProduct, $psIdImage,$differences[0], $differences[1],$imagePath);
    }
    
    

    public function insertTmpProducts($array_product,$key){
        $view_manager = new ViewManager();
        $result =$view_manager->getSqlNewProducts($array_product,$key);
        $connection = connectionServer();
        foreach($result as $sql){
            $res=null;
            $res = mysql_query($sql,$connection);
            if($res){
            }else{
                 $errno = mysql_errno($connection);
                 $error = mysql_error($connection);
                 switch ($errno) {
                     case 1062:
                        throw new HandleOperationsException($error);
                     break;
                     default:
                     throw new HandleOperationsException($error);
                     break;
                 }
             }  
        }
        closeConnectionServer($connection);
    }
    

    public function updateTmpProducts($array_product,$key){
        $view_manager = new ViewManager();
        $result =$view_manager->getSqlChangedProducts($array_product,$key);
        $connection = connectionServer();
        foreach($result as $sql){
            $res=null;
            $res = mysql_query($sql,$connection);
            if($res){
            }else{
                 $errno = mysql_errno($connection);
                 $error = mysql_error($connection);
                 switch ($errno) {
                     case 1062:
                        throw new HandleOperationsException($error);
                     break;
                     default:
                     throw new HandleOperationsException($error);
                     break;
                 }
             }  
        }
        closeConnectionServer($connection);
    }
           
    
}

?>