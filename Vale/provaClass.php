<?php

	require_once "./class.Compare.php";
	
	$mapping = new Mapping("File/A20151008161213_SEM.chk");
	
	echo "<br> Prova: <br> Proviamo a dare un file in input (tramite path)<br><br>";
	echo "	1)Il risultato della prima tabella:<br><br>";
	print_r($mapping->getItemMaster());
	
	echo "<br><br>";
	echo "	2)Il risultato della seconda tabella:<br><br>";
	print_r($mapping->getCombinations());
	
?>