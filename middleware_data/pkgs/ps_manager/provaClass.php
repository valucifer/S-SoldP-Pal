<?php
	//Creato da: Valentino Vivone
	//il giorno: 02/11/15
	//Modificato da: Valentino Vivone
	//il giorno: 09/11/2015
	
	require_once "./Mapping.php";
/*
	$mapping = new Mapping("File/A20151102142434_SEM.chk");
	
	echo "<br> Prova: <br> Proviamo a dare un file in input (tramite path)<br><br>";
	echo "	1)Il risultato della prima tabella:<br><br>";
	print_r($mapping->getItemMaster());
	
	echo "<br><br>";
	echo "	2)Il risultato della seconda tabella:<br><br>";
	print_r($mapping->getCombinations());
*/
	include "../config/config.inc.php";
	include "../init.php";
	$cat = new Category("98");
	print_r($cat->getProductsWs());
	echo "<br><br>";
	$arr = $cat->getProductsWs();
	$size = sizeof($arr);
	echo "<br>";
	for($i = 0; $i < $size; $i++){
		$arr2 = $arr[$i];
		$num = (int)$arr2["id"];
		$prod = new Product($num);
		echo "active: $prod->active<br>";
	}
	
	
	
	
?>