<?php
require_once ("Mapping.php");
require_once ("UpdateTmpTables.php");
require_once ("ProductUpdate.php");
require_once ("UpdateTmpTables.php");
require_once ("PrestashopProducts.php");

class PrestashopUpdate{

    private $logger=null;
    private $mapping = null;
    private $keys = null;
    private $triple = null;
    private $array_mapping = null;
    private $array_combinations = null;
    private $url_photo;

    public function __construct($mapping_path){
        $tmp = explode ('_',$mapping_path);
        $this->url_photo = $tmp[0].'_FOTO/';
        $this->logger = new Logger();
        $this->mapping = new Mapping($mapping_path);
        $this->keys= $this->mapping->keys();
        $this->triple= $this->mapping->triple();
        $this->array_mapping = $this->mapping->getItemMaster();
        $this->array_combinations= $this->mapping->getCombinations();
    }

    //solo se esiste il prodotto
    private function _updatePsImages($ps_product_id, $images_url){
        $image_manager = new PrestashopImageHandler();
        print_r($images_url);
        for($i = 0; $i<sizeof($images_url); $i++){
            $image_id = $image_manager->getIdImageByName($images_url[$i]);
            echo "**************<br>$image_id<br>";
            $tmp_manager = new UpdateTmpTables();
            if($image_id===""){//primo inserimento dell'immagine
                $psIdImage = $image_manager->updateImageInPrestashop($ps_product_id,$image_id,$this->url_photo,$images_url[$i]);
                $tmp_manager->insertImageField ($this->url_photo.$images_url[$i],$ps_product_id,$psIdImage);
            }else{//update immagine già esistente
                echo "<br>$this->url_photo.$images_url[$i],$ps_product_id,$image_id<br>";
                $result = $tmp_manager->updateImageField ($this->url_photo.$images_url[$i],$ps_product_id,$image_id);
                if($result){
                    echo"<br>Sono entrato<br>";
                    $image_manager->updateImageInPrestashop($ps_product_id, $image_id, $this->url_photo, $images_url[$i]);//update su prestashop
                }else{
                    echo "le immagini sono uguali non si fa l'inserimento, non inserico su prestashop </br>";
                }
            }
        }    
    }

    public function updatePsProduct(){
        $product_update = new ProductUpdate();
        $insert_product = new PrestashopProduct();
        foreach($this->keys as $key){
            $result = $product_update->productExists($key);
            if(! $result ){
                //prodotto non esiste inserire in ps e db tmp
                try{
                    $image_manager = new PrestashopImageHandler();
                    $array_product = $insert_product->insertProductForPrestashop($this->array_mapping[$key], $this->url_photo, $this->triple[$key], $this->array_combinations[$key]);
                    $array_images_id = $array_product[1];
                    print_r($array_images_id);
                    $product_update->insertProduct($array_product[0],$key);
                    echo "Inserisco il nuovo prodotto con ps_id $array_product[0] e reference $key ";
                    $tmp_manager = new UpdateTmpTables();
                    for($i=0;$i<sizeof($array_images_id);$i++){
                        $tmp = explode (';',$array_images_id[$i]);
                        if(empty($tmp)) break;
                        $tmp_manager->insertImageField ($this->url_photo.$tmp[1],$array_product[0],$tmp[0]);
                    }   
                }catch(Exception $e){
                    echo "$key<br/>";
                    echo $e->getMessage()." in line". $e->getLine()."<br/>";
                    echo "<br/><br/><br/><br/>";
                }  
            }else{//il prodotto esiste già
                echo "<br>il prodotto esiste già<br>";
                $url = $this->formatUrlPhoto($key);
                print_r($url);
                print_r($result);
                $this->_updatePsImages($result,$url);  
            }
        }
    }

    private function formatUrlPhoto($key){
        $array_single_product = $this->array_mapping[$key];
        $array_url = $array_single_product['URL'];
        $single_url = explode ('.jpg,',$array_url);
        $url = array();
        for($i=0; $i<sizeof($single_url)-1;$i++){
            array_push ($url,$single_url[$i].'.jpg');
        }
        return $url;
    }

}
?>