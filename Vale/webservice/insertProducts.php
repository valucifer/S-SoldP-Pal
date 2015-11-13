<?php
	include "../config/config.inc.php";
	include "../init.php";
	require_once "./Mapping.php";
	
	class imageForPrestashop{
		
		function imageForPrestashop(){}
		
		public function insertImageInPrestashop($id_product, $url){
			$shops = Shop::getShops(true, null, true);    
			$image = new Image();
			$image->id_product = $id_product;
			$image->position = Image::getHighestPosition($id_product) + 1;
			$image->cover = true; // or false;
    
			if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->add()){
                $image->associateTo($shops);
                if (!$this->copyImg($id_product, $image->id, trim($url), 'products')){
					$image->delete();
				}
            }
			return $image->id;
		}
		
		public function updateImageInPrestashop($id_product, $id_img, $url){
			if($id_img == "") $this->insertImageInPrestashop($id_prod,$url);
			else{
				$shops = Shop::getShops(true, null, true);    
				$image = new Image();
				$image->id_product = $id_product;
		
				if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->update()){
					$image->associateTo($shops);
					if (!$this->copyImg($id_product, $id_img, trim($url), 'products')){
						$image->delete();
					}
				}
			}
		}
		
		private function copyImg($id_entity, $id_image = null, $url, $entity = 'products'){
			$tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
			$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

			switch ($entity){
				default:
				case 'products':
					$image_obj = new Image($id_image);
					$path = $image_obj->getPathForCreation();
					break;
				case 'categories':
					$path = _PS_CAT_IMG_DIR_.(int)$id_entity;
					break;
			}
    
			$url = str_replace(' ' , '%20', trim($url));
			
			// Evaluate the memory required to resize the image: if it's too much, you can't resize it.
    
			if (!ImageManager::checkImageMemoryLimit($url))
				return false;
			
			// 'file_exists' doesn't work on distant file, and getimagesize make the import slower.
			// Just hide the warning, the traitment will be the same.
			if (@copy($url, $tmpfile)){
				ImageManager::resize($tmpfile, $path.'.jpg');
				$images_types = ImageType::getImagesTypes($entity);
				foreach ($images_types as $image_type)
					ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'],$image_type['height']);

				if (in_array($image_type['id_image_type'], $watermark_types))
					Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
			}else{
				unlink($tmpfile);
				return false;
			}
			unlink($tmpfile);
			return true;
		}
		
	}
	
	class productForPrestashop{
		
		function productForPrestashop(){}
		
		private function setArrayElementForLinkRewrite($name_element, $is_name, $language = 1){
			if($is_name){
				$name_product = array($language=>trim($name_element));
				return $name_product;
			}else{
				$array_replace = array("."," ",",","&","+","*","/","\\",":",";","_","=","!","?","'","\"","$","€","<",">");
				$link_rewrite_product = array($language=>strtolower(str_replace($array_replace,"-",trim($name_element))));
				return $link_rewrite_product;
			}
		}
		
		private function setCategoriesForProduct($name_category, $id_parent_category, $language){
			$id_category = 2;
			$category = new Category();
			$array_categories = $category::searchByNameAndParentCategoryId($language, trim($name_category), $id_parent_category);
			
			if(empty($array_categories)){
				$category_for_product = new Category();
				$category_for_product->active = 0;
				$category_for_product->id_parent = (int) trim($id_parent_category);
				$category_for_product->name = $this->setArrayElementForLinkRewrite(trim($name_category), true, $language);
				$category_for_product->link_rewrite = $this->setArrayElementForLinkRewrite(trim($name_category), false, $language);
				$category_for_product->meta_keywords = trim($name_category);
				$category_for_product->add();
				$id_category = $category_for_product->id;
			}else{
				$id_category = (int) $array_categories["id_category"];
			}
			
			return $id_category;			
		}
		
		private function setSupplierForProduct($name_supplier){
			$id_supplier = 0;
			$supplier = new SupplierCore();
			$id_supplier = $supplier::getIdByName(trim($name_supplier));
			
			if(! $id_supplier ){
				$supplier->name = trim($name_supplier);
				$supplier->add();
				$id_supplier = $supplier->id;
			}
			
			return $id_supplier;
		}
		
		private function setManufacturerForProduct($name_manufacturer){
			$id_manufacturer = 0;
			$manufacturer = new ManufacturerCore();
			$id_manufacturer = $manufacturer::getIdByName(trim($name_manufacturer));
			
			if(! $id_manufacturer ){
				$manufacturer->name = trim($name_manufacturer);
				$manufacturer->add();
				$id_manufacturer = $manufacturer->id;
			}
			
			return $id_manufacturer;
		}
		
		public function insertProductForPrestashop($product_attributes = array(), $url_foto, $language = 1){
			$product = new Product();
			//$language is 1 because 1 -> italian
			
			$string_name_product = trim($product_attributes["Nome"]);
			$product->name = $this->setArrayElementForLinkRewrite($string_name_product, true, $language);
			$product->meta_keywords = $string_name_product;
			$product->link_rewrite = $this->setArrayElementForLinkRewrite($string_name_product, false, $language);
			
			$product->id_category_default = 2; //Home
			$product->redirect_type = '404';
			$product->price = (float) $product_attributes["Prezzo"];
			$product->active = $product_attributes["Attivo"];
			$product->minimal_quantity = (int)$product_attributes["Qta_min"];
			$product->show_price = 1;
			$product->on_sale = 0;
			$product->reference = $product_attributes["Reference"];
			$product->online_only = 1;
			$product->id_tax_rules_group = 0;
			
			$product->id_supplier = $this->setSupplierForProduct(trim($product_attributes["Supplier"]));
			$product->id_manufacturer = $this->setManufacturerForProduct(trim($product_attributes["Manufacture"]));
			
			$array_features = $product_attributes["Feature"];
			$product->width = (float)$array_features["Larghezza"];
			$product->height = (float)$array_features["Altezza"];
			$product->depth = (float)$array_features["Lunghezza"];
			
			$product->add();
			
			$categories = trim($product_attributes["Categorie"]);
			$tmp = explode(",",$categories);
			
			$griffe = $tmp[1]; $modello = $tmp[0];
			$id_parent = null; $id_child = null;
			
			$id_parent = $this->setCategoriesForProduct(trim($griffe), 2, $language);
			$id_child = $this->setCategoriesForProduct(trim($modello), $id_parent, $language);
			
			$cat_pos_tmp = array(); 
			array_push($cat_pos_tmp,$id_child); 
			array_push($cat_pos_tmp,$id_parent);
			$product->addToCategories($cat_pos_tmp);
			
			if($product->active != 0){
				$activeCategory = new Category($id_parent);
				$activeCategory->active = 1;
				$activeCategory->update();
				
				$activeCategory = new Category($id_child);
				$activeCategory->active = 1;
				$activeCategory->update();
			}
				
			$tmp =  explode(".jpg,", trim($product_attributes["URL"]));
			$size_url = sizeof($tmp);
			$array_id_image = array();
			
			for($i = 0; $i < $size_url; $i++){
				if(!empty($tmp[$i])){
					$url = trim($url_foto).$tmp[$i].".jpg";
					$image = new imageForPrestashop();
					$id_image = $image->insertImageInPrestashop($product->id,$url);
					array_push($array_id_image,$id_image);
				}
			}
			
			$product->addProductAttribute($product->price, 0.000, 0.000, 0.000, (int)$product_attributes["Qta"], $array_id_image, $product->reference, 
			$product->id_supplier, 0, '');
		}
		
		private function controlCategoriesForActivateTheir($idCategory){
			
		}
		
		public function updateProductForPrestashop($product_attributes = array(), $id_product, $language = 1){
			$product = new Product($id_product);
			
			$string_name_product = trim($product_attributes["Nome"]);
			$product->name = $this->setArrayElementForLinkRewrite($string_name_product, true, $language);
			$product->meta_keywords = $string_name_product;
			$product->link_rewrite = $this->setArrayElementForLinkRewrite($string_name_product, false, $language);
			
			$product->id_category_default = 2; //Home
			$product->redirect_type = '404';
			$product->price = (float)$product_attributes["Prezzo"];
			$product->active = $product_attributes["Attivo"];
			
			$product->minimal_quantity = (int)$product_attributes["Qta_min"];
			$product->show_price = 1;
			$product->on_sale = 0;
			$product->online_only = 1;
			$product->id_tax_rules_group = 0;
			
			$arrayFeatures = $product_attributes["Feature"];
			$product->width = (float)$arrayFeatures["Larghezza"];
			$product->height = (float)$arrayFeatures["Altezza"];
			$product->depth = (float)$arrayFeatures["Lunghezza"];
			
			$product->update();
			StockAvailable::setQuantity($product->id,'',(int)$product_attributes["Qta"]);
			
		}
	}
	
	ini_set('max_execution_time', 600);
	$prova = new productForPrestashop();
	
	try{
		$path = "File/A20151008161213_SEM.chk";
		$mapping = new Mapping($path);
		
		$tmp = explode("_",$path);
		$urlFoto = $tmp[0]."_FOTO/";
		
		$keys = $mapping->keys();
		
		$arrayMapping = $mapping->getItemMaster();
		
		//$arrayCombinations = $mapping->getCombinations();
		foreach($keys as $key){
			try{
				$prova->insertProductForPrestashop($arrayMapping[$key],$urlFoto);
			}catch(Exception $e){
				echo "$key<br/>";
				echo $e->getMessage()." in line". $e->getLine()."<br/>";
				echo "<br/><br/><br/><br/>";
			}
			/*$prova->updateProductForPrestashop($arrayMapping[$key],1254);
			break;*/
		}
		
		echo "<br><br>finish!";
		
	}catch(Exception $e){
		echo $e->getMessage();
	}
	
	
	
	
	
?>