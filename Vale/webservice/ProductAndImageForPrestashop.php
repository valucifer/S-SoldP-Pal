<?php
	include "../config/config.inc.php";
	include "../init.php";
	require_once "./class.Compare.php";
	
	class ImageForPrestashop{
		
		function ImageForPrestashop(){}
		
		public function insertImageInPrestashop($id_prod, $url){
			$id_product = $id_prod;
	
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
	
		}
		
		public function updateImageInPrestashop($id_prod, $id_img, $url){
			if($id_img == "") $this->insertImageInPrestashop($id_prod,$url);
			else{
				$id_product = $id_prod;
				
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
		
		public function createCategories($categories){
			$language = 1; //Italian
			$sizeCategories = sizeof($categories);
			for($i = 0; $i < $sizeCategories; $i++){
				$category = new Category();
				$arrayCategories = $category::searchByNameAndParentCategoryId($language,trim($categories[$i]),2);
				
				if(empty($arrayCategories)){
					$replace = array("."," ",",","&");
					$categoryParent = new Category();
					$categoryParent->active = 0;
					$categoryParent->id_parent = 2;
					$categoryParent->name = array($language=>trim($categories[$i]));
					$categoryParent->link_rewrite = array($language=>strtolower(str_replace($replace,"-",$categories[$i])));
					$categoryParent->add();
				}
			}
		}
		
		public function createSubCategories($categories,$subCategories){
			$language = 1; //Italian
			$sizeSubCategories = sizeof($subCategories);
			$sizeCategories = sizeof($categories);
			
			$category = new Category();
			for($i = 0; $i < $sizeCategories; $i++){
				$nameCategory = trim($categories[$i]);
				for($j = 0; $j < $sizeSubCategories; $j++){
						
					$flagChild = true;
					$idParent = 2;
					$arrayParent = $category::searchByNameAndParentCategoryId($language,trim($nameCategory),2);
					
					if(!empty($arrayParent)){
						$idParent = (int)$arrayParent["id_category"];
						$arrayFlag = $category::searchByNameAndParentCategoryId($language,trim($subCategories[$j]),$idParent);
						
						if(empty($arrayFlag)){
							$replace = array("."," ",",","&");
							$categoryChild = new Category();
							$categoryChild->active = 0;
							$categoryChild->id_parent = $idParent;
							$categoryChild->name = array($language=>trim($subCategories[$j]));
							$categoryChild->link_rewrite = array($language=>strtolower(str_replace($replace,"-",trim($subCategories[$j]))));
							$categoryChild->add();
						}
					}
					
				}
			}
		}
		
		public function insertProductForPrestashop($productAttributes = array(), $urlFoto){
			$product = new Product();
			$language = 1; //Italian => languages = 1
			
			$stringa = $productAttributes["Nome"];
			$element = array();
			$element[$language] = $stringa;
			
			$product->name = $element;
			$product->meta_keywords = $stringa;
			$replace = array("."," ",",","&","+","*","/","\\",":",";","_","=","!","?","'","\"","$","€","<",">");
			$stringa = str_replace($replace,"-",$stringa);
			$element = array();
			$element[$language] = strtolower($stringa);
			
			$product->link_rewrite = $element;
			
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
			
			$supplier = new SupplierCore();
			$id_supplier = $supplier::getIdByName(trim($productAttributes["Supplier"]));
			
			if(! $id_supplier ){
				$supplier->name = trim($productAttributes["Supplier"]);
				$supplier->add();
			}
			
			$id_supplier = $supplier::getIdByName(trim($productAttributes["Supplier"]));
			$product->id_supplier = (int)$id_supplier;
			
			$manufacturer = new ManufacturerCore();
			$id_manufacturer = $manufacturer::getIdByName(trim($productAttributes["Manufacture"]));
			if(! $id_manufacturer ){
				$manufacturer->name = trim($productAttributes["Manufacture"]);
				$manufacturer->add();
			}
			
			$id_manufacturer = $manufacturer::getIdByName(trim($productAttributes["Manufacture"]));
			$product->id_manufacturer = (int)$id_manufacturer;
			
			$arrayFeatures = $productAttributes["Feature"];
			$product->width = (float)$arrayFeatures["Larghezza"];
			$product->height = (float)$arrayFeatures["Altezza"];
			$product->depth = (float)$arrayFeatures["Lunghezza"];
			
			$product->add();
			StockAvailable::setQuantity($product->id,'',(int)$productAttributes["Qta"]);
			
			$categories = trim($productAttributes["Categorie"]);
			$tmp = explode(",",$categories);
			$griffe = $tmp[1];
			$modello = $tmp[0];
			$idParent = null;
			$idChild = null;
			
			$category = new Category();
			$arrayParent = $category::searchByNameAndParentCategoryId($language,trim($griffe),2);
			$idParent = (int)$arrayParent["id_category"];
			$arrayChild = $category::searchByNameAndParentCategoryId($language,trim($modello),$idParent);
			$idChild = (int)$arrayChild["id_category"];
			$pos = array(); array_push($pos,$idChild); array_push($pos,$idParent);
			$product->addToCategories($pos);
			
			$activeCategory = new Category($idParent);
			$activeCategory->active = 1;
			$activeCategory->update();
			
			$activeCategory = new Category($idChild);
			$activeCategory->active = 1;
			$activeCategory->update();
			
			$tmp =  explode(".jpg,", trim($productAttributes["URL"]));
			$sizeUrl = sizeof($tmp);
			for($i = 0; $i < $sizeUrl; $i++){
				if(!empty($tmp[$i])){
					$url = trim($urlFoto).$tmp[$i].".jpg";
					$image = new ImageForPrestashop();
					$image->insertImageInPrestashop($product->id,$url);
				}
			}
		}
		
		public function updateProductForPrestashop($productAttributes = array(), $idProduct){
			$product = new Product($idProduct);
			
			$language = 1; //Italian => languages = 1
			
			$stringa = $productAttributes["Nome"];
			$element = array();
			$element[$language] = $stringa;
			
			$product->name = $element;
			$product->meta_keywords = $stringa;
			$replace = array("."," ",",","&","+","*","/","\\",":",";","_","=","!","?","'","\"","$","€","<",">");
			$stringa = str_replace($replace,"-",$stringa);
			$element = array();
			$element[$language] = strtolower($stringa);
			
			$product->link_rewrite = $element;
			
			$product->id_category_default = 2; //Home
			$product->redirect_type = '404';
			$product->price = (float) $productAttributes["Prezzo"];
			$product->active = $productAttributes["Attivo"];
			
			if($productAttributes["Attivo"] == 0){
				
			}
			
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
		
		$arrayCategories = $mapping->getCategory();
		$arraySubCategories = $mapping->getSubCategory();
		
		$prova->createCategories($arrayCategories);
		$prova->createSubCategories($arrayCategories,$arraySubCategories);
		
		//$arrayCombinations = $mapping->getCombinations();
		foreach($keys as $key){
			/*try{
				$prova->insertProductForPrestashop($arrayMapping[$key],$urlFoto);
			}catch(Exception $e){
				echo "$key<br/>";
				echo "$e->getMessage() in line $e->getLine()<br/>";
				echo "<br/><br/><br/><br/>";
			}*/
			$prova->updateProductForPrestashop($arrayMapping[$key],1254);
			break;
		}
		
		echo "<br><br>finish!";
		
	}catch(Exception $e){
		echo $e->getMessage();
	}
	
	
	
	
	
?>