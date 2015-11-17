<?php
	//Creato da: Valentino Vivone
	//il giorno: 02/11/15
	//Modificato da: Valentino Vivone
	//il giorno: 09/11/2015
	
	require_once "./Mapping.php";

	include "../config/config.inc.php";
	include "../init.php";
	try{
	$prod = new Product(662);
	print_r($prod->getAttributeCombinations(1));
	echo "<br><br>";
	$arr = $prod->getAttributeCombinations(1);
	$arr = $arr[0];
	$comb = new CombinationCore($arr["id_product_attribute"]);
	echo "cia";
	echo "COMB: ".$comb->id;
;	}catch(Exception $e){echo $e->getMessage();}
	
?>