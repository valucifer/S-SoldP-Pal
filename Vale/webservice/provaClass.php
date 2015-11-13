<?php
	//Creato da: Valentino Vivone
	//il giorno: 02/11/15
	//Modificato da: Valentino Vivone
	//il giorno: 09/11/2015
	
	require_once "./Mapping.php";
	
	$mapping = new Mapping("File/A20151016144918_SEM.chk");
	
	echo "<br> Prova: <br> Proviamo a dare un file in input (tramite path)<br><br>";
	echo "	1)Il risultato della prima tabella:<br><br>";
	print_r($mapping->getItemMaster());
	
	echo "<br><br>";
	echo "	2)Il risultato della seconda tabella:<br><br>";
	print_r($mapping->getCombinations());
/*
	include "../config/config.inc.php";
	include "../init.php";
	$cat = new Category();
	$arrayCategories = $cat::searchByNameAndParentCategoryId(1,"YAMAMAY",2);
	echo "prima<br>";
	print_r($arrayCategories);
	echo "<br>dopo<br>";
	try{
		$arr = $cat->getProducts(1,1,10);
		print_r($arr);
		echo "<br> dopo";
	}catch(Exception $e){
		echo $e->getMessage();
	}
	
	$supplier = new Supplier();
	$id = $supplier::getIdByName("mario");
	if( $id != 0){
		echo "esiste";
	}else{
		$supplier->name = "mario";
		$supplier->add();
		echo "creato";
	}
*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>