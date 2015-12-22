<?php

require_once ("settings.php");
require_once (MD_PKGS_DIR."/Mapping.php");
require_once (MD_DBMANAGER_DIR."/connection.php");
require_once (MD_LIBS_DIR."/HandleOperationsException.php");
require_once (MD_LIBS_DIR."/Logger.php");

/**
* This class handles views in database.
* @author Valentino Vivone <v.vivone.5389@gmail.com>
* @version 1.0
*
*/
class ViewManager{
    private $logger=null;

    public function __construct(){
        $this->logger = new Logger();
    }

    /**
	* Get all products contain in the view new_products.
	*
	* @return array of elements if this view contains their, else return false.
	* @see MappingArray createSingleArrayTriple()
	* @see MappingArray createSingleArrayMapping()
	* @see MappingArray createSingleArrayCombination()
	*
	*/
    public function getNewProduct(){
        $connection = connectionServer();
        $mapping = new MappingArray();

        $active = "";	$category = "";	 $price = "";	$supplier = ""; $name = "";	$collection = "";
        $manufacture = "";	$qta = "";	$qta_min = "";	$size = "";	$height = "";	$width = "";
        $name_color = array();	$quantity = array();	$name_size = array();	$model = "";
        $code_color = array();	$url = "";	$name_photo = array();	$code_size = array();

        $sql = "SELECT ps_tmp_product.ps_id, ps_buffer_product.reference, ps_buffer_product.attivo, ps_buffer_product.categoria, 
ps_buffer_product.prezzo, ps_buffer_product.supplier, ps_buffer_product.manufacture, ps_buffer_product.qta, 
ps_buffer_product.qta_min, ps_buffer_product.lunghezza, ps_buffer_product.altezza, ps_buffer_product.larghezza, ps_buffer_product.colore,
ps_buffer_product.quantita,ps_buffer_product.taglia, ps_buffer_product.nome, ps_buffer_product.modello,ps_buffer_product.linea, 
ps_buffer_product.codice_colore, ps_buffer_product.url, ps_buffer_product.immagine, ps_buffer_product.codice_taglia FROM ps_buffer_product LEFT JOIN ps_tmp_product ON ps_buffer_product.reference = ps_tmp_product.reference WHERE( ps_tmp_product.ps_id IS NULL)ORDER BY ps_buffer_product.reference ASC";

        $to_return = array();
        $result = mysql_query($sql,$connection);

        if (mysql_num_rows($result) > 0){
            $new_reference ='';
            while($row = mysql_fetch_array( $result )){

                if(empty($new_reference)){
                    $new_reference = $row[1];
                }

                if($new_reference === $row[1]){
                    $active = $row[2];	$category = $row[3];	$price = $row[4];
                    $supplier = $row[5];	$manufacture = $row[6];	$qta = $row[7];
                    $qta_min = $row[8]; $size = $row[9];	$height = $row[10];
                    $width = $row[11];	array_push($name_color, $row[12]);	array_push($quantity, $row[13]);
                    array_push($name_size, $row[14]);	$name = $row[15];	$model = $row[16];
                    $collection = $row[17];	array_push($code_color, $row[18]);	$url = $row[19];
                    array_push($name_photo, $row[20]);	array_push($code_size, $row[21]);
                }else{
                    $array_mapping = $mapping->createSingleArrayMapping($price, $active, $new_reference, $name, 
                                                                        $category, $supplier, $manufacture, $qta, $qta_min,$width, 
                                                                        $height, $size, $model, $collection, $url);

                    $array_triple = $mapping->createSingleArrayTriple($new_reference, $code_color, $code_size);

                    $array_combinations = $mapping->createSingleArrayCombination($new_reference, $code_color, $code_size, $name_color, 
                                                                                 $name_size, $name_photo, $quantity); 
                    $element = array();
                    array_push($element,$array_mapping);
                    array_push($element,$array_triple);
                    array_push($element,$array_combinations);

                    array_push($to_return,$element);
                    $new_reference = $row[1];

                    $name_color = array();	$quantity = array();	$name_size = array();
                    $code_color = array();	$name_photo = array();	$code_size = array();

                    $active = $row[2];	$category = $row[3];	$price = $row[4];	$supplier = $row[5];	
                    $manufacture = $row[6];	$qta = $row[7];	$qta_min = $row[8]; $size = $row[9];
                    $height = $row[10];	$width = $row[11];	array_push($name_color, $row[12]);	
                    array_push($quantity, $row[13]);	array_push($name_size, $row[14]);	$name = $row[15];	
                    $model = $row[16];	$collection = $row[17];	array_push($code_color, $row[18]);	
                    $url = $row[19];	array_push($name_photo, $row[20]);	array_push($code_size, $row[21]);

                }
            }

            $array_mapping = $mapping->createSingleArrayMapping($price, $active, $new_reference, $name, $category, $supplier, 
                                                                $manufacture, $qta, $qta_min,$width, $height, $size, $model, 
                                                                $collection, $url);

            $array_triple = $mapping->createSingleArrayTriple($new_reference, $code_color, $code_size);

            $array_combinations = $mapping->createSingleArrayCombination($new_reference, $code_color, $code_size, 
                                                                         $name_color, $name_size, $name_photo, $quantity); 

            $element = array();
            array_push($element,$array_mapping);
            array_push($element,$array_triple);
            array_push($element,$array_combinations);

            array_push($to_return,$element);

            closeConnectionServer($connection);
            return $to_return;    
        } else {
            closeConnectionServer($connection);
            return FALSE;
        }
    }

    /**
	* Get all products contain in the view products_differences.
	*
	* @return array of elements if this view contains their, else return false.
	* @see MappingArray createSingleArrayTriple()
	* @see MappingArray createSingleArrayMapping()
	* @see MappingArray createSingleArrayCombination()
	*
	*/
    public function getProductDifferences(){
        $connection = connectionServer();
        $mapping = new MappingArray();

        $ps_id = "";  $active = "";	$category = "";	 $price = "";	$supplier = ""; $name = "";	$collection = "";
        $manufacture = "";	$qta = "";	$qta_min = "";	$size = "";	$height = "";	$width = "";
        $name_color = array();	$quantity = array();	$name_size = array();	$model = "";
        $code_color = array();	$url = "";	$name_photo = array();	$code_size = array();

        $sql = "SELECT ps_tmp_product.ps_id,ps_buffer_product.reference, ps_buffer_product.attivo, ps_buffer_product.categoria, 
ps_buffer_product.prezzo, ps_buffer_product.supplier, ps_buffer_product.manufacture, ps_buffer_product.qta, 
ps_buffer_product.qta_min, ps_buffer_product.lunghezza, ps_buffer_product.altezza, ps_buffer_product.larghezza, ps_buffer_product.colore,
ps_buffer_product.quantita,ps_buffer_product.taglia, ps_buffer_product.nome, ps_buffer_product.modello,ps_buffer_product.linea, 
ps_buffer_product.codice_colore, ps_buffer_product.url, ps_buffer_product.immagine, ps_buffer_product.codice_taglia FROM ps_buffer_product, ps_tmp_product WHERE ((ps_buffer_product.reference = ps_tmp_product.reference
AND ps_buffer_product.codice_colore = ps_tmp_product.codice_colore AND ps_buffer_product.codice_taglia = ps_tmp_product.codice_taglia )AND( ps_buffer_product.attivo <> ps_tmp_product.attivo 
OR ps_buffer_product.prezzo <> ps_tmp_product.prezzo OR ps_buffer_product.qta <> ps_tmp_product.qta OR ps_buffer_product.qta_min <> ps_tmp_product.qta_min 
OR ps_buffer_product.lunghezza <> ps_tmp_product.lunghezza OR ps_buffer_product.altezza <> ps_tmp_product.altezza
OR ps_buffer_product.larghezza <> ps_tmp_product.larghezza OR ps_buffer_product.quantita <> ps_tmp_product.quantita
OR ps_buffer_product.nome <> ps_tmp_product.nome OR ps_buffer_product.modello <> ps_tmp_product.modello OR ps_buffer_product.immagine <> ps_tmp_product.immagine 
OR ps_buffer_product.linea <> ps_tmp_product.linea )) ORDER BY ps_buffer_product.reference ASC";

        $to_return = array();
        $result = mysql_query($sql,$connection);

        if (mysql_num_rows($result) > 0){
            $new_reference ='';
            while($row = mysql_fetch_array( $result )){

                if(empty($new_reference)){
                    $new_reference = $row[1];
                }

                if($new_reference === $row[1]){
                    $ps_id = $row[0];   $active = $row[2];	$category = $row[3];	$price = $row[4];
                    $supplier = $row[5];	$manufacture = $row[6];	$qta = $row[7];
                    $qta_min = $row[8]; $size = $row[9];	$height = $row[10];
                    $width = $row[11];	array_push($name_color, $row[12]);	array_push($quantity, $row[13]);
                    array_push($name_size, $row[14]);	$name = $row[15];	$model = $row[16];
                    $collection = $row[17];	array_push($code_color, $row[18]);	$url = $row[19];
                    array_push($name_photo, $row[20]);	array_push($code_size, $row[21]);
                }else{
                    $array_mapping = $mapping->createSingleArrayMapping($price, $active, $new_reference, $name, 
                                                                        $category, $supplier, $manufacture, $qta, $qta_min,$width, 
                                                                        $height, $size, $model, $collection, $url);

                    $array_triple = $mapping->createSingleArrayTriple($new_reference, $code_color, $code_size);

                    $array_combinations = $mapping->createSingleArrayCombination($new_reference, $code_color, $code_size, $name_color, 
                                                                                 $name_size, $name_photo, $quantity); 
                    $element = array();
                    array_push($element,$array_mapping);
                    array_push($element,$array_triple);
                    array_push($element,$array_combinations);
                    array_push($element,$ps_id);

                    array_push($to_return,$element);
                    $new_reference = $row[1];

                    $name_color = array();	$quantity = array();	$name_size = array();
                    $code_color = array();	$name_photo = array();	$code_size = array();

                    $ps_id = $row[0];   $active = $row[2];	$category = $row[3];	$price = $row[4];	
                    $supplier = $row[5];	
                    $manufacture = $row[6];	$qta = $row[7];	$qta_min = $row[8]; $size = $row[9];
                    $height = $row[10];	$width = $row[11];	array_push($name_color, $row[12]);	
                    array_push($quantity, $row[13]);	array_push($name_size, $row[14]);	$name = $row[15];	
                    $model = $row[16];	$collection = $row[17];	array_push($code_color, $row[18]);	
                    $url = $row[19];	array_push($name_photo, $row[20]);	array_push($code_size, $row[21]);

                }
            }

            $array_mapping = $mapping->createSingleArrayMapping($price, $active, $new_reference, $name, $category, $supplier, 
                                                                $manufacture, $qta, $qta_min,$width, $height, $size, $model, 
                                                                $collection, $url);

            $array_triple = $mapping->createSingleArrayTriple($new_reference, $code_color, $code_size);

            $array_combinations = $mapping->createSingleArrayCombination($new_reference, $code_color, $code_size, 
                                                                         $name_color, $name_size, $name_photo, $quantity); 

            $element = array();
            array_push($element,$array_mapping);
            array_push($element,$array_triple);
            array_push($element,$array_combinations);
            array_push($element,$ps_id);

            array_push($to_return,$element);

            closeConnectionServer($connection);
            return $to_return;    
        } else {
            closeConnectionServer($connection);
            return FALSE;
        }
    }

    /**
	* Gets all products contain in the buffer.
	*
	* @return array of elements if this view contains their, else return false.
	* @see MappingArray createSingleArrayTriple()
	* @see MappingArray createSingleArrayMapping()
	* @see MappingArray createSingleArrayCombination()
	*
	*/
    public function getAllProducts(){
        $connection = connectionServer();
        $mapping = new MappingArray();
        $active = "";	$category = "";	 $price = "";	$supplier = ""; $name = "";	$collection = "";
        $manufacture = "";	$qta = "";	$qta_min = "";	$size = "";	$height = "";	$width = "";
        $name_color = array();	$quantity = array();	$name_size = array();	$model = "";
        $code_color = array();	$url = "";	$name_photo = array();	$code_size = array();

        $sql = "SELECT * FROM ps_buffer_product WHERE 1 ORDER BY reference ASC";

        $to_return = array();
        $result = mysql_query($sql,$connection);

        if (mysql_num_rows($result) > 0){
            $new_reference ='';
            while($row = mysql_fetch_array( $result )){

                if(empty($new_reference)){
                    $new_reference = $row[0];
                }

                if($new_reference === $row[0]){
                    $active = $row[1];	$category = $row[2];	$price = $row[3];
                    $supplier = $row[4];	$manufacture = $row[5];	$qta = $row[6];
                    $qta_min = $row[7]; $size = $row[8];	$height = $row[9];
                    $width = $row[10];	array_push($name_color, $row[11]);	array_push($quantity, $row[12]);
                    array_push($name_size, $row[13]);	$name = $row[14];	$model = $row[15];
                    $collection = $row[16];	array_push($code_color, $row[17]);	$url = $row[19];
                    array_push($name_photo, $row[20]);	array_push($code_size, $row[18]);
                }else{
                    $array_mapping = $mapping->createSingleArrayMapping($price, $active, $new_reference, $name, 
                                                                        $category, $supplier, $manufacture, $qta, $qta_min,$width, 
                                                                        $height, $size, $model, $collection, $url);

                    $array_triple = $mapping->createSingleArrayTriple($new_reference, $code_color, $code_size);

                    $array_combinations = $mapping->createSingleArrayCombination($new_reference, $code_color, $code_size, $name_color, 
                                                                                 $name_size, $name_photo, $quantity); 
                    $element = array();
                    array_push($element,$array_mapping);
                    array_push($element,$array_triple);
                    array_push($element,$array_combinations);

                    array_push($to_return,$element);
                    $new_reference = $row[0];

                    $name_color = array();	$quantity = array();	$name_size = array();
                    $code_color = array();	$name_photo = array();	$code_size = array();

                    $active = $row[1];	$category = $row[2];	$price = $row[3];
                    $supplier = $row[4];	$manufacture = $row[5];	$qta = $row[6];
                    $qta_min = $row[7]; $size = $row[8];	$height = $row[9];
                    $width = $row[10];	array_push($name_color, $row[11]);	array_push($quantity, $row[12]);
                    array_push($name_size, $row[13]);	$name = $row[14];	$model = $row[15];
                    $collection = $row[16];	array_push($code_color, $row[17]);	$url = $row[19];
                    array_push($name_photo, $row[20]);	array_push($code_size, $row[18]);

                }
            }

            $array_mapping = $mapping->createSingleArrayMapping($price, $active, $new_reference, $name, $category, $supplier, 
                                                                $manufacture, $qta, $qta_min,$width, $height, $size, $model, 
                                                                $collection, $url);

            $array_triple = $mapping->createSingleArrayTriple($new_reference, $code_color, $code_size);

            $array_combinations = $mapping->createSingleArrayCombination($new_reference, $code_color, $code_size, 
                                                                         $name_color, $name_size, $name_photo, $quantity); 

            $element = array();
            array_push($element,$array_mapping);
            array_push($element,$array_triple);
            array_push($element,$array_combinations);

            array_push($to_return,$element);

            closeConnectionServer($connection);

            return $to_return;    
        } else {
            closeConnectionServer($connection);
            return 0;
        }
    }

    /**
	* Gets all products contain in the view products_differences.
	*
	* @return array of elements if this view contains their, else return false.
	* @see MappingArray createSingleArrayTriple()
	* @see MappingArray createSingleArrayMapping()
	* @see MappingArray createSingleArrayCombination()
	*
	*/
    public function getSqlNewProducts($array_product,$key){
        $connection = connectionServer();
           
        $sql = "SELECT ps_tmp_product.ps_id, ps_buffer_product.reference, ps_buffer_product.attivo, ps_buffer_product.categoria, 
ps_buffer_product.prezzo, ps_buffer_product.supplier, ps_buffer_product.manufacture, ps_buffer_product.qta, 
ps_buffer_product.qta_min, ps_buffer_product.lunghezza, ps_buffer_product.altezza, ps_buffer_product.larghezza, ps_buffer_product.colore,
ps_buffer_product.quantita,ps_buffer_product.taglia, ps_buffer_product.nome, ps_buffer_product.modello,ps_buffer_product.linea, 
ps_buffer_product.codice_colore, ps_buffer_product.url, ps_buffer_product.immagine, ps_buffer_product.codice_taglia FROM ps_buffer_product LEFT JOIN ps_tmp_product ON ps_buffer_product.reference = ps_tmp_product.reference WHERE( ps_tmp_product.ps_id IS NULL AND  ps_buffer_product.reference ='".$key."' ) ORDER BY ps_buffer_product.reference ASC";
        $to_return = array();
        $result = mysql_query($sql,$connection);
        if($result){
            if (mysql_num_rows($result) > 0){
                $to_return = array();
                while($row = mysql_fetch_array( $result )){       
                    $sql = "INSERT INTO ps_tmp_product (ps_id, reference, attivo, categoria, prezzo, supplier, 
                    manufacture, qta, qta_min, lunghezza, altezza, larghezza, colore, quantita, taglia, 
						nome, modello, linea, codice_colore, url, immagine, codice_taglia )
						VALUES('".$array_product."','".$key."','".$row[2]."',
							   '".$row[3]."','".$row[4]."','".$row[5]."','".$row[6]."',
							   '".$row[7]."','".$row[8]."','".$row[9]."','".$row[10]."',
							   '".$row[11]."','".$row[12]."','".$row[13]."','".$row[14]."',
							   '".$row[15]."','".$row[16]."','".$row[17]."','".$row[18]."',
							   '".$row[19]."','".$row[20]."','".$row[21]."')";

                    array_push($to_return,$sql); 
                }
                closeConnectionServer($connection);
                return $to_return ;
            }else return array();
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
        closeConnectionServer($connection);
    }
    
    public function getSqlColorsAndSizeProducts($array_product,$key){
        $connection = connectionServer();
        
         
        $sql = "SELECT ps_tmp_product.ps_id, ps_buffer_product.reference, ps_buffer_product.attivo, ps_buffer_product.categoria, 
ps_buffer_product.prezzo, ps_buffer_product.supplier, ps_buffer_product.manufacture, ps_buffer_product.qta, 
ps_buffer_product.qta_min, ps_buffer_product.lunghezza, ps_buffer_product.altezza, ps_buffer_product.larghezza, ps_buffer_product.colore,
ps_buffer_product.quantita,ps_buffer_product.taglia, ps_buffer_product.nome, ps_buffer_product.modello,ps_buffer_product.linea, 
ps_buffer_product.codice_colore, ps_buffer_product.url, ps_buffer_product.immagine, ps_buffer_product.codice_taglia FROM ps_buffer_product LEFT JOIN ps_tmp_product ON (ps_buffer_product.reference = ps_tmp_product.reference AND ps_buffer_product.codice_colore = ps_tmp_product.codice_colore AND ps_buffer_product.codice_taglia = ps_tmp_product.codice_taglia) WHERE( ps_tmp_product.ps_id IS NULL AND ps_buffer_product.reference ='".$key."')";
        $to_return = array();
        $result = mysql_query($sql,$connection);
        if($result){
            if (mysql_num_rows($result) > 0){
                $to_return = array();
                while($row = mysql_fetch_array( $result )){       
                    $sql = "INSERT INTO ps_tmp_product (ps_id, reference, attivo, categoria, prezzo, supplier, 
                    manufacture, qta, qta_min, lunghezza, altezza, larghezza, colore, quantita, taglia, 
						nome, modello, linea, codice_colore, url, immagine, codice_taglia )
						VALUES('".$array_product."','".$key."','".$row[2]."',
							   '".$row[3]."','".$row[4]."','".$row[5]."','".$row[6]."',
							   '".$row[7]."','".$row[8]."','".$row[9]."','".$row[10]."',
							   '".$row[11]."','".$row[12]."','".$row[13]."','".$row[14]."',
							   '".$row[15]."','".$row[16]."','".$row[17]."','".$row[18]."',
							   '".$row[19]."','".$row[20]."','".$row[21]."')";
                    
                    array_push($to_return,$sql); 
                }
                closeConnectionServer($connection);
                return $to_return ;
            }else return array();
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
        closeConnectionServer($connection);
    }

    
    public function getColorsAndSizesDifferences(){
        $connection = connectionServer();
        $mapping = new MappingArray();

        $ps_id = "";  $active = "";	$category = "";	 $price = "";	$supplier = ""; $name = "";	$collection = "";
        $manufacture = "";	$qta = "";	$qta_min = "";	$size = "";	$height = "";	$width = "";
        $name_color = array();	$quantity = array();	$name_size = array();	$model = "";
        $code_color = array();	$url = "";	$name_photo = array();	$code_size = array();

        $sql = "SELECT ps_tmp_product.ps_id, ps_buffer_product.reference, ps_buffer_product.attivo, ps_buffer_product.categoria, 
ps_buffer_product.prezzo, ps_buffer_product.supplier, ps_buffer_product.manufacture, ps_buffer_product.qta, 
ps_buffer_product.qta_min, ps_buffer_product.lunghezza, ps_buffer_product.altezza, ps_buffer_product.larghezza, ps_buffer_product.colore,
ps_buffer_product.quantita,ps_buffer_product.taglia, ps_buffer_product.nome, ps_buffer_product.modello,ps_buffer_product.linea, 
ps_buffer_product.codice_colore, ps_buffer_product.url, ps_buffer_product.immagine, ps_buffer_product.codice_taglia FROM ps_buffer_product LEFT JOIN ps_tmp_product ON (ps_buffer_product.reference = ps_tmp_product.reference AND ps_buffer_product.codice_colore = ps_tmp_product.codice_colore AND ps_buffer_product.codice_taglia = ps_tmp_product.codice_taglia) WHERE( ps_tmp_product.ps_id IS NULL) ORDER BY ps_buffer_product.reference ASC";

        $to_return = array();
        $result = mysql_query($sql,$connection);

        if (mysql_num_rows($result) > 0){
            $new_reference ='';
            while($row = mysql_fetch_array( $result )){

                if(empty($new_reference)){
                    $new_reference = $row[1];
                }

                if($new_reference === $row[1]){
                    $ps_id = $row[0];   $active = $row[2];	$category = $row[3];	$price = $row[4];
                    $supplier = $row[5];	$manufacture = $row[6];	$qta = $row[7];
                    $qta_min = $row[8]; $size = $row[9];	$height = $row[10];
                    $width = $row[11];	array_push($name_color, $row[12]);	array_push($quantity, $row[13]);
                    array_push($name_size, $row[14]);	$name = $row[15];	$model = $row[16];
                    $collection = $row[17];	array_push($code_color, $row[18]);	$url = $row[19];
                    array_push($name_photo, $row[20]);	array_push($code_size, $row[21]);
                }else{
                    $array_mapping = $mapping->createSingleArrayMapping($price, $active, $new_reference, $name, 
                                                                        $category, $supplier, $manufacture, $qta, $qta_min,$width, 
                                                                        $height, $size, $model, $collection, $url);

                    $array_triple = $mapping->createSingleArrayTriple($new_reference, $code_color, $code_size);

                    $array_combinations = $mapping->createSingleArrayCombination($new_reference, $code_color, $code_size, $name_color, 
                                                                                 $name_size, $name_photo, $quantity); 
                    $element = array();
                    array_push($element,$array_mapping);
                    array_push($element,$array_triple);
                    array_push($element,$array_combinations);
                    array_push($element,$ps_id);

                    array_push($to_return,$element);
                    $new_reference = $row[1];

                    $name_color = array();	$quantity = array();	$name_size = array();
                    $code_color = array();	$name_photo = array();	$code_size = array();

                    $ps_id = $row[0];   $active = $row[2];	$category = $row[3];	$price = $row[4];	
                    $supplier = $row[5];	
                    $manufacture = $row[6];	$qta = $row[7];	$qta_min = $row[8]; $size = $row[9];
                    $height = $row[10];	$width = $row[11];	array_push($name_color, $row[12]);	
                    array_push($quantity, $row[13]);	array_push($name_size, $row[14]);	$name = $row[15];	
                    $model = $row[16];	$collection = $row[17];	array_push($code_color, $row[18]);	
                    $url = $row[19];	array_push($name_photo, $row[20]);	array_push($code_size, $row[21]);

                }
            }

            $array_mapping = $mapping->createSingleArrayMapping($price, $active, $new_reference, $name, $category, $supplier, 
                                                                $manufacture, $qta, $qta_min,$width, $height, $size, $model, 
                                                                $collection, $url);

            $array_triple = $mapping->createSingleArrayTriple($new_reference, $code_color, $code_size);

            $array_combinations = $mapping->createSingleArrayCombination($new_reference, $code_color, $code_size, 
                                                                         $name_color, $name_size, $name_photo, $quantity); 

            $element = array();
            array_push($element,$array_mapping);
            array_push($element,$array_triple);
            array_push($element,$array_combinations);
            array_push($element,$ps_id);

            array_push($to_return,$element);

            closeConnectionServer($connection);
            return $to_return;    
        } else {
            closeConnectionServer($connection);
            return 0;
        }
    }

    /**
	* Get all products contain in the view products_differences.
	*
	* @return array of elements if this view contains their, else return false.
	* @see MappingArray createSingleArrayTriple()
	* @see MappingArray createSingleArrayMapping()
	* @see MappingArray createSingleArrayCombination()
	*
	*/
    public function getSqlChangedProducts($array_product,$key){
        $connection = connectionServer();
        
        $sql = "SELECT ps_tmp_product.ps_id,ps_buffer_product.reference, ps_buffer_product.attivo, ps_buffer_product.categoria, 
ps_buffer_product.prezzo, ps_buffer_product.supplier, ps_buffer_product.manufacture, ps_buffer_product.qta, 
ps_buffer_product.qta_min, ps_buffer_product.lunghezza, ps_buffer_product.altezza, ps_buffer_product.larghezza, ps_buffer_product.colore,
ps_buffer_product.quantita,ps_buffer_product.taglia, ps_buffer_product.nome, ps_buffer_product.modello,ps_buffer_product.linea, 
ps_buffer_product.codice_colore, ps_buffer_product.url, ps_buffer_product.immagine, ps_buffer_product.codice_taglia FROM ps_buffer_product, ps_tmp_product WHERE ((ps_buffer_product.reference = ps_tmp_product.reference
AND ps_buffer_product.codice_colore = ps_tmp_product.codice_colore AND ps_buffer_product.codice_taglia = ps_tmp_product.codice_taglia )AND( ps_buffer_product.attivo <> ps_tmp_product.attivo 
OR ps_buffer_product.prezzo <> ps_tmp_product.prezzo OR ps_buffer_product.qta <> ps_tmp_product.qta OR ps_buffer_product.qta_min <> ps_tmp_product.qta_min 
OR ps_buffer_product.lunghezza <> ps_tmp_product.lunghezza OR ps_buffer_product.altezza <> ps_tmp_product.altezza
OR ps_buffer_product.larghezza <> ps_tmp_product.larghezza OR ps_buffer_product.quantita <> ps_tmp_product.quantita
OR ps_buffer_product.nome <> ps_tmp_product.nome OR ps_buffer_product.modello <> ps_tmp_product.modello OR ps_buffer_product.immagine <> ps_tmp_product.immagine 
OR ps_buffer_product.linea <> ps_tmp_product.linea )AND ( ps_buffer_product.reference ='".$key."') )ORDER BY ps_buffer_product.reference ASC";
        $to_return = array();
        $result = mysql_query($sql,$connection);
        if($result){
            if (mysql_num_rows($result) > 0){
                $to_return = array();
                while($row = mysql_fetch_array( $result )){        
                    $sql = "UPDATE  ps_tmp_product SET attivo = '".$row[2]."', prezzo = '".$row[4]."', qta = '".$row[7]."', qta_min = '".$row[8]."', lunghezza = '".$row[9]."', altezza = '".$row[10]."',larghezza = '".$row[11]."', nome = '".$row[15]."', modello = '".$row[16]."' , linea = '".$row[17]."' WHERE ps_id = '".$array_product."' AND codice_colore = '".$row[18]."' AND codice_taglia = '".$row[21]."'";
                    array_push($to_return,$sql); 
                }
                closeConnectionServer($connection);
                return $to_return ;
            }
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
        closeConnectionServer($connection);
    }

    public function _getSqlChangedProducts($array_product,$key){
        $connection = connectionServer();
        $sql = "SELECT ps_tmp_product.ps_id,ps_buffer_product.reference, ps_buffer_product.attivo, ps_buffer_product.categoria, 
ps_buffer_product.prezzo, ps_buffer_product.supplier, ps_buffer_product.manufacture, ps_buffer_product.qta, 
ps_buffer_product.qta_min, ps_buffer_product.lunghezza, ps_buffer_product.altezza, ps_buffer_product.larghezza, ps_buffer_product.colore,
ps_buffer_product.quantita,ps_buffer_product.taglia, ps_buffer_product.nome, ps_buffer_product.modello,ps_buffer_product.linea, 
ps_buffer_product.codice_colore, ps_buffer_product.url, ps_buffer_product.immagine, ps_buffer_product.codice_taglia FROM ps_buffer_product, ps_tmp_product WHERE ((ps_buffer_product.reference = ps_tmp_product.reference
AND ps_buffer_product.codice_colore = ps_tmp_product.codice_colore AND ps_buffer_product.codice_taglia = ps_tmp_product.codice_taglia )AND( ps_buffer_product.attivo <> ps_tmp_product.attivo 
OR ps_buffer_product.prezzo <> ps_tmp_product.prezzo OR ps_buffer_product.qta <> ps_tmp_product.qta OR ps_buffer_product.qta_min <> ps_tmp_product.qta_min 
OR ps_buffer_product.lunghezza <> ps_tmp_product.lunghezza OR ps_buffer_product.altezza <> ps_tmp_product.altezza
OR ps_buffer_product.larghezza <> ps_tmp_product.larghezza OR ps_buffer_product.quantita <> ps_tmp_product.quantita
OR ps_buffer_product.nome <> ps_tmp_product.nome OR ps_buffer_product.modello <> ps_tmp_product.modello OR ps_buffer_product.immagine <> ps_tmp_product.immagine 
OR ps_buffer_product.linea <> ps_tmp_product.linea )AND ( ps_buffer_product.reference ='".$key."') )ORDER BY ps_buffer_product.reference ASC";
        $to_return = array();
        $result = mysql_query($sql,$connection);
        if($result){
            if (mysql_num_rows($result) > 0){
                $to_return = array();
                while($row = mysql_fetch_array( $result )){        
                    $sql = "INSERT INTO ps_tmp_product (ps_id, reference, attivo, categoria, prezzo, supplier, 
                    manufacture, qta, qta_min, lunghezza, altezza, larghezza, colore, quantita, taglia, 
						nome, modello, linea, codice_colore, url, immagine, codice_taglia )
						VALUES('".$array_product."','".$key."','".$row[2]."',
							   '".$row[3]."','".$row[4]."','".$row[5]."','".$row[6]."',
							   '".$row[7]."','".$row[8]."','".$row[9]."','".$row[10]."',
							   '".$row[11]."','".$row[12]."','".$row[13]."','".$row[14]."',
							   '".$row[15]."','".$row[16]."','".$row[17]."','".$row[18]."',
							   '".$row[19]."','".$row[20]."','".$row[21]."')";

                    array_push($to_return,$sql); 
                }
                closeConnectionServer($connection);
                return $to_return ;
            }
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
        closeConnectionServer($connection);
    }
}

?>