<?php
	include "../config/config.inc.php";
	include "../init.php";
	require_once "./class.Compare.php";
	
	class ImageForPrestashop{
		
		function ImageForPrestashop(){}
		
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
	
	class ProductForPrestashop{
		
		function ProductForPrestashop(){}
		
		private function setArrayElementForLinkRewrite($name_element, $is_name = true, $language = 1){
			if($is_name){
				$name_product = array($language=>trim($name_element));
				return $name_product;
			}else{
				$array_replace = array("."," ",",","&","+","*","/","\\",":",";","_","=","!","?","'","\"","$","€","<",">");
				$link_rewrite_product = array($language=>strtolower(str_replace($array_replace,"-",trim($name_element))));
				return $link_rewrite_product;
			}
		}
		
		private function setCategoriesForProduct($name_category, $id_parent_category = 2, $language = 1){
			$id_category = 2;
			$category = new Category();
			$arrayCategories = $category::searchByNameAndParentCategoryId(trim($language),$name_category,trim($id_parent_category));
			
			if(empty($arrayCategories)){
				$category_for_product = new Category();
				$category_for_product->active = 0;
				$category_for_product->id_parent = (int) trim($id_parent_category);
				$category_for_product->name = $this->setArrayElementForLinkRewrite($name_category);
				$category_for_product->link_rewrite = $this->setArrayElementForLinkRewrite($name_category,false);
				$category_for_product->meta_keywords = trim($name_category);
				$category_for_product->add();
				$id_category = $category_for_product->id;
			}else{
				$id_category = (int) $arrayCategories["id_category"];
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
		
		public function insertProductForPrestashop($productAttributes = array(), $urlFoto){
			$product = new Product();
			$language = 1; //Italian => languages = 1
			
			$string_name_product = $productAttributes["Nome"];
			$product->name = $this->setArrayElementForLinkRewrite($string_name_product);
			$product->meta_keywords = $string_name_product;
			$product->link_rewrite = $this->setArrayElementForLinkRewrite($string_name_product, false);
			
			$product->id_category_default = 2; //Home
			$product->redirect_type = '404';
			$product->price = (float) $productAttributes["Prezzo"];
			$product->active = $productAttributes["Attivo"];
			$product->minimal_quantity = (int)$productAttributes["Qta_min"];
			$product->show_price = 1;
			$product->on_sale = 0;
			$product->reference = $productAttributes["Reference"];
			$product->online_only = 1;
			$product->id_tax_rules_group = 0;
			
			$product->id_supplier = $this->setSupplierForProduct($productAttributes["Supplier"]);
			$product->id_manufacturer = $this->setManufacturerForProduct($productAttributes["Manufacture"]);
			
			$arrayFeatures = $productAttributes["Feature"];
			$product->width = (float)$arrayFeatures["Larghezza"];
			$product->height = (float)$arrayFeatures["Altezza"];
			$product->depth = (float)$arrayFeatures["Lunghezza"];
			
			$product->add();
			
			$categories = trim($productAttributes["Categorie"]);
			$tmp = explode(",",$categories);
			
			$griffe = $tmp[1]; $modello = $tmp[0];
			$idParent = null;  $idChild = null;
			
			$idParent = $this->setCategoriesForProduct($griffe);
			$idChild = $this->setCategoriesForProduct($modello,$idParent);
			
			$pos = array(); array_push($pos,$idChild); array_push($pos,$idParent);
			$product->addToCategories($pos);
			
			if($product->active != 0){
				$activeCategory = new Category($idParent);
				$activeCategory->active = 1;
				$activeCategory->update();
				
				$activeCategory = new Category($idChild);
				$activeCategory->active = 1;
				$activeCategory->update();
			}
				
			$tmp =  explode(".jpg,", trim($productAttributes["URL"]));
			$sizeUrl = sizeof($tmp);
			$arrayIdImage = array();
			for($i = 0; $i < $sizeUrl; $i++){
				if(!empty($tmp[$i])){
					$url = trim($urlFoto).$tmp[$i].".jpg";
					$image = new ImageForPrestashop();
					$idImage = $image->insertImageInPrestashop($product->id,$url);
					array_push($arrayIdImage,$idImage);
				}
			}
			$product->addProductAttribute($product->price, 0.000, 0.000, 0.000, (int)$productAttributes["Qta"], $arrayIdImage, $product->reference, 
			$product->id_supplier, 0, '');
		}
		
		private function controlCategoriesForActivateTheir($idCategory){
			
		}
		
		public function updateProductForPrestashop($productAttributes = array(), $idProduct){
			$product = new Product($idProduct);
			$language = 1; //Italian => languages = 1
			
			$product->name = $this->setArrayElementForLinkRewrite($stringa);
			$product->meta_keywords = $stringa;
			$product->link_rewrite = $this->setArrayElementForLinkRewrite($stringa,false);
			
			$product->id_category_default = 2; //Home
			$product->redirect_type = '404';
			$product->price = (float) $productAttributes["Prezzo"];
			$product->active = $productAttributes["Attivo"];
			
			$product->minimal_quantity = (int)$productAttributes["Qta_min"];
			$product->show_price = 1;
			$product->on_sale = 0;
			$product->online_only = 1;
			$product->id_tax_rules_group = 0;
			
			$arrayFeatures = $productAttributes["Feature"];
			$product->width = (float)$arrayFeatures["Larghezza"];
			$product->height = (float)$arrayFeatures["Altezza"];
			$product->depth = (float)$arrayFeatures["Lunghezza"];
			
			$product->update();
			StockAvailable::setQuantity($product->id,'',(int)$productAttributes["Qta"]);
			
		}
	}
	
	ini_set('max_execution_time', 600);
	$prova = new ProductForPrestashop();
	
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