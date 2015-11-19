<?php

/*
*
* @author Valentino Vivone <v.vivone.5389@gmail.com>
* @version 1.0
*
*/

	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs');
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs/color_comparison');
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs/db_manager');
	set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/ps_manager');
	set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/image_checker');
	set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/ftp_connection');
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/libs');
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/libs/color_lib');
	set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/files');

	include "../../../../config/config.inc.php";
	include "../../../../init.php";
	require_once ("PrestashopProducts.php");
	require_once ("Mapping.php");

	require_once ("connection.php");
	require_once ("HandleOperationsException.php");
	require_once ("Logger.php");

class ViewManager{
    private $logger=null;
        
    public function __construct(){
        $this->logger = new Logger();
    }
    
    public function getNewProduct($reference){
		$connection = connectionServer();
		
		$mapping = new Mapping();
		$active = "";	$category = "";	 $price = "";	$supplier = ""; $name = "";	$collection = "";
		$manufacture = "";	$qta = "";	$qta_min = "";	$size = "";	$height = "";	$width = "";
		$name_color = array();	$quantity = array();	$name_size = array();	$model = "";
		$code_color = array();	$url = "";	$name_photo = array();	$code_size = array();
		
		$sql = "SELECT * FROM  new_product WHERE ( reference = '".$reference."')";
		
		$result = mysql_query($sql,$connection);
		if (mysql_num_rows($result) > 0){
			$to_return = array();
			while($row = mysql_fetch_array( $result )){
				$active = $row[2];	$category = $row[3];	$price = $row[4];
				$supplier = $row[5];	$manufacture = $row[6];	$qta = $row[7];
				$qta_min = $row[8]; $size = $row[9];	$height = $row[10];
				$width = $row[11];	array_push($name_color, $row[12]);	array_push($quantity, $row[13]);
				array_push($name_size, $row[14]);	$name = $row[15];	$model = $row[16];
				$collection = $row[17];	array_push($code_color, $row[18]);	$url = $row[19];
				array_push($name_photo, $row[20]);	array_push($code_size, $row[21]);
			}
			
			$array_mapping = $mapping->createSingleArrayMapping($price, $active, $reference, $name, $category, $supplier, $manufacture, $qta, $qta_min,
												$width, $height, $size, $model, $collection, $url);
			
			$array_triple = $mapping->createSingleArrayTriple($reference, $code_color, $code_size);
			
			$array_combinations = $mapping->createSingleArrayCombination($reference, $code_color, $code_size, 
																			$name_color, $name_size, $name_photo, $quantity);
			
			array_push($to_return, $array_mapping);
			array_push($to_return, $array_triple);
			array_push($to_return, $array_combinations);
							
			closeConnectionServer($connection);
			return $to_return;    
		} else {
			closeConnectionServer($connection);
			return FALSE;
		}
    }
    
    public function getNewProduct($reference){
		$connection = connectionServer();
		
		$mapping = new Mapping();
		$ps_id = ""; $active = "";	$category = "";	 $price = "";	$supplier = ""; $name = "";	$collection = "";
		$manufacture = "";	$qta = "";	$qta_min = "";	$size = "";	$height = "";	$width = "";
		$name_color = array();	$quantity = array();	$name_size = array();	$model = "";
		$code_color = array();	$url = "";	$name_photo = array();	$code_size = array();
		
		$sql = "SELECT * FROM  products_differences WHERE ( reference = '".$reference."')";
		
		$result = mysql_query($sql,$connection);
		if (mysql_num_rows($result) > 0){
			$to_return = array();
			while($row = mysql_fetch_array( $result )){
				$ps_id = $row[0];	$active = $row[2];	$category = $row[3];	$price = $row[4];
				$supplier = $row[5];	$manufacture = $row[6];	$qta = $row[7];
				$qta_min = $row[8]; $size = $row[9];	$height = $row[10];
				$width = $row[11];	array_push($name_color, $row[12]);	array_push($quantity, $row[13]);
				array_push($name_size, $row[14]);	$name = $row[15];	$model = $row[16];
				$collection = $row[17];	array_push($code_color, $row[18]);	$url = $row[19];
				array_push($name_photo, $row[20]);	array_push($code_size, $row[21]);
			}
			
			$array_mapping = $mapping->createSingleArrayMapping($price, $active, $reference, $name, $category, $supplier, $manufacture, $qta, $qta_min,
												$width, $height, $size, $model, $collection, $url);
			
			$array_triple = $mapping->createSingleArrayTriple($reference, $code_color, $code_size);
			
			$array_combinations = $mapping->createSingleArrayCombination($reference, $code_color, $code_size, 
																			$name_color, $name_size, $name_photo, $quantity);
			
			array_push($to_return, $array_mapping);
			array_push($to_return, $array_triple);
			array_push($to_return, $array_combinations);
			ararray_push($to_return, $ps_id);
							
			closeConnectionServer($connection);
			return $to_return;    
		} else {
			closeConnectionServer($connection);
			return FALSE;
		}
    }
}

?>