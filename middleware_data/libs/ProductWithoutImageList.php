<?php

require_once("settings.php");

class ProductWithoutImageList{

    private $activeProducts = null;
    private $notActiveProducts = null;
    private $listDirPath = null;
    private $productListFile = null;
    
    public function __construct(){
        $this->activeProducts = array();
        $this->notActiveProducts = array();
        $this->listDirPath = MD_ROOT."/product_list";
        if( !file_exists($this->listDirPath) ){
            mkdir($this->listDirPath);
        }
        $this->productListFile = $this->listDirPath.'/List.txt';
    }
    
    public function addProductToList($productReference, $active){
        //var result = file_put_contents($this->productListFile, $productReference, FILE_APPEND | LOCK_EX);
        if($active === TRUE)
            ($this->activeProducts).push($productReference);
        else ($this->notActiveProducts).push($productReference);
    }
    
    public function writeDownTheList(){
        echo "called writeDown<br>";
        if(count($this->activeProducts) > 0){
            file_put_contents($this->productListFile, "====Prodotti Attivi senza immagine (che verranno disattivati)====", FILE_APPEND | LOCK_EX);
            foreach($prod as $this->activeProducts)
                file_put_contents($this->productListFile, $prod, FILE_APPEND | LOCK_EX);
            file_put_contents($this->productListFile, "=======================", FILE_APPEND | LOCK_EX);
        }
        if(count($this->notActiveProducts) > 0){
            file_put_contents($this->productListFile, "====Prodotti non attivi senza immagine====", FILE_APPEND | LOCK_EX);
            foreach($prod as $this->notActiveProducts)
                file_put_contents($this->productListFile, $prod, FILE_APPEND | LOCK_EX);
            file_put_contents($this->productListFile, "=======================", FILE_APPEND | LOCK_EX);
        }
    }
    
}

?>