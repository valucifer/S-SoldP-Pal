<?php
	//Creato da: Valentino Vivone
	//il giorno: 02/11/15
	//Modificato da: Valentino Vivone
	//il giorno: 09/11/2015
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs');
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs/color_comparison');
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/pkgs/db_manager');
	set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/ps_manager');
	set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/image_checker');
	set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/pkgs/ftp_connection');
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/libs');
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/libs/color_lib');
	set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/files');

	include "../../config/config.inc.php";
	include "../../init.php";
	require_once ("PrestashopProducts.php");
	require_once ("Mapping.php");

	$mapping = new Mapping("./files/A20151008161213_SEM.chk");
	print_r($mapping->getItemMaster());
	echo "<br><br>";
	print_r($mapping->getCombinations());
	echo "<br><br>";
	print_r($mapping->triple()); 
	echo "<br><br><br>************************************************************************<br><br><br>";
	echo "<br><br><br>************************************************************************<br><br><br>";
	echo "<br><br><br>************************************************************************<br><br><br>";
	$arr1 = $mapping->createSingleArrayMapping('79.00', '1', 'KPBS03S08', 'BORSA SINTETICA DONNA', 'BORSA,KRIZIA POI DOPO',
	                                          'KRIZIA POI DOPO', 'KRIZIA POI DOPO', '0', '1', '12.000', '35.000',
											  '36.280', 'Secchiello', 'WHY', 'KPBS03S08,3,001.jpg,KPBS03S08,2,005.jpg,');
	
	print_r($arr1);
	echo "<br><br><br>************************************************************************<br><br><br>";
	echo "<br><br><br>************************************************************************<br><br><br>";
	echo "<br><br><br>************************************************************************<br><br><br>";
	
	$ccol = array('001','005');
	$csiz = array('','');
	$arr2 = $mapping->createSingleArrayTriple('KPBS03S08', $ccol, $csiz);
	
	print_r($arr2);
	echo "<br><br><br>************************************************************************<br><br><br>";
	echo "<br><br><br>************************************************************************<br><br><br>";
	echo "<br><br><br>************************************************************************<br><br><br>";
	$ncol = array('NERO','BEIGE');
	$nsiz = array('','');
	$ccol = array('001','005');
	$csiz = array('','');
	$qua = array('85','58');
	$imm = array('KPBS03S08,3,001.jpg','KPBS03S08,2,005.jpg,');
	$arr3 = $mapping->createSingleArrayCombination('KPBS03S08', $ccol, $csiz, $ncol, $nsiz, $imm, $qua);
	
	print_r($arr3);
	echo "<br><br><br>************************************************************************<br><br><br>";
	echo "<br><br><br>************************************************************************<br><br><br>";
	echo "<br><br><br>************************************************************************<br><br><br>";
	
	//$image = new ImageForPrestashop();
	//echo $image->updateImageInPrestashop(8, 37, "./files/A20151008161213_FOTO/", 'KPBS04M01,88,E95.jpg');
	
	
	/*
	$prova = new productForPrestashop();
	
	$array_prova = array ( 'Prezzo' => '10.00', 
						   'Attivo' => 1,
						   'Reference' => 'KPBS03S05',
						   'Nome' => 'BORSA SINTETICA DONNA MIA',
						   'Categorie' => 'BORSE,KRIZIA POI DOPO',
						   'Supplier' => 'KRIZIA POI DOPO',
						   'Manufacture' => 'KRIZIA POI DOPO',
						   'Qta' => 10,
						   'Qta_min' => 10, 
						   'Feature' => array ( 'Larghezza' => '114.000', 
												'Altezza' => '355.000', 
												'Lunghezza' => '36.000',
												'Modello' => 'SaccaROSIO',
												'Linea' => 'PERCHE',
											),
							'URL' => 'KPBS03S05,2,001.jpg,KPBS03S05,5,005.jpg, '
					);
					
	$array_comb = array ( 'Product_reference' => 'KPBS03S05', 
						  'KPBS03S05;001;' => array ( 'Attributi' => 'COLORE,TAGLIA', 
													  'Valori' => 'ROSSO,40', 
													  'Qta' => '100', 
													  'Immagine' => 'KPBS04M01,88,E95.jpg'
												)
						);
	
	$array_triple = array ( 'KPBS03S05;001;' => 'KPBS03S05;001;'
					);

*/
	$urlFoto = "./files/A20151008161213_FOTO/";
	
	
	/*
	try{
		$arr = $prova->updateProductForPrestashop($arr1, 8, $urlFoto, $array_triple, $array_comb);
		echo "<br>444<br>";
		print_r($arr);
		echo "<br>444<br>";
	}catch(Exception $e){
		echo $e->getMessage();
	}
	*/
	
	/*
	$array_keys = $mapping->keys();
	$array_mapping = $mapping->getItemMaster();
	$array_combinations = $mapping->getCombinations();
	$array_triple = $mapping->triple();
*/
    $prova = new PrestashopProduct();
    //$urlFoto = "./files/A20151016144918_FOTO/"; 
	
	//foreach($array_keys as $key){
		try{
			$arr = $prova->insertProductForPrestashop($arr1, $urlFoto, $arr2, $arr3);
			echo "<br> 444 <br>";
			print_r($arr);
			echo "<br> 444 <br>";
			
		}catch(Exception $e){
			echo "exception: ".$e->getMessage();
		}
	//}
?>


