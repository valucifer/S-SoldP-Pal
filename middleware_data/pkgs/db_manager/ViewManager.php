<?php

require_once ("Mapping.php");
require_once ("connection.php");
require_once ("HandleOperationsException.php");
require_once ("Logger.php");

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
		
		$sql = "SELECT * FROM new_products WHERE 1 ORDER BY reference ASC";
		
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
		
		$sql = "SELECT * FROM products_differences WHERE 1 ORDER BY reference ASC";
		
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
	* Get all products contain in the view products_differences.
	*
	* @return array of elements if this view contains their, else return false.
	* @see MappingArray createSingleArrayTriple()
	* @see MappingArray createSingleArrayMapping()
	* @see MappingArray createSingleArrayCombination()
	*
	*/
    public function getSqlNewProducts($array_product,$key){
        $connection = connectionServer();
        $sql = "SELECT * FROM  new_products WHERE reference ='".$key."' ORDER BY reference ASC";
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
        $sql = "SELECT * FROM  products_differences WHERE reference ='".$key."' ORDER BY reference ASC";
		$to_return = array();
        $result = mysql_query($sql,$connection);
        if($result){
		  if (mysql_num_rows($result) > 0){
            $to_return = array();
            while($row = mysql_fetch_array( $result )){        
                $sql = "UPDATE  ps_tmp_product SET attivo = '".$row[2]."', prezzo = '".$row[3]."', qta = '".$row[6]."', qta_min = '".$row[7]."', lunghezza = '".$row[8]."', altezza = '".$row[9]."',larghezza = '".$row[10]."', nome = '".$row[15]."', modello = '".$row[16]."' , linea = '".$row[17]."' WHERE ps_id = '".$array_product."' AND colore = '".$row[12]."' AND taglia = '".$row[14]."'";
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