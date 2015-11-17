<?php
require ("PrestashopUpdate.php");
echo "inizio aggiornamento </br>";
$updatePS = new PrestashopUpdate('../Files/A20151008161213_SEM.chk');
$updatePS->updatePsProduct();
echo "aggiornamento completato </br>"; 
?>