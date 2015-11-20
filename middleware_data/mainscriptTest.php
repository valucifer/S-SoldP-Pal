<?php

echo "This is path before running set:".get_include_path()."<br/>";

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs/color_comparison');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs/db_manager');
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/ps_manager');
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/image_checker');
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/ftp_connection');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/libs');
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/libs/color_lib');
set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/files');


require_once("Mapping.php");
require_once("PrestashopProducts.php");

$mapping = new Mapping("./files/A20151016144918_SEM.chk");
print_r($mapping->getItemMaster());

/*
$manager = new PrestashopUpdate("./files/A20151008161213_SEM.chk");
$manager->updatePsProduct();
	
	$mapping = new Mapping("./files/A20151008161213_SEM.chk");
	$prova = new PrestashopProduct();
	$keys = $mapping->keys();
	$triple = $mapping->triple();
	$mapp = $mapping->getItemMaster();
	$comb = $mapping->getCombinations();
	$url_photo = './files/A20151008161213_FOTO/';
	
	foreach ( $keys as $key ){
		$result = $prova->insertProductForPrestashop($mapp[$key],$url_photo,$triple[$key],$comb[$key]);
		echo "<br> ***************************** prova inserimenti ***************************** <br>";
		print_r($result);
		echo "<br> ***************************** prova inserimenti ***************************** <br>";
	}
	
	$mapp = array ( 'Prezzo' => 69.00,
					'Attivo' => 1,
					'Reference' => 'KPBS03S05',
					'Nome' => 'BORSA SINTETICA DONNA',
					'Categorie' => 'BORSE,KRIZIA POI DOPO', 
					'Supplier' => 'KRIZIA POI DOPO', 
					'Manufacture' => 'KRIZIA POI DOPO',
					'Qta' => 8,
					'Qta_min' => 1,
					'Feature' => array ( 'Larghezza' => 14.000,
										 'Altezza' => 35.000,
										 'Lunghezza' => 36.000,
										 'Modello' => 'Sacca', 
										 'Linea' => 'WHY PERCHE'
								),
					'URL' => 'KPBS03S05,2,001.jpg,KPBS03S05,3,005.jpg,qwe.jpg,'
			);
	
	$triple = array ( 'KPBS03S05;005;36' => 'KPBS03S05;005;36'
			  );
			  
	$comb = array ( 'Product_reference' => 'KPBS03S05',
					'KPBS03S05;005;36' => array ( 'Attributi' => 'COLORE,TAGLIA',
												  'Valori' => 'BLUE,36',
												  'Qta' => 100,
												  'Immagine' => '1.jpg,2.jpg,',
												  'Codici' => '005,36' 
										)
			);
	
	$result = $prova->updateProductForPrestashop($mapp, 8, $url_photo, $triple, $comb);
	echo "<br> ***************************** prova inserimenti ***************************** <br>";
	print_r($result);
	echo "<br> ***************************** prova inserimenti ***************************** <br>";
*/
	
	
	
	
	
	
?>