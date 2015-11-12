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
		
		private function setCategoriesForSingleProduct($productCategory){
			
			$language = 1; //Italian
			$category = new Category();
			$arrayCategories = $category::getSimpleCategories($language);
			$returnArray = array();
			
			$flagParent = true; $nameParent = ""; $idParent = null;
			$flagChild = true;  $nameChild = "";  $idChild = null;
			
			$tmp = explode(",",$productCategory);
			$nameParent = $tmp[1];
			$nameChild = $tmp[0];
			
			foreach($arrayCategories as $array){
				if(! empty($category::getChildren($array["id_category"],$language))){
					if(trim($array["name"]) === trim($nameParent)){
						$flagParent = false; $idParent = $array["id_category"];
						$arrayChildrenFromParentID = $category::getChildren($idParent,$language);
						echo "<br>IDPARENT: ".$array["id_category"];
						if(empty($arrayChildrenFromParentID))
							break;
						else{
							foreach($arrayChildrenFromParentID as $childrenFromIdParent){
								if(trim($childrenFromIdParent["name"]) === trim($nameChild)){
									echo "<br>IDchild: ".$childrenFromIdParent["id_category"];
									$flagChild = false; $idChild = $childrenFromIdParent["id_category"];
									break;
								}
							}
						}
						
						break;
					}
				}
			}
			$replace = array("."," ",",","&");
			if($flagParent && $flagChild){
				echo "Entroentro";
				$categoryParent = new Category();
				$categoryParent->active = 1;
				$categoryParent->id_parent = 2;
				$categoryParent->name = array($language=>$nameParent);
				$categoryParent->link_rewrite = array($language=>str_replace($replace,"-",$nameParent));
			
				$categoryParent->add();
				
				$idParent = $categoryParent->id;
				
				$categoryChild = new Category();
				$categoryChild->active = 1;
				$categoryChild->id_parent = $idParent;
				$categoryChild->name = array($language=>$nameChild);
				$categoryChild->link_rewrite = array($language=>str_replace($replace,"-",$nameChild));
				$categoryChild->add();
				$idChild = $categoryChild->id;
			}else{
				/*if($flagParent){
					$categoryParent = new Category();
					$categoryParent->active = 1;
					$categoryParent->id_parent = 2;
					$categoryParent->name = array($language=>$nameParent);
					$categoryParent->link_rewrite = array($language=>str_replace($replace,"-",$nameParent));
					$categoryParent->add();
					$idParent = $categoryParent->id;
				}*/
				if($flagChild){
					echo "entro";
					$categoryChild = new Category();
					$categoryChild->active = 1;
					$categoryChild->id_parent = $idParent;
					$categoryChild->name = array($language=>$nameChild);
					$categoryChild->link_rewrite = array($language=>str_replace($replace,"-",$nameChild));
					$categoryChild->add();
					$idChild = $categoryChild->id;
				}
			}
			
			array_push($returnArray,2); //Aggiungo il prodotto anche alla Home
			array_push($returnArray,$idParent);
			array_push($returnArray,$idChild);
			
			return $returnArray;
		}
		
		public function insertProductForPrestashop($productAttributes = array()){
			$product = new Product();
			$language = 1; //Italian => languages = 1
			
			$stringa = $productAttributes["Nome"];
			$element = array();
			$element[$language] = $stringa;
			
			$product->name = $element;
			$product->meta_keywords = $stringa;
			
			$stringa = str_replace(" ","-",$stringa);
			$element = array();
			$element[$language] = $stringa;
			
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
			
			$manufactures = new Manufacturer();
			$product->id_manufacturer = (int) $manufactures::getIdByName($productAttributes["Manufacture"]);
			
			$arrayFeatures = $productAttributes["Feature"];
			$product->width = (float)$arrayFeatures["Larghezza"];
			$product->height = (float)$arrayFeatures["Altezza"];
			$product->depth = (float)$arrayFeatures["Lunghezza"];
			
			$product->add();
			StockAvailable::setQuantity($product->id,'',(int)$productAttributes["Qta"]);
			$product->addToCategories($this->setCategoriesForSingleProduct($productAttributes["Categorie"]));
		}
		
	}
	
	$prova = new ProductForPrestashop();
	$ppp = array (
		'Prezzo'=> 81.00, 
		'Attivo'=>1, 
		'Reference'=>'KPBS03S05', 
		'Nome'=>'Valentino Vivone', 
		'Categorie' => "Cintura,VALENTINO",
		'Supplier'=>"Fashion Supplier", 
		'Manufacture'=>"Fashion Manufacturer", 
		'Qta'=>8, 
		'Qta_min'=>1, 
		'Feature'=>array(
			'Larghezza'=>14.000, 
			'Altezza'=>35.000, 
			'Lunghezza'=>36.000 )
		);
	try{
		$prova->insertProductForPrestashop($ppp);
	}catch(Exception $e){
		echo $e->getMessage();
	}
	$ppp = array (
		'Prezzo'=> 81.00, 
		'Attivo'=>1, 
		'Reference'=>'KPBS03S05', 
		'Nome'=>'Carlos Borges', 
		'Categorie' => "Shopping,MIRIADE",
		'Supplier'=>"Fashion Supplier", 
		'Manufacture'=>"Fashion Manufacturer", 
		'Qta'=>8, 
		'Qta_min'=>1, 
		'Feature'=>array(
			'Larghezza'=>14.000, 
			'Altezza'=>35.000, 
			'Lunghezza'=>36.000 )
		);
	try{
		$prova->insertProductForPrestashop($ppp);
	}catch(Exception $e){
		echo $e->getMessage();
	}
	//$prova->updateImageInPrestashop(61,129,"C:\\Users\\Valentino\\Desktop\\KPBS03S05_3_005.jpg");
	
	/*try{
		$mapping = new Mapping("File/A20151016144918_SEM.chk");
		
		$keys = $mapping->keys();
		
		$arrayMapping = $mapping->getItemMaster();
		
		//$arrayCombinations = $mapping->getCombinations();
		
		foreach($keys as $key){
			$prova->insertProductForPrestashop($arrayMapping[$key]);
			echo "key: $key<br>";
		}
		
		echo "<br><br>finish!";
		
	}catch(Exception $e){
		echo $e->getMessage();
	}
	
	
	*/
	
	
	
	
?>