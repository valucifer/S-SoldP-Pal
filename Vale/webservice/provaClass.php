<?php
	//Creato da: Valentino Vivone
	//il giorno: 02/11/15
	//Modificato da: Valentino Vivone
	//il giorno: 09/11/2015
	
	require_once "./class.Compare.php";
	
	$mapping = new Mapping("File/A20151016144918_SEM.chk");
	
	echo "<br> Prova: <br> Proviamo a dare un file in input (tramite path)<br><br>";
	echo "	1)Il risultato della prima tabella:<br><br>";
	print_r($mapping->getItemMaster());
	
	echo "<br><br>";
	echo "	2)Il risultato della seconda tabella:<br><br>";
	print_r($mapping->getCombinations());
	
?>