<?php
	//Creato da: Valentino Vivone
	//il giorno: 02/11/15
	//Modificato da: Valentino Vivone
	//il giorno: 09/11/2015
	
	require_once "./Mapping.php";

	include "../config/config.inc.php";
	include "../init.php";
	
	/*$mapping = new Mapping("File/A20151008161213_SEM.chk");
	print_r($mapping->getItemMaster());*/
	$attribute = new Attribute();
	print_r($attribute->isAttribute(6,"GRIGO",1));
	
?>