<?php
	//Creato da: Valentino Vivone
	//il giorno: 02/11/15
	//Modificato da: Valentino Vivone
	//il giorno: 09/11/2015
	
	include "../config/config.inc.php";
	include "../init.php";
	require_once "./_PrestashopProducts.php";
	require_once "./Mapping.php";

	$mapping = new Mapping("File/A20151008161213_SEM.chk");
	print_r($mapping->getItemMaster());echo"<br><br><br>";
	print_r($mapping->getCombinations());echo"<br><br><br>";
	print_r($mapping->triple());echo "<br><br><br>";
	echo "<br>------------------------------------------------------<br>";
	echo "<br>------------------------------------------------------<br>";
	
	$prova = new productForPrestashop();
	
	$array_prova = array ( 'Prezzo' => '160.00', 
						   'Attivo' => 1,
						   'Reference' => 'KPBS03S05',
						   'Nome' => 'BORSA SINTETICA DONNA',
						   'Categorie' => 'BORSE,KRIZIA POI DOPO',
						   'Supplier' => 'KRIZIA POI DOPO',
						   'Manufacture' => 'KRIZIA POI DOPO',
						   'Qta' => 8,
						   'Qta_min' => '1', 
						   'Feature' => array ( 'Larghezza' => '14.000', 
												'Altezza' => '35.000', 
												'Lunghezza' => '363636.000',
												'Modello' => 'Sacca',
												'Linea' => 'zxy',
											),
							'URL' => 'KPBS03S05,2,001.jpg,KPBS03S05,3,005.jpg, '
					);
	$array_comb = array ( 'Product_reference' => 'YASC98M01X', 
						  'YASC98M01X;EEE;42' => array ( 'Attributi' => 'COLORE,TAGLIA', 
														 'Valori' => 'ROSSO,42', 
														 'Qta' => '20', 
														 'Immagine' => 'cazzo.jpg'
												)
						);
	
	$array_triple = array ( 'YASC98M01X;EEE;42' => 'YASC98M01X;EEE;42'
					);

	$urlFoto = "File/A20151008161213_FOTO/";
	
	
	try{
	echo "Array: ".print_r($array_prova)."<br><br>";
	echo "Array: ".print_r($array_comb)."<br><br>";
	echo "Array: ".print_r($array_triple)."<br><br>";
	echo "Array: ".$urlFoto."<br><br>";
	echo "----------------------------------------------<br>";
		$prova->updateProductForPrestashop($array_prova, 12, $urlFoto, $array_triple, $array_comb);
	}catch(Exception $e){
		echo "exception: ".$e->getMessage();
	}
	
	/*$product = new Product(11);
	print_r($product->getFeatures());echo "<br><br>";
	$language=1;
	$array_old_features = $product->getFeatures();
	foreach($array_old_features as $array_old_single_features){
		$feature = new FeatureCore((int)$array_old_single_features['id_feature']);
		$tmp_feature = $feature->name;
		$single_feature = $tmp_feature[$language];
		
		$feature_value = new FeatureValueCore((int)$array_old_single_features["id_feature_value"]);
		$tmp_feature_value = $feature_value->value;
		$single_feature_value = $tmp_feature_value[$language];
		echo "le Feature: ".$single_feature.": ".$single_feature_value."<br>";
	}
	*/
	
	/*$prod = new Product(12);
	$arr = array(12);//id prodotto
	print_r($prod->getAttributeCombinations(1));*/
	/*print_r($prod->getAttributesColorList($arr)); echo "<br><br>";
	$arr_c_t = $prod->getAttributesGroups(1);//language
	
	foreach($arr_c_t as $col_and_size){
		echo 'colore: '.$col_and_size['attribute_name'].'<br>'; print_r($col_and_size);echo "<br><br>";
		
	}*/
?>















