
<?php
require_once ("ImageChecker.php");
require_once ("ImageUpdate.php");
require_once ("ViewManager.php");

class UpdateTmpTables{ 
    /**
    * PHP class that provides functions to update the informations of the tmp product table 
    * @package    UpdateProduct
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

    public function __construct(){}

    /**
    *This function provide if the new images of product are the same, if not than update image path and
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
                echo "le immagini sono uguali </br>";
                return false;
            }
            $toUpdate->updateImageInformation($psIdProduct, $psIdImage, $differences[0], $differences[1], $imagePath);
            echo "le immagini sono diverse le ho aggiornate </br>";
            return true;

        }
    }

    /**
    *This function provideinsert image path and information into the DB tmp table
    *@params string $imagePath, int $psIdProduct, int $psIdImage
    **/
    public function insertImageField ($imagePath, $psIdProduct, $psIdImage){
        $toUpdate = new ImageUpdate();
        $comparator = new ImageChecker();
        $differences = $comparator->areImagesDifferent($imagePath, 40, 1," ", " " );
        $toUpdate->insertImageInformation($psIdProduct, $psIdImage,$differences[0], $differences[1],$imagePath);
    }
    
    
    /**
    *
    **/
    public function updateTmpProducts($array_product,$key){
        $view_manager = new ViewManager();
        echo"<br/> sono qua 1 $array_product,$key";
        $result =$view_manager->getSqlNewProducts($key);
        $connection = connectionServer();
        echo"<br/> sono qua 2";
        echo"<br/> sono qua 3";
        print_r($result);
        foreach($result as $sql){
            echo "</br> $sql";
            $res = mysql_query($sql,$connection);
            echo "</br>risultato $res";
            if($res){
                $this->logger->postMessage("Il prodotto $ps_id e' stato inserito correttamente ");
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