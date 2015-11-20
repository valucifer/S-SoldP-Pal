<?php
require_once ("Mapping.php");
require_once ("UpdateTmpTables.php");
require_once ("ProductUpdate.php");
require_once ("PrestashopProducts.php");
require_once ("ProductBufferTables.php");
require_once ("ViewManager.php");
require_once ("Logger.php");

/**
* Class that updates prestashop products informations from views and images
* @package    UpdateProduct
* @author     Carlos Borges (carboma89@gmail.com), Valentino Vivone, Nello Saulino
**/



class PrestashopUpdate{

    private $logger=null;
    private $mapping = null;
    private $keys = null;
    private $triple = null;
    private $array_mapping = null;
    private $array_combinations = null;
    private $url_photo;

    public function __construct(){
        $this->logger = new Logger();
    }

    /** 
    *Initializes the mapping and the buffer table
    *@params string $mapping_path
    */
    public function startUpdate($mapping_path){
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

    /** 
    *Updates images if it change
    *@params string $ps_product_id, string $images_url
    */
    private function _updatePsImages($ps_product_id, $images_url){
        $image_manager = new PrestashopImageHandler();
        for($i = 0; $i<sizeof($images_url); $i++){
            $image_id = $image_manager->getIdImageByName($images_url[$i]);
            $tmp_manager = new UpdateTmpTables();
            if($image_id===""){//primo inserimento dell'immagine
            }else{//update immagine già esistente
                $result = $tmp_manager->updateImageField ($this->url_photo.$images_url[$i],$ps_product_id,$image_id);
                if($result){
                    $image_manager->updateImageInPrestashop($ps_product_id, $image_id, $this->url_photo, $images_url[$i]);//update su prestashop
                }
            }
        }    
    }

    /** 
    *Updates or insert product informations
    */
    public function updatePsProduct(){
        $product_update = new ProductUpdate();
        $insert_product = new PrestashopProduct();
        $new_products_manager = new ViewManager();
        $tmp_manager = new UpdateTmpTables();
        $all_products = $new_products_manager-> getAllProducts();
        $logger->postMessage("Inizio aggiornamento ","DEBUG");
        foreach($all_products as $product){
            $array_reference = $product[0];
            $key=$array_reference['Reference'];
            asort($product[1]);
            asort($product[2]);
            $result = $product_update->productExists($key);
            if($result){
                $url = $this->formatUrlPhoto($key);
                $this->_updatePsImages($result,$url);
            }
        }
        $new_products = $new_products_manager-> getNewProduct();
        foreach($new_products as $product){
            $array_reference = $product[0];
            $key=$array_reference['Reference'];
            asort($product[1]);
            asort($product[2]);
            $array_product = $insert_product->insertProductForPrestashop($product[0], $this->url_photo,$product[1], $product[2]);
            $array_images_id = $array_product[1];
            for($i=0;$i<sizeof($array_images_id);$i++){
                $tmp = explode (';',$array_images_id[$i]);
                if(empty($tmp)) break;
                $tmp_manager->insertImageField ($this->url_photo.$tmp[1],$array_product[0],$tmp[0]);
            }
            $tmp_manager->insertTmpProducts($array_product[0],$key);
        }
        $changed_products = $new_products_manager->getProductDifferences();
        foreach($changed_products as $product){
            $array_reference = $product[0];
            $key = $array_reference['Reference'];
            asort($product[1]);
            asort($product[2]);
            $array_product = $insert_product->updateProductForPrestashop($product[0], (int) $product[3], $this->url_photo,$product[1], $product[2]);
            if( !empty($array_product[3]) ){
                foreach($array_product[3] as $new_img){
                    $new_photo_infos = explode(';',$new_img);
                    $tmp_manager->insertImageField ($this->url_photo.$new_photo_infos[1],$array_product[0],$new_photo_infos[1]);
                }
            }
            $tmp_manager->updateTmpProducts($array_product[0],$key);
        }
        $logger->postMessage("Fine aggiornamento ","DEBUG");
        $buffer_manager = new ProductBufferTables();
        $buffer_manager->freeBufferTable();
    }
  
    /** 
    *Private function that formats Url
    *@params string $key
    *@return url formatting
    */
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