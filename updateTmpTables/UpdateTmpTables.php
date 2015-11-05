
<?php
require_once ("../imageChecker/ImageChecker.php");
require_once ("./imagesUpdate/ImageUpdate.php");
class UpdateTmpTables{ 
/**
    * PHP class that provides functions to update the informations of the tmp product table 
    * @package    UpdateProduct
    * @author     Carlos Borges (carboma89@gmail.com)
    **/

    //Static variables to set delta and color numbers to analyze images colors
    private $COLORNUMBER="40";
    private $DELTA="1";

    public function __construct(){
        }

    /**
    *This function provide if the new images of product are the same, if not than update image path and
    *information into the DB tmp table
    *@params string $imagePath, string $size, string $color, string $size
    **/
    public function updateImageField ($imagePath , $material, $color, $size){
        
        $toUpdate = new ImageUpdate();
        $comparator = new ImageChecker();
        if($toUpdate->ifImageExist($material, $size, $color)){
            $imgInformation = $toUpdate->getImageInformation($material, $size, $color);
            $differences = $comparator->areImagesDifferent($imagePath,  $COLORNUMBER, $DELTA,       $imgInformation['colorAnalisys'], $imgInformation['md5Digest'] );
            if(gettype($differences)=="string"){//if $differences is a string the two images are equals
                echo "le immagini sono uguali";
                return true;
            }
            $toUpdate->updateImageInformation($material, $size, $color,$differences[1], $differences[0],$imgPath);
            echo "le immagini sono diverse le ho aggiornato";
        }else{//images is not present into the DB 
            //compaire md5 and color analisys with empty string because this information isn't present into the DB 
            $differences = $comparator->areImagesDifferent($imagePath,  $COLORNUMBER, $DELTA," ", " " );
            echo"sono qua";
            echo"\n\n md5: ".$differences[0]."\n\n color analisys: ".$differences[1];
            $toUpdate->updateImageInformation($material, $size, $color,$differences[1], $differences[0],$imgPath);
            echo "non esistono le info sulle immagini le ho inserito";
        }
    }
 }
?>