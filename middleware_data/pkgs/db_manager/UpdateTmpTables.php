
<?php
require_once ("../imageChecker/ImageChecker.php");
require_once ("ImageUpdate.php");
class UpdateTmpTables{ 
/**
    * PHP class that provides functions to update the informations of the tmp product table 
    * @package    UpdateProduct
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

    //Static variables to set delta and color numbers to analyze images colors
    private $COLORNUMBER=40;
    private $DELTA=1;

    public function __construct(){
        }

    /**
    *This function provide if the new images of product are the same, if not than update image path and
    *information into the DB tmp table
    *@params string $imagePath, int $psIdProduct, int $psIdImage
    **/
    public function updateImageField ($imagePath , $psIdProduct,$psIdImage){
        
        $toUpdate = new ImageUpdate();
        $comparator = new ImageChecker();
        
        if($toUpdate->ifImageExist($psIdImage)){
            $imgInformation = $toUpdate->getImageInformation($psIdImage);
            $differences = $comparator->areImagesDifferent($imagePath,  40, 1, $imgInformation['md5Digest'], $imgInformation['colorAnalysis'] );
            if(gettype($differences)==="string"){//if $differences is a string the two images are equals
                echo "le immagini sono uguali </br>";
                return false;
            }
            $toUpdate->updateImageInformation($psIdProduct, $psIdImage,$differences[1], $differences[0],$imagePath);
            echo "le immagini sono diverse le ho aggiornate </br>";
            return true;
            
        }
    }
    
     /**
    *This function provideinsert image path and information into the DB tmp table
    *@params string $imagePath, int $psIdProduct, int $psIdImage
    **/
    public function insertImageField ($imagePath,$psIdProduct,$psIdImage){
        $toUpdate = new ImageUpdate();
        $comparator = new ImageChecker();
        $differences = $comparator->areImagesDifferent($imagePath,  40, 1," ", " " );
        $toUpdate->insertImageInformation($psIdProduct, $psIdImage,$differences[1], $differences[0],$imagePath);
    }
 }
?>