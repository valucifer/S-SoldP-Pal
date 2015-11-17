<?php
require ("Mapping.php");
echo "inizio aggiornamento </br>";
$mapping = new Mapping('../Files/A20151008161213_SEM.chk');
print_r($mapping->getItemMaster());
?>