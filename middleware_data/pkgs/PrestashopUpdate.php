<?php

require_once ("Mapping.php");
require_once ("UpdateTmpTables.php");
require_once ("ProductUpdate.php");
require_once ("PrestashopProducts.php");
require_once ("ProductBufferTables.php");
require_once ("ViewManager.php");

class PrestashopUpdate{

    private $logger=null;
    private $mapping = null;
    private $keys = null;
    private $triple = null;
    private $array_mapping = null;
    private $array_combinations = null;
    private $url_photo;

    public function __construct($mapping_path){
            echo"<br/> 1";
        $tmp = explode ('_',$mapping_path);
        $this->url_photo = $tmp[0].'_FOTO/';
        $this->logger = new Logger();
        $this->mapping = new Mapping($mapping_path);
        $this->keys= $this->mapping->keys();
        $this->triple= $this->mapping->triple();
        $this->array_mapping = $this->mapping->getItemMaster();
        $this->array_combinations= $this->mapping->getCombinations();
        $buffer = new ProductBufferTables();
        foreach($this->keys as $key){
            $single_mapping = $this->array_mapping[$key];
            $single_triple = $this->triple[$key];
            $single_combination_array = $this->array_combinations[$key];
            foreach($single_triple as $triple){
                $feature = $single_mapping['Feature'];
                $tmp_triple = explode(';',$triple);
                $codice_colore = $tmp_triple[1]; 
                $codice_taglia = $tmp_triple[2]; 
                $single_array_combination= $single_combination_array[$triple];
                $tmp_value = explode (',',$single_array_combination['Valori']);
                $colore = $tmp_value[0];
                $taglia = $tmp_value[1];
                $quantita = $single_array_combination['Qta'];
                $immagine= $single_array_combination['Immagine']; 
                $buffer->insertProduct( $key, $single_mapping['Attivo'], $single_mapping['Categorie'], $single_mapping['Prezzo'], $single_mapping['Supplier'], $single_mapping['Manufacture'], $single_mapping['Qta'], $single_mapping['Qta_min'], $feature['Lunghezza'], $feature['Altezza'], $feature['Larghezza'], $colore, $quantita, $taglia, $single_mapping['Nome'], $feature['Modello'], $feature['Linea'], $codice_colore, $codice_taglia, $single_mapping['URL'], $immagine);
            }        
        }
        
    }

    //solo se esiste il prodotto
    private function _updatePsImages($ps_product_id, $images_url){
        $image_manager = new PrestashopImageHandler();
        print_r($images_url);
        for($i = 0; $i<sizeof($images_url); $i++){
            $image_id = $image_manager->getIdImageByName($images_url[$i]);
            $tmp_manager = new UpdateTmpTables();
            if($image_id===""){//primo inserimento dell'immagine
                /**$psIdImage = $image_manager->updateImageInPrestashop($ps_product_id,$image_id,$this->url_photo,$images_url[$i]);**/
                $tmp_manager->insertImageField ($this->url_photo.$images_url[$i],$ps_product_id,$psIdImage);
                return 1;
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
        $tmp_manager = new UpdateTmpTables();
        $insert_product = new PrestashopProduct();
        $new_products_manager = new ViewManager();
        $new_products = $new_products_manager-> getNewProduct();
        foreach($new_products as $product){
            $image_manager = new PrestashopImageHandler();
            $tmp_manager = new UpdateTmpTables();
            $array_reference = $product[0];
            $key=$array_reference['Reference'];
            $array_product = $insert_product->insertProductForPrestashop($product[0], $this->url_photo,$product[1], $product[2]);
            $array_images_id = $array_product[1];
            for($i=0;$i<sizeof($array_images_id);$i++){
                $tmp = explode (';',$array_images_id[$i]);
                if(empty($tmp)) break;
                $tmp_manager->insertImageField ($this->url_photo.$tmp[1],$array_product[0],$tmp[0]);
            }
            $tmp_manager->updateTmpProducts($array_product[0],$key);
        }
        /*foreach($this->keys as $key){
            $result = $product_update->productExists($key);
            if(! $result ){
                //prodotto non esiste inserire in ps e db tmp
                try{
                    $image_manager = new PrestashopImageHandler();
                    $array_product = $insert_product->insertProductForPrestashop($this->array_mapping[$key], $this->url_photo, $this->triple[$key], $this->array_combinations[$key]);
                    $array_images_id = $array_product[1];
                    print_r($array_images_id);
                    $product_update->insertProduct($array_product[0],$key);
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
                if($this->_updatePsImages($result,$url)==1){//il prodotto esiste ma è una nuova immagine
                    
                }  
            }
        }*/
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