<?php
/*
* Mapping and Combinations related to file elements taken by input
*
* @author Valentino Vivone <v.vivone.5389@gmail.com>
* @version 1.0
*
*/

class Mapping{

    private $triple;
    private $duple;
    private $_TB_ART;
    private $_TB_ART_DET;
    private $_TB_ART_DIBA;
    private $_TB_ART_DESCR;
    private $_TB_ART_DET_FOTO;
    private $_TB_ART_DET_DISP;
    private $_TB_DESCR_LIN;
    private $keys;

    /*
		* Initializes inner logic structures.
		* 
		* @param string $path a string that represents a valid {TIMESTAMP}_SEM.chk pathname
		* @return
		* @see $this->createArray
		* @see $this->keys
		* @see $this->triple
		* @see $this->launchException
		*
		*/
    public function __construct($path){
        $tmp_array = $this->createArray($path);

        if(sizeof($tmp_array) === 1){
            throw new Exception("Il file semaforo è vuoto");
        }else{
            $tmp_path = "./files/";

            $this->_TB_ART = $this->createArray($tmp_path.$tmp_array[1]);

            if(!$this->launchException($this->_TB_ART,17)){
                throw new Exception("Il file ".$tmp_path.$tmp_array[1]."non rispetta la corretta sintassi oppure è vuoto");
            }

            //$this->_TB_ART_DESCR = $this->createArray($tmp_path.$tmp_array[2]);

            $this->_TB_ART_DET = $this->createArray($tmp_path.$tmp_array[3]);
            if(!$this->launchException($this->_TB_ART_DET,8)){
                throw new Exception("Il file ".$tmp_path.$tmp_array[3]."non rispetta la corretta sintassi oppure è vuoto");
            }

            $this->_TB_ART_DET_DISP = $this->createArray($tmp_path.$tmp_array[4]);
            if(!$this->launchException($this->_TB_ART_DET_DISP,5)){
                throw new Exception("Il file ".$tmp_path.$tmp_array[4]."non rispetta la corretta sintassi oppure è vuoto");
            }

            $this->_TB_ART_DET_FOTO = $this->createArray($tmp_path.$tmp_array[5]);
            if(!$this->launchException($this->_TB_ART_DET_FOTO,5)){
                throw new Exception("Il file ".$tmp_path.$tmp_array[5]."non rispetta la corretta sintassi oppure è vuoto");
            }

            //$this->_TB_ART_DIBA = $this->createArray($tmp_path.$tmp_array[6]);

            $this->_TB_DESCR_LIN = $this->createArray($tmp_path.$tmp_array[7]);
            if(!$this->launchException($this->_TB_DESCR_LIN,4)){
                throw new Exception("Il file ".$tmp_path.$tmp_array[7]."non rispetta la corretta sintassi oppure è vuoto");
            }

            $this->keys = $this->keys();
            $this->triple = $this->triple();
        }
    }

    /*
		* Controls if a file given as input is well-formed
		* 
		* @param array $array 
		* @param integer $expr_reg
		* @return bool
		*
		*/
    private function launchException($array, $expr_reg){
        $flag = true;
        if(sizeof($array) === 1){
            $flag = false;
        }else{
            $size = sizeof($array);
            for($i = 0; $i < $size; $i++){
                $tmp  = explode(";",$array[$i]);
                $size_tmp = sizeof($tmp); 
                if($size_tmp === 1) break;
                if($size_tmp != $expr_reg){
                    $flag = false;
                    break;
                }					
            }
        }
        return $flag;
    }

    /*
		* Sorts elements contained into $path file.
		* 
		* @param string $path
		* @return
		*
		*/
    private function sortFile($path){
        $path_file = array();
        $_TB = fopen(trim($path), "r") or die("Unable to open file!"); //Open file in 'r'

        while(!feof($_TB)) {
            $tmp = fgets($_TB);
            array_push($path_file, $tmp);
        }

        sort($path_file);

        $_TB = fopen(trim($path), "w") or die("Unable to open file!"); //Open file in 'w'
        $size = sizeof($path_file);
        for($i = 0; $i < $size; $i++){
            if($path_file[$i] !== "\n")
                fwrite($_TB,$path_file[$i]);
        }

        fclose($_TB);
    }

    /*
		* Extracts the file's elements and put them into an array.
		* 
		* @param string $path
		* @return array
		*
		*/
    private function extractInFile($path){
        $path_file = array();
        $_TB = fopen(trim($path), "r") or die("Unable to open file!"); //Open file in 'r'

        while(!feof($_TB)) {
            $tmp = fgets($_TB);
            array_push($path_file, $tmp);
        }

        return $path_file;
    }

    /*
		* Creates elements' arrays 
		* 
		* @param string $path
		* @see $this->sortFile
		* @see $this->extractInFile
		* @return array
		*
		*/
    private function createArray($path){
        $this->sortFile($path);
        return $this->extractInFile($path);
    }

    /*
		* Creates an arrays of keys. This keys are products codes. 
		* 
		* @return array
		*
		*/
    public function keys(){
        $array_tmp = array();
        $size = sizeof($this->_TB_ART);

        for($i = 0; $i < $size; $i++){
            $tmp = explode(";",$this->_TB_ART[$i]); //split the string on ';'.

            if(sizeof($tmp) === 1) 
                break;

            array_push($array_tmp,$tmp[0]);
        }

        return $array_tmp;

    }

    /*
		* Creates a key => value array where key is the product code and value is an array that contains
		* width, height, size, product model and collection
		* 
		* @return array
		*
		*/
    private function getArtAndAttribute(){
        $return = array();
        $size_array_TBART = sizeof($this->_TB_ART);
        $size_array_TBDESCRLIN = sizeof($this->_TB_DESCR_LIN);

        for($i = 0; $i < $size_array_TBART; $i++){
            $tmp = explode(";",$this->_TB_ART[$i]);

            if(sizeof($tmp) === 1) 
                break;

            $model = trim($tmp[6]);
            $collez = trim($tmp[3]);
            $name_model = "";
            $name_collez = "";

            for($j = 0; $j < $size_array_TBDESCRLIN; $j++){
                $tmp1 = explode(";",$this->_TB_DESCR_LIN[$j]);
                if(sizeof($tmp1) === 1) 
                    break;

                if($tmp1[0] === "COLLEZ" && $tmp1[2] === "IT"){
                    if($tmp1[1] === $collez){
                        $name_collez = trim($tmp1[3]);
                    }
                }
                if($tmp1[0] === "MODELLO" && $tmp1[2] === "IT"){
                    if($tmp1[1] === $model){
                        $name_model = trim($tmp1[3]);
                        break;
                    }
                }

            }

            $element = array();
            $element["Larghezza"] = str_replace(",",".",trim($tmp[12]));
            $element["Altezza"] = str_replace(",",".",trim($tmp[13]));
            $element["Lunghezza"] = str_replace(",",".",trim($tmp[11]));
            $element["Modello"] = str_replace("\"","",trim($name_model));
            $element["Linea"] = str_replace("\"","",trim($name_collez));

            $return[$tmp[0]] = $element;
        }

        return $return;
    }

    /*
		* Creates a  key => value array where key is formatted in the following way : "product code; product color code; product size code". value is formatted in the following way "product code; product color code; product size code".
		* 
		* @return array
		*
		*/
    public function triple(){
        $combinations = array();

        $size_array_TBARTDET = sizeof($this->_TB_ART_DET);
        $size_array_keys = sizeof($this->keys);

        $count = 0;
        for($j = 0; $j < $size_array_keys; $j++){
            $element = array();

            for ($i = $count; $i < $size_array_TBARTDET; $i++){
                $tmp = explode(";",$this->_TB_ART_DET[$i]);

                if(sizeof($tmp) === 1) 
                    break;

                if($this->keys[$j] === $tmp[0])
                    $element[$tmp[0].";".$tmp[1].";".$tmp[2]] = $tmp[0].";".$tmp[1].";".$tmp[2];
                else{
                    $count = $i;
                    break;
                }

            }

            $combinations[$this->keys[$j]] = $element;
        }

        return $combinations;

    }

    /*
		* Creates a key => value array where key is the product code, and value is "product code; product name; model code; supplier of the product"
		* @return array
		*
		*/
    private function getReferenceNameModelAndSupplier(){
        $return = array();
        $size_array_TBART = sizeof($this->_TB_ART);
        $size_array_TBDESCRLIN = sizeof($this->_TB_DESCR_LIN);

        for($i = 0; $i < $size_array_TBART; $i++){
            $tmp = explode(";",$this->_TB_ART[$i]);

            if(sizeof($tmp) === 1) 
                break;

            //Initialize the variables
            $reference = $tmp[0]; $name = null; $business_unit = null; $griffe = null;

            for($j = 0; $j < $size_array_TBDESCRLIN; $j++){
                $tmp1 = explode(";",$this->_TB_DESCR_LIN[$j]);

                if(sizeof($tmp1) === 1) 
                    break;

                //Research the code of product
                if($tmp1[0] === "CDMAT" and $tmp1[2] === "IT"){
                    if($tmp1[1] === $tmp[0]){
                        $name = trim($tmp1[3]);
                    }
                }

                //Research the model of product
                if($tmp1[0] === "BU" and $tmp1[2] === "IT"){
                    if($tmp1[1] === $tmp[4]){
                        $business_unit = trim($tmp1[3]);
                    }
                }

                //Research the brand of product
                if($tmp1[0] === "GRIFFE" and $tmp1[2] === "IT"){
                    if($tmp1[1] === $tmp[2]){
                        $griffe = trim($tmp1[3]);
                    }
                }			

            }

            //Remove \" of the strings
            $name = str_replace("\"","",$name);
            $business_unit = str_replace("\"","",$business_unit);
            $griffe = str_replace("\"","",$griffe);

            $return[$reference] = $reference.";".$name.";".$business_unit.";".$griffe;
        }

        return $return;
    }

    /*
		* Creates a key => value array where key is the product code and value is "product price; flag of salable product"
		* 
		* @return array
		*
		*/
    private function getPriceAndSalable(){			
        $return = array();
        $size_key = sizeof($this->keys);
        $size_array_TBARTDET =  sizeof($this->_TB_ART_DET);

        $count = 0;
        for($i = 0; $i < $size_key; $i++){
            $flag = 0;
            $price = "";

            for($j = $count; $j < $size_array_TBARTDET; $j++){
                $tmp = explode(";",$this->_TB_ART_DET[$j]);

                if(sizeof($tmp) === 1) 
                    break;

                if($this->keys[$i] === $tmp[0]){
                    $price = str_replace(",",".",trim($tmp[4]));
                    if($tmp[6] === "S"){
                        $flag = 1;
                    }
                }else{
                    $count = $j;
                    break;
                }

            }

            $return[$this->keys[$i]] = $price.";".$flag;
        }

        return $return;

    }

    //DA DOMANDARE se si deve considerare la combinazione o i singoli per quanto riguarda la quantità
    /*private function getQuantity(){
			//Con questa funzione avremo la quantità e la quantità minima del singolo 
			//articolo contenuto nel file _TB_ART_DET_DISP. 
			//Tali caratteristiche saranno ricavate andando a cercare il codice dell'articolo.

			$return = array();
			$sizeKey = sizeof($this->keys);
			$sizeArrayTBARTDETDISP =  sizeof($this->_TB_ART_DET_DISP);

			//$count viene utilizzato come indice di scorrimento. (poichè gli elementi nel file sono stati ordinati, è inutile andare
			//a scorrere tutto il file; bensì si salva l'ultimo indice e si riparte da questo).
			$count = 0;
			for($i = 0; $i < $sizeKey; $i++){
				$element = array();
				for($j = $count; $j < $sizeArrayTBARTDETDISP; $j++){
					$tmp = explode(";",$this->_TB_ART_DET_DISP[$j]);
					if(sizeof($tmp) === 1) 
						break;
					$tripleTBARTDET = $tmp[0].";".$tmp[1].";".$tmp[2];
					if($this->keys[$i] === $tmp[0]){
						$element[$tripleTBARTDET] = $tmp[4].";1";//Qtà;1 => Qtà: quantità, 1: quantità minima dell'articolo (DA DOMANDARE)
					}else{
						$count = $j;
						break;
					}
				}
				$return[$this->keys[$i]] = $element;
			}

			return $return;

			//Esempio di Output:
			//[KEY1] => QTA';1
			//[KEY2] => QTA';1
		}*/

    /*
		* Creates a key => value array where key is the product code and value is "product quantity; min product quantity"
		* 
		* @return array
		*
		*/
    private function getQuantity(){
        $return = array();
        $size_key = sizeof($this->keys);
        $size_array_TBARTDETDISP =  sizeof($this->_TB_ART_DET_DISP);

        $count = 0;
        for($i = 0; $i < $size_key; $i++){
            $tot_qta = 0;

            for($j = $count; $j < $size_array_TBARTDETDISP; $j++){
                $tmp = explode(";",$this->_TB_ART_DET_DISP[$j]);

                if(sizeof($tmp) === 1) 
                    break;

                if($this->keys[$i] === $tmp[0]){
                    $tot_qta = $tot_qta + $tmp[4];
                }else{
                    $count = $j;
                    break;
                }

            }
            $return[$this->keys[$i]] = $tot_qta.";1";
        }

        return $return;
    }

    /*
		* Creates a key => value array where key is product code and value is "product code; product color code".
		* 
		* @return array
		*
		*/
    private function duple(){		
        $combinations = array();
        $size_array_TBARTDETFOTO = sizeof($this->_TB_ART_DET_FOTO);

        for ($i = 0; $i < $size_array_TBARTDETFOTO; $i++){
            $tmp = explode(";",$this->_TB_ART_DET_FOTO[$i]);

            if(sizeof($tmp) === 1) 
                break;

            $combinations[$tmp[0]] = $tmp[0].";".$tmp[1];
        }

        return $combinations;

    }

    /*
		* Creates a key => value array where key is the product code and value is a key => value array with: key = key is the duple representing product code and product color; value = product photo name.
		* 
		* @return array
		*
		*/
    private function getUrlFoto(){
        $return = array();
        $size_key = sizeof($this->keys);
        $size_array_TBARTDETFOTO =  sizeof($this->_TB_ART_DET_FOTO);

        $count = 0;
        for($i = 0; $i < $size_key; $i++){
            $element = array();

            for($j = $count; $j < $size_array_TBARTDETFOTO; $j++){
                $tmp = explode(";",$this->_TB_ART_DET_FOTO[$j]);

                if(sizeof($tmp) === 1) 
                    break;

                $duple_TBARTDET = $tmp[0].";".$tmp[1];

                if($this->keys[$i] === $tmp[0]){
                    $element[$duple_TBARTDET] = trim($tmp[4]);
                }else{
                    $count = $j;
                    break;
                }

            }

            $return[$this->keys[$i]] = $element;
        }
        return $return;
    }

    /*
		* Creates a string with the following format: "color code: color name".
		* 
		* @param string $cod_color
		* @return string
		*
		*/
    private function getColors($cod_color){
        $return = ":";
        $size_array_TBDESCRLIN = sizeof($this->_TB_DESCR_LIN);

        if($cod_color != ""){
            for($i = 0; $i < $size_array_TBDESCRLIN; $i++){
                $tmp = explode(";",$this->_TB_DESCR_LIN[$i]);

                if(sizeof($tmp) === 1) 
                    break;

                if($tmp[0] === "COLORE" && $tmp[1] === $cod_color && $tmp[2] === "IT"){
                    $return = $cod_color.":".str_replace("\"","",trim($tmp[3]));
                    break;
                }

            }
        }

        return $return;
    }

    /*
		* Creates string representing a single product quantity.
		* 
		* @param string $cod_size
		* @param string $cod_art
		* @param string $cod_color
		* @return string
		*
		*/
    private function getQuantityForCombinations($cod_art, $cod_color, $cod_size){
        $return = 0;
        $size_array_TBARTDETDISP = sizeof($this->_TB_ART_DET_DISP);


        for($i = 0; $i < $size_array_TBARTDETDISP; $i++){
            $tmp = explode(";",$this->_TB_ART_DET_DISP[$i]);

            if(sizeof($tmp) === 1) 
                break;

            if($tmp[0] === $cod_art && $tmp[1] === $cod_color && $tmp[2] === $cod_size){
                $return = (float)$tmp[4];
                break;
            }

        }


        return $return;
    }

    /*
		* Creates a "product size: product name" string.
		* 
		* @param string $cod_size
		* @return string
		*
		*/
    private function getSizes($cod_size){
        $return = ":";
        $sizeArrayTBDESCRLIN = sizeof($this->_TB_DESCR_LIN);

        if($cod_size != ""){
            for($i = 0; $i < $sizeArrayTBDESCRLIN; $i++){
                $tmp = explode(";",$this->_TB_DESCR_LIN[$i]);

                if(sizeof($tmp) === 1) 
                    break;

                if($tmp[0] === "TAGLIA" && $tmp[1] === $cod_size && $tmp[2] === "IT"){
                    $return = $cod_size.":".str_replace("\"","",$tmp[3]);
                    break;
                }

            }
        }

        return $return;
    }

    /*
		* Creates string representing a photo name.
		* 
		* @param string $cod_size
		* @return string
		*
		*/
    private function getImages($cod_art,$cod_color){
        //La funzione crea una combinazione di immagini associata all articolo preso in oggetto.
        $return = "";
        $size_array_TBFOTO = sizeof($this->_TB_ART_DET_FOTO);

        if($cod_color != "" && $cod_art != ""){
            for($i = 0; $i < $size_array_TBFOTO; $i++){
                $tmp = explode(";",$this->_TB_ART_DET_FOTO[$i]);

                if(sizeof($tmp) === 1) 
                    break;

                if($tmp[0] === $cod_art && $tmp[1] === $cod_color){
                    $return = $return.trim($tmp[4]).",";
                }

            }
        }

        return $return;
    }

    /*
		* Creates a key => value array where key is the elements duple (product code and color code), and value is an array of triple (product code, color code, size code).
		* @see $this->getColors
		* @see $this->getSizes
		* @see $this->getImages
		* @see $this->getQuantityForCombinations
		*
		* @return array
		*
		*/
    private function getAttributesValuesAndImagesForCombinations(){
        $return = array();
        $size_array_keys = sizeof($this->keys);

        for($i = 0; $i < $size_array_keys; $i++){
            $keys_array_triple = $this->triple[$this->keys[$i]];
            $size_array_keys_triple = sizeof($keys_array_triple);
            $element = array();

            foreach($keys_array_triple as $value){
                $tmp  = explode(";",$value);

                if(sizeof($tmp) === 1) 
                    break;

                $color = $this->getColors($tmp[1]);
                $size = $this->getSizes($tmp[2]);
                $image = $this->getImages($tmp[0],$tmp[1]);
                $quantity = $this->getQuantityForCombinations($tmp[0],$tmp[1],$tmp[2]);

                $tmp_color = explode(":",$color);
                $tmp_size = explode(":",$size);

                $attributes = "COLORE,TAGLIA";
                $values = $tmp_color[1].",".$tmp_size[1];
                $element[$value] = array("Attribute" => $attributes, "Value" => $values, "Quantity" => $quantity, "Image" => $image);
            }

            $return[$this->keys[$i]] = $element;
        }

        return $return;

    }

    /*
		* Creates a Mapping array with "key = product code"
		* 
		* @see $this->getPriceAndSalable
		* @see $this->getReferenceNameModelAndSupplier
		* @see $this->getQuantity
		* @see $this->getUrlFoto
		* @see $this->getArtAndAttribute
		*
		* @return array
		*
		*/
    public function getItemMaster(){
        $return = array();
        $priceAndSalable = $this->getPriceAndSalable();
        $referenceNameModelAndSupplier = $this->getReferenceNameModelAndSupplier();
        $quantity = $this->getQuantity();
        $urlFoto = $this->getUrlFoto();
        $feature = $this->getArtAndAttribute();

        foreach($this->keys as $keys){
            $element = array();
            $tmp = explode(";",$priceAndSalable[$keys]);
            $element["Prezzo"] = $tmp[0];
            $element["Attivo"] = $tmp[1];

            $tmp = explode(";",$referenceNameModelAndSupplier[$keys]);
            $element["Reference"] = $tmp[0];
            $element["Nome"] = $tmp[1];
            $element["Categorie"] = $tmp[2].",".$tmp[3];
            $element["Supplier"] = $tmp[3];
            $element["Manufacture"] = $tmp[3];

            $tmp = explode(";",$quantity[$keys]);
            $element["Qta"] = $tmp[0];
            $element["Qta_min"] = $tmp[1];

            $element["Feature"] = $feature[$keys];

            $stringTmp = "";
            foreach($urlFoto[$keys] as $singleFoto){
                $stringTmp = $stringTmp.$singleFoto.",";
            }
            $element["URL"] = $stringTmp;

            $return[$keys] = $element;
        }

        return $return;
    }

    /*
		* Creates a Combinations array with "key = triple"
		* 
		* @see $this->getAttributesValuesAndImagesForCombinations
		*
		* @return array
		*
		*/
    public function getCombinations(){
        $return = array();
        $combinations = $this->getAttributesValuesAndImagesForCombinations();

        foreach($this->keys as $keys){
            $element = array();
            $element["Product_reference"] = $keys;
            $tmp = $combinations[$keys];

            foreach($this->triple[$keys] as $combo){
                $sigle_attribute_value_and_image = $tmp[$combo];
                $element[$combo] = array("Attributi" => $sigle_attribute_value_and_image["Attribute"],"Valori" => $sigle_attribute_value_and_image["Value"], "Qta" => $sigle_attribute_value_and_image["Quantity"], "Immagine" => $sigle_attribute_value_and_image["Image"]);
            }

            $return[$keys] = $element;
        }

        return $return;
    }

}





?>