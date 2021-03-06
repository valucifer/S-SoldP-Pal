<?php
include "../../config/config.inc.php";//da controllare durante l'installazione finale (Carlos Borges)
include "../../init.php";
require_once ("Mapping.php");

/**
* This class handles images in Prestashop
*
* @author Valentino Vivone <v.vivone.5389@gmail.com>
* @version 1.0
*
*/
class PrestashopImageHandler{

    public function __construct(){}

    /**
	* Inserts product's image in Prestashop.
	* 
	* @param integer $id_product
	* @param string $url
	* @param string $name_photo
	* @return integer 
	* @see $this->copyImg
	*
	*/
    public function insertImageInPrestashop($id_product, $url, $name_photo){
        $shops = Shop::getShops(true, null, true);    
        $image = new ImageCore();
        $image->id_product = $id_product;
        $image->position = Image::getHighestPosition($id_product) + 1;
        $image->cover = true; // or false;

        $tmp = explode(".",$name_photo);
        $name_photo_product = "";
        $name_for_legend = "";

        if(count($tmp) == 1){
            $name_photo_product = trim($url).$name_photo.".jpg";
            $name_for_legend = $name_photo.".jpg";
        }
        else{
            $name_photo_product = trim($url).$name_photo;
            $name_for_legend = $name_photo;
        }

        $image->legend = array('1'=>trim($name_for_legend));

        if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->add()){
            $image->associateTo($shops);

            if (!$this->copyImg($id_product, $image->id, $name_photo_product, 'products')){
                $image->delete();
            }

        }

        return $image->id;
    }

    /**
	* Updates product image in Prestashop.
	* 
	* @param integer $id_product, integer $id_img, string $url, string $name_photo
	* @return integer 
	* @see $this->copyImg
	*
	*/
    public function updateImageInPrestashop($id_product, $id_img, $url, $name_photo){
        if(empty($id_img)) 
            return (int)$this->insertImageInPrestashop($id_product,$url,$name_photo);
        else{
            $shops = Shop::getShops(true, null, true);    
            $image = new ImageCore((int)$id_img);
            $image->id_product = $id_product;

            $tmp = explode(".",$name_photo);
            $name_photo_product = "";

            $name_for_legend = "";

            if(count($tmp) == 1){
                $name_photo_product = trim($url).$name_photo.".jpg";
                $name_for_legend = $name_photo.".jpg";
            }
            else{
                $name_photo_product = trim($url).$name_photo;
                $name_for_legend = $name_photo;
            }

            $image->legend = array('1'=>trim($name_for_legend));

            if (($image->validateFields(false, true)) === true && ($image->validateFieldsLang(false, true)) === true && $image->update()){
                $image->associateTo($shops);

                if (!$this->copyImg($id_product, $id_img, $name_photo_product, 'products')){
                    $image->delete();
                }

            }

            return (int)$image->id;
        }
    }

    /**
	* Resizes image product or image category in Prestashop.
	* 
	* @param integer $id_entity
	* @param integer $id_image
	* @param string $url
	* @param string $entity
	* @return bool 
	*
	*/
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

    /**
	* Releases a image id in Prestashop. It requires the image name.
	* 
	* @param string $name_photo
	* @param integer $language
	* @return a empty string if the name has not been found, else integer.
	* 
	*/
    public function getIdImageByName($name_photo, $language = 1){
        $id = "";
        $image = new ImageCore();
        $array_all_images = $image->getAllImages();

        $tmp = explode(".",$name_photo);
        $name_photo_product = "";

        if(count($tmp) == 1)
            $name_photo_product = $name_photo.".jpg";
        else
            $name_photo_product = $name_photo;

        foreach($array_all_images as $array_single_image){
            $array_image = new ImageCore((int)$array_single_image['id_image']);
            $image_name = $array_image->legend;

            if(strtolower($image_name[$language]) === strtolower($name_photo_product)){
                $id = (int)$array_image->id;
                break;
            }

        }

        return (int)$id;
    }

}

/**
* This class handles products in Prestashop
*
* @author Valentino Vivone <v.vivone.5389@gmail.com>
* @version 1.0
*
*/
class PrestashopProduct{

    public function __construct(){}

    /**
	* Create an key => value array where the key is the language and the value is a name of element.
	* If $name_element is a name of the element, then $is_name is equal true, else if $name_element is a
	* link_rewrite of the name, then $is_name is equal false.
	* 
	* @param string $name_element
	* @param bool $is_name
	* @param integer $language
	* @return array 
	*
	*/
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

    /**
	* Create a Category in Prestashop.
	* 
	* @param string $name_category
	* @param integer $id_parent_category
	* @param integer $language
	* @return integer 
	*
	*/
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

    /**
	* Creates a Supplier in Prestashop if not exists, returns supplier id elsewhere.
	* 
	* @param string $name_supplier
	* @return integer 
	*
	*/
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

    /**
	* Creates a Manufacturer in Prestashop if not exists, return manufacturer id elsewhere.
	* 
	* @param string $name_manufacturer
	* @return integer 
	*
	*/
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

    /**
	* Creates product features in Prestashop.
	* 
	* @param integer $id_product
	* @param integer $language
	* @param array $array_features_product
	* @return 
	*
	*/
    private function addFeaturesForProducts($id_product, $language, $array_features_product){
        $feature_product = new FeatureCore();
        $feature_product_value = new FeatureValueCore();

        $id_feature_width = $feature_product->addFeatureImport("Larghezza");
        $id_feature_height = $feature_product->addFeatureImport("Altezza");
        $id_feature_size = $feature_product->addFeatureImport("Lunghezza");
        $id_feature_model = $feature_product->addFeatureImport("Modello");
        $id_feature_collez = $feature_product->addFeatureImport("Linea");

        $array_id_features = array();
        array_push($array_id_features,$id_feature_width);
        array_push($array_id_features,$id_feature_height);
        array_push($array_id_features,$id_feature_size);
        array_push($array_id_features,$id_feature_model);
        array_push($array_id_features,$id_feature_collez);

        $string_value_width = $array_features_product["Larghezza"]." cm";
        $string_value_height = $array_features_product["Altezza"]." cm";
        $string_value_size = $array_features_product["Lunghezza"]." cm";
        $string_value_model = $array_features_product["Modello"];
        $string_value_collez = $array_features_product["Linea"];

        $array_id_feature_value = array();
        array_push($array_id_feature_value,$feature_product_value->addFeatureValueImport($id_feature_width,$string_value_width,(int)$id_product,$language));
        array_push($array_id_feature_value,$feature_product_value->addFeatureValueImport($id_feature_height,$string_value_height,(int)$id_product,$language));
        array_push($array_id_feature_value,$feature_product_value->addFeatureValueImport($id_feature_size,$string_value_size,(int)$id_product,$language));
        array_push($array_id_feature_value,$feature_product_value->addFeatureValueImport($id_feature_model,$string_value_model,(int)$id_product,$language));
        array_push($array_id_feature_value,$feature_product_value->addFeatureValueImport($id_feature_collez,$string_value_collez,(int)$id_product,$language));

        $size_feature_value = sizeof($array_id_feature_value);
        $product = new Product((int)$id_product);
        for($i = 0; $i < $size_feature_value; $i++){
            $product->addFeaturesToDB($array_id_features[$i],$array_id_feature_value[$i]);
        }

    }

    /**
	* Inserts a product in Prestashop.
	* 
	* @param array $product_attributes
	* @param string $url
	* @param array $triple_cod_col_siz
	* @param array $array_combinations
	* @param integer $language
	* @return array 
	* @see $this->setSupplierForProduct
	* @see $this->setArrayElementForLinkRewrite
	* @see $this->setManufacturerForProduct
	* @see $this->addFeaturesForProducts
	* @see $this->setCategoriesForProduct
	* @see $this->addCombinationsForPrestashop
	* @see PrestashopImageHandler insertImageInPrestashop()
	*
	*/
    public function insertProductForPrestashop($product_attributes = array(), $url_photo, $triple_cod_col_siz, $array_combinations, $language = 1){
        $product = new Product();
        //$language is 1 because 1 -> italian

        $string_name_product = trim($product_attributes["Nome"]);
        $product->name = $this->setArrayElementForLinkRewrite($string_name_product, true, $language);
        $product->meta_keywords = $string_name_product;
        $product->link_rewrite = $this->setArrayElementForLinkRewrite($string_name_product, false, $language);

        $product->id_category_default = 2; //Home
        $product->redirect_type = '404';
        $product->price = (float) $product_attributes["Prezzo"];
        $product->active = (int) $product_attributes["Attivo"];
        $product->minimal_quantity = (int)$product_attributes["Qta_min"];
        $product->show_price = 1;
        $product->reference = $product_attributes["Reference"];
        $product->id_tax_rules_group = 0;

        //$product->id_supplier = $this->setSupplierForProduct(trim($product_attributes["Supplier"]));
        $product->id_supplier = 0;
        $product->id_manufacturer = $this->setManufacturerForProduct(trim($product_attributes["Manufacture"]));
		
        $array_features = $product_attributes["Feature"];
        $product->width = (float)$array_features["Larghezza"];
        $product->height = (float)$array_features["Altezza"];

        $product->add();

        $this->addFeaturesForProducts($product->id, $language, $array_features);

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
                $url = trim($url_photo);
                $image = new PrestashopImageHandler();
                $id_image = $image->insertImageInPrestashop($product->id,$url,$tmp[$i]);
                array_push($array_id_image,$id_image);
            }
        }

        $this->addCombinationsForPrestashop($product->id, $url_photo, $triple_cod_col_siz, $array_combinations, $language);

        $return = array();
        $array_images_combinations_of_the_product = $product->getImages($language);
        array_push($return, $product->id);

        $element = array();
        foreach($array_images_combinations_of_the_product as $array_combo_image){
            $name_of_the_image = $array_combo_image['legend'];
            $id_image_of_the_product = $array_combo_image['id_image'];
            array_push($element, $id_image_of_the_product.";".$name_of_the_image );
        }
        array_push($return, $element);
        return $return;
    }

    /**
	* Creates a product Combination.
	* 
	* @param integer $id_product
	* @param string $url_photo
	* @param array $triple_cod_col_siz
	* @param array $array_combinations
	* @param integer $language
	* @return  
	* @see $this->createAttributeGroups
	* @see $this->setArrayElementForLinkRewrite
	* @see $this->getCodeColor
	* @see PrestashopImageHandler getIdImageByName()
	* @see PrestashopImageHandler insertImageInPrestashop()
	*
	*/
    private function addCombinationsForPrestashop($id_product, $url_photo, $triple_cod_col_siz, $array_combinations, $language = 1){
        $product = new Product((int)$id_product);

        $price = 0.000;//(float)$product->price; In futuro si deve de-commentare per attuare il prezzo ( cambiare la combinazione)

        $reference = $product->reference;
        $id_supplier = (int)$product->id_supplier;

        foreach($triple_cod_col_siz as $triple){
            $array_attributes_and_values = $array_combinations[$triple];

            $attributes = $array_attributes_and_values["Attributi"];
            $values = $array_attributes_and_values["Valori"];
            $image = trim($array_attributes_and_values["Immagine"]);
            $quantity = (int)$array_attributes_and_values["Qta"];

            $variable_tmp_attributes = explode(",",$attributes);
            $variable_tmp_values = explode(",",$values);

            $id_attributes_for_combinations = array();
            for($i = 0; $i < sizeof($variable_tmp_attributes); $i++){
                $code = "";
                if($variable_tmp_values[$i] != ""){
                    $id_attribute_group = $this->createAttributeGroups($variable_tmp_attributes[$i], $language);
					$id_attributes_for_not_repeat = $this->getAttributeColorAndSize($variable_tmp_values[$i], $language);
                        
                    if(strtolower($variable_tmp_attributes[$i]) === "colore" || strtolower($variable_tmp_attributes[$i]) === "colori"){
                        $product_attribute_for_not_reply = $product->getAttributeCombinations($language);
						
						if(empty($product_attribute_for_not_reply)){
							
							if($id_attributes_for_not_repeat === '-1'){
								$code = $this->getCodeColor(strtolower($variable_tmp_values[$i]));
								if($code != ""){
									$code = "#".$code;
									$attribute_for_product = new Attribute();
									$attribute_for_product->name = $this->setArrayElementForLinkRewrite($variable_tmp_values[$i], true, $language);
									$attribute_for_product->color = $code;
									$attribute_for_product->id_attribute_group = $id_attribute_group;
									$attribute_for_product->add();
									array_push($id_attributes_for_combinations, $attribute_for_product->id);
								}
							}else{
								array_push($id_attributes_for_combinations, (int)$id_attributes_for_not_repeat);
							}
							
                        }else{
                            $flag = true;
                            foreach($product_attribute_for_not_reply as $more_attributes){
                                if($more_attributes['attribute_name'] === $variable_tmp_values[$i]){
                                    array_push($id_attributes_for_combinations, (int)$more_attributes['id_attribute']);
                                    $flag = false;
                                    break;
                                }
                            }
                            if($flag){
								if($id_attributes_for_not_repeat === '-1'){
									$code = $this->getCodeColor(strtolower($variable_tmp_values[$i]));
									if($code != ""){
										$code = "#".$code;
										$attribute_for_product = new Attribute();
										$attribute_for_product->name = $this->setArrayElementForLinkRewrite($variable_tmp_values[$i], true, $language);
										$attribute_for_product->color = $code;
										$attribute_for_product->id_attribute_group = $id_attribute_group;
										$attribute_for_product->add();
										array_push($id_attributes_for_combinations, $attribute_for_product->id);
									}
								}else{
									array_push($id_attributes_for_combinations, $id_attributes_for_not_repeat);
								}
                            }
							
                        }
                    }

                    if(strtolower($variable_tmp_attributes[$i]) === "taglia" || strtolower($variable_tmp_attributes[$i]) === "taglie"){
                        $product_attribute_for_not_reply = $product->getAttributeCombinations($language);
						
						if(empty($product_attribute_for_not_reply)){
							if($id_attributes_for_not_repeat === '-1'){
								$attribute_for_product = new Attribute();
								$attribute_for_product->name = $this->setArrayElementForLinkRewrite($variable_tmp_values[$i], true, $language);
								$attribute_for_product->id_attribute_group = $id_attribute_group;
								$attribute_for_product->add();
								array_push($id_attributes_for_combinations, $attribute_for_product->id);
							}else{
								array_push($id_attributes_for_combinations, (int)$id_attributes_for_not_repeat);
							}
                        }else{
                            $flag = true;
                            foreach($product_attribute_for_not_reply as $more_attributes){
                                $vrbls_1 = trim($more_attributes['attribute_name']);
                                $vrbls_2 = trim($variable_tmp_values[$i]);
                                if(gettype($vrbls_1) === "string" && gettype($vrbls_2) === "string"){
                                    if($vrbls_1 === $vrbls_2){
                                        array_push($id_attributes_for_combinations, (int)$more_attributes['id_attribute']);
                                        $flag = false;
                                        break;
                                    }
                                }
                                if(gettype($vrbls_1) === "integer" && gettype($vrbls_2) === "integer"){
                                    if($vrbls_1 == $vrbls_2){
                                        array_push($id_attributes_for_combinations, (int)$more_attributes['id_attribute']);
                                        $flag = false;
                                        break;
                                    }
                                }

                            }
                            if($flag){
								if($id_attributes_for_not_repeat === '-1'){
									$attribute_for_product = new Attribute();
									$attribute_for_product->name = $this->setArrayElementForLinkRewrite($variable_tmp_values[$i], true, $language);
									$attribute_for_product->id_attribute_group = $id_attribute_group;
									$attribute_for_product->add();
									array_push($id_attributes_for_combinations, $attribute_for_product->id);
								}else{
									array_push($id_attributes_for_combinations, (int)$id_attributes_for_not_repeat);
								}
                            }
                        }
                    }
                }
            }

            $id_images = array();
            $tmp_photo = explode(".jpg,",$image);

            for($i = 0; $i < sizeof($tmp_photo); $i++){
                if(!empty($tmp_photo[$i])){
                    $image_for_prestashop = new PrestashopImageHandler();
                    $id_image = $image_for_prestashop->getIdImageByName(trim($tmp_photo[$i]));

                    if(!empty($id_image)){
                        array_push($id_images, $id_image);
                    }
                    else{
                        $id_image = $image_for_prestashop->insertImageInPrestashop($id_product, trim($url_photo), trim($tmp_photo[$i]));
                        array_push($id_images, $id_image);
                    }
                }
            }

            $id_product_attributes = $product->addProductAttribute($price, 0, 0, 0, $quantity, "", $reference, $id_supplier, 0, 1);

            $combinations = new CombinationCore((int)$id_product_attributes);

            $combinations->setAttributes($id_attributes_for_combinations);
            $combinations->setImages($id_images);
        }
    }

    /**
	* Returns a color code from color name take as input.
	* 
	* @param string $name_color
	* @return string
	*
	*/
    private function getCodeColor($name_color){
        $return = "";
        $colors  =  array('blue alice'=>'F0F8FF','alice blu'=>'F0F8FF','bianco antico'=>'FAEBD7','aqua'=>'00FFFF','acqua'=>'00FFFF','acqua marina'=>'7FFFD4', 'azzurro'=>'F0FFFF','beige'=>'F5F5DC','biscotto'=>'FFE4C4',
                          'nero'=>'000000','blanched almond'=>'FFEBCD','mandorla sbiancata'=>'FFEBCD','blue'=>'0000FF','blu'=>'0000FF','blue violet'=>'8A2BE2','blue viola'=>'8A2BE2','blu violetto'=>'8A2BE2','marrone di fuoco'=>'A52A2A',
                          'burly wood'=>'DEB887','legno corpulento'=>'DEB887','legno'=>'DEB887','cadet blue'=>'5F9EA0','cadetto blu'=>'5F9EA0','chartreuse'=>'7FFF00','cioccolato'=>'D2691E','corallo'=>'FF7F50','bluetto'=>'6495ED',
                          'seta di mais'=>'FFF8DC','seta'=>'FFF8DC','cremisi'=>'DC143C','ciano'=>'00FFFF','blu scuro'=>'00008B','ciano scuro'=>'008B8B','verga d\'oro scuro'=>'B8860B','grigio scuro'=>'A9A9A9','verde scuro'=>'006400',
                          'kaki scuro'=>'BDB76B','magenta scuro'=>'8B008B','verde oliva scuro'=>'556B2F','arancione scuro'=>'FF8C00','orchidea scuro'=>'9932CC','rosso scuro'=>'8B0000','salmone scuro'=>'E9967A','verde acqua scuro'=>'8FBC8F',
                          'ardesia blu scuro'=>'483D8B','ardesia grigio scuro'=>'2F4F4F','turchese scuro'=>'00CED1','viola scuro'=>'9400D3','rosa intenso'=>'FF1493','blu cielo profondo'=>'00BFFF','dimgray'=>'696969','blu evaso'=>'1E90FF',
                          'mattone'=>'B22222','bianco floreale'=>'FFFAF0','verde foresta'=>'228B22','fucsia'=>'FF00FF','gainsboro'=>'DCDCDC','bianco fantasma'=>'F8F8FF','oro'=>'FFD700','goldenrod'=>'DAA520','grigio'=>'808080','verde'=>'008000',
                          'verde giallo'=>'ADFF2F','miele'=>'F0FFF0','rosa forte'=>'FF69B4','rosso indiano'=>'CD5C5C','indaco'=>'4B0082','avorio'=>'FFFFF0','kaki'=>'F0E68C','lavanda'=>'E6E6FA','lavanda arrossita'=>'FFF0F5',
                          'prato verde'=>'7CFC00','chiffon limone'=>'FFFACD','blu chiaro'=>'ADD8E6','corallo chiaro'=>'F08080','ciano chiaro'=>'E0FFFF','lightgoldenrodyellow'=>'FAFAD2','grigio chiaro'=>'D3D3D3','verde chiaro'=>'90EE90',
                          'rosa chiaro'=>'FFB6C1','salmone chiaro'=>'FFA07A','verde acqua chiaro'=>'20B2AA','blu chielo chiaro'=>'87CEFA','grigio ardesia chiaro'=>'778899','blu acciaio chiaro'=>'B0C4DE','giallo chiaro'=>'FFFFE0',
                          'lime'=>'00FF00','verde lime'=>'32CD32','biancheria'=>'FAF0E6','magenta'=>'FF00FF','marrone'=>'800000','acquamarina media'=>'66CDAA','blu medio'=>'0000CD','orchidea media'=>'BA55D3','porpora media'=>'9370D0',
                          'verde acqua media'=>'3CB371','orchidea blu media'=>'7B68EE','verde primavera media'=>'00FA9A','turchese medio'=>'48D1CC','rosso viola medio'=>'C71585','blu mezzanotte'=>'191970','crema di menta'=>'F5FFFA',
                          'rosa nebbia'=>'FFE4E1','mocassino'=>'FFE4B5','bianco navajo'=>'FFDEAD','marina militare'=>'000080','vecchi merletti'=>'FDF5E6','oliva'=>'808000','verde oliva'=>'6B8E23','arancione'=>'FFA500','rosso arancio'=>'FF4500',
                          'rosso arancione'=>'FF4500','orchidea'=>'DA70D6','verde pallido'=>'98FB98','turchese pallido'=>'AFEEEE','rosso violetto pallido'=>'DB7093','papaya'=>'FFEFD5','soffio pesca'=>'FFDAB9','peru'=>'CD853F','rosa'=>'FFC0CB',
                          'prugna'=>'DDA0DD','blu in polvere'=>'B0E0E6','porpora'=>'800080','rosso'=>'FF0000','mattone rosato'=>'BC8F8F','blu reale'=>'4169E1','marrone sella'=>'8B4513','salmone'=>'FA8072','marrone sabbia'=>'F4A460',
                          'verde mare'=>'2E8B57','conchiglia'=>'FFF5EE','sienna'=>'A0522D','argento'=>'C0C0C0','blu cielo'=>'87CEEB','blu orchidea'=>'6A5ACD','grigio orchidea'=>'708090','neve'=>'FFFAFA','verde primavera'=>'00FF7F',
                          'blu acciaio'=>'4682B4','abbronzatura'=>'D2B48C','teal'=>'008080','cardo'=>'D8BFD8','pomodoro'=>'FF6347','turchese'=>'40E0D0','viola'=>'EE82EE','frumento'=>'F5DEB3','bianco'=>'FFFFFF','bianco fumo'=>'F5F5F5',
                          'giallo'=>'FFFF00','giallo verde'=>'9ACD32');

        if(!array_key_exists($name_color, $colors)){
            return "FFFFFF";
        }else return $colors[$name_color];
    }
	
	private function getAttributeColorAndSize($name_attribute, $language = 1){
		$attribute = new Attribute();
		$array_all_attributes = $attribute->getAttributes($language);
		
		foreach($array_all_attributes as $color_or_size){
			if(strtolower($color_or_size["name"]) === strtolower(trim($name_attribute))){
				return (int)$color_or_size["id_attribute"];
			}
		}
		
		return '-1';
	}

    /**
	* Creates a group of attributes in Prestashop.
	* 
	* @param string $attribute_product
	* @param integer $language
	* @return integer
	* @see $this->setArrayElementForLinkRewrite
	*
	*/
    private function createAttributeGroups($attribute_product, $language = 1){
        $attributes_groups = new AttributeGroupCore();
        $array_attributes_groups = $attributes_groups->getAttributesGroups($language);

        foreach($array_attributes_groups as $array_single_attribute_group){
            if(strtolower($array_single_attribute_group["name"]) === strtolower(trim($attribute_product))){
                return (int)$array_single_attribute_group["id_attribute_group"];
            }
        }

        $attribute_group = new AttributeGroupCore();

        $attribute_group->name = $this->setArrayElementForLinkRewrite($attribute_product, true, $language);
        $attribute_group->public_name = $this->setArrayElementForLinkRewrite($attribute_product, true, $language);

        $type = strtolower($attribute_product);
        if(strtolower($attribute_product) == "colore" || strtolower($attribute_product) == "colori")
            $type = "color";
        if(strtolower($attribute_product) == "taglia" || strtolower($attribute_product) == "taglie")
            $type = "select";

        $attribute_group->group_type = $type;
        $attribute_group->add();

        return (int)$attribute_group->id;

    }

    /**
	* Controls and changes, if possible, category's active parameter if a product's active parameter changes.
	* 
	* @param array $ids_categories_array
	* @return 
	*
	*/
    private function controlCategoriesForActivateTheir($ids_categories_array){
        $size_array_categories = sizeof($ids_categories_array);

        for($i = 0; $i < $size_array_categories; $i++){
            $tmp = $ids_categories_array[$i];
            $category = new Category((int)$tmp["id"]);

            $array_of_ids_products = $category->getProductsWs();
            $size_array_products = sizeof($array_of_ids_products);
            $count = 0;

            for($j = 0; $j < $size_array_products; $j++){
                $array_prod = $array_of_ids_products[$j];
                $product = new Product((int)$array_prod["id"]);
                if(!$product->active){
                    $count++;
                }
            }

            if($count == $size_array_products){
                $category->active = 0;
                $category->update();
            }else{
                $category->active = 1;
                $category->update();
            }
        }

    }

    /**
	* Controls if the prestashop entity names are equal or not.
	* 
	* @param string $actual_name
	* @param string new_name
	* @param integer $language
	* @return bool
	*
	*/
    private function isOldNameProductEgualToNewNameProduct($actual_name, $new_name, $language = 1){
        $old_name = $actual_name[$language];
        if(strtolower(trim($old_name)) === strtolower(trim($new_name))){
            return true;
        }else{
            return false;
        }
    }

    /**
	* Controls if the variables are equal or not.
	* 
	* @param string $old_value
	* @param string $new_value
	* @return bool
	*
	*/
    private function isOldOrNewValueForProduct($old_value, $new_value){
        if(gettype($old_value) === "string" && gettype($new_value) === "string"){
            if(strtolower(trim($old_value)) === strtolower(trim($new_value)))
                return true;
            else
                return false;
        }

        if(gettype($old_value) === "integer" && gettype($new_value) === "integer") {
            if($old_value == $new_value)
                return true;
            else
                return false;
        }

        if(gettype($old_value) === "double" && gettype($new_value) === "double") {
            if($old_value == $new_value)
                return true;
            else
                return false;
        }
    }

    /**
	* Updates a product on Prestashop.
	* 
	* @param array $product_attributes
	* @param integer $id_product
	* @param string $url_photo
	* @param array $triple_cod_col_siz
	* @param integer $language
	* @param array $array_combinations
	* @return array
	* @see $this->isOldNameProductEgualToNewNameProduct
	* @see $this->isOldOrNewValueForProduct
	* @see $this->controlCategoriesForActivateTheir 
	* @see $this->updateCombinantionsForPrestashop
	*
	*/
    public function updateProductForPrestashop($product_attributes = array(), $id_product, $url_photo, $triple_cod_col_siz, $array_combinations, $language = 1){
        $product = new Product($id_product);
        $is_change_product = false;

        $new_name_product = trim($product_attributes["Nome"]);
        if(!$this->isOldNameProductEgualToNewNameProduct($product->name, $new_name_product)){
            $product->name = $this->setArrayElementForLinkRewrite($new_name_product, true, $language);
            $product->meta_keywords = $new_name_product;
            $product->link_rewrite = $this->setArrayElementForLinkRewrite($new_name_product, false, $language);
            $is_change_product = true;
        }

        $new_price = (float)$product_attributes["Prezzo"];
        if(!$this->isOldOrNewValueForProduct((float)$product->price,$new_price)){
            $product->price = $new_price;
            $is_change_product = true;
        }

        $new_active = (int)$product_attributes["Attivo"];
        $control_category = $this->isOldOrNewValueForProduct((int)$product->active, $new_active);
        if(!$control_category){
            $product->active = $new_active;
            $is_change_product = true;
        }

        $new_minimal_quantity = (int)$product_attributes["Qta_min"];
        if(!$this->isOldOrNewValueForProduct((int)$product->minimal_quantity, $new_minimal_quantity)){
            $product->minimal_quantity = $new_minimal_quantity;
            $is_change_product = true;
        }

        $new_quantity = (int)$product_attributes["Qta"];
        if(!$this->isOldOrNewValueForProduct((int)$product->getQuantity($id_product), $new_quantity)){
            StockAvailable::setQuantity($id_product, 0, $new_quantity);
        }

        $array_old_features = $product->getFeatures();
        $array_features = $product_attributes["Feature"];

        $height = false;
        $width = false;

        foreach($array_old_features as $array_old_single_features){
            $feature = new FeatureCore((int)$array_old_single_features['id_feature']);
            $tmp_feature = $feature->name;
            $single_old_feature = $tmp_feature[$language];

            $feature_value = new FeatureValueCore((int)$array_old_single_features["id_feature_value"]);
            $tmp_feature_value = $feature_value->value;
            $single_old_feature_value = $tmp_feature_value[$language];

            if($this->isOldOrNewValueForProduct("Altezza",$single_old_feature)){
                if(!$this->isOldOrNewValueForProduct($single_old_feature_value,($array_features["Altezza"]." cm"))){
                    $feature_value->value = array($language => ($array_features["Altezza"]." cm"));
                    $feature_value->update();
                    $height = true;
                    $is_change_product = true;
                }						
            }
            if($this->isOldOrNewValueForProduct("Larghezza",$single_old_feature)){
                if(!$this->isOldOrNewValueForProduct($single_old_feature_value,($array_features["Larghezza"]." cm"))){
                    $feature_value->value = array($language => ($array_features["Larghezza"]." cm"));
                    $feature_value->update();
                    $width = true;
                    $is_change_product = true;
                }						
            }
            if($this->isOldOrNewValueForProduct("Lunghezza",$single_old_feature)){
                if(!$this->isOldOrNewValueForProduct($single_old_feature_value,($array_features["Lunghezza"]." cm"))){
                    $feature_value->value = array($language => ($array_features["Lunghezza"]." cm"));
                    $feature_value->update();
                    $is_change_product = true;
                }						
            }		
            if($this->isOldOrNewValueForProduct("Modello",$single_old_feature)){
                if(!$this->isOldOrNewValueForProduct($single_old_feature_value,$array_features["Modello"])){
                    $feature_value->value = array($language => $array_features["Modello"]);
                    $feature_value->update();
                    $is_change_product = true;
                }						
            }		
            if($this->isOldOrNewValueForProduct("Linea",$single_old_feature)){
                if(!$this->isOldOrNewValueForProduct($single_old_feature_value,$array_features["Linea"])){
                    $feature_value->value = array($language => $array_features["Linea"]);
                    $feature_value->update();
                    $is_change_product = true;
                }						
            }		
        }

        if($height){
            $product->height = (float)$array_features["Altezza"];
            $is_change_product = true;
        }
        if($width){
            $product->width = (float)$array_features["Larghezza"];
            $is_change_product = true;
        }

        if($is_change_product)
            $product->update();

        if(!$control_category){
            $ids_categories_array = $product->getWsCategories();
            $this->controlCategoriesForActivateTheir($ids_categories_array);
        }

        $change_new_image = $this->updateCombinantionsForPrestashop($id_product, $url_photo, $triple_cod_col_siz, $array_combinations, $language);
        
        
        $string_triple = array();

        $return = array();
        array_push($return, $product->id);
        
        $array_images_combinations_of_the_product = $product->getImages($language);

        $element = array();
        foreach($array_images_combinations_of_the_product as $array_combo_image){
            $name_of_the_image = $array_combo_image['legend'];
            $id_image_of_the_product = $array_combo_image['id_image'];
            array_push($element, $id_image_of_the_product.";".$name_of_the_image );
        }
        
        array_push($return, $element);
        
        foreach($change_new_image as $array_combo_image){
            $fetch_tmp = explode(";",$array_combo_image);
            $fetch_jpg = explode(".jpg", $fetch_tmp[1]);
            $fetch_image_name = explode(",", $fetch_jpg[0]);
            array_push($string_triple, $fetch_image_name[2]);
        }
        
        array_push($return, $change_new_image);
        
        array_push($return, $string_triple);
        return $return;
    }

    /**
	* Updates product combinations on Prestashop.
	* 
	* @param integer $id_product
	* @param string $url_photo
	* @param array $triple_cod_col_siz
	* @param array $array_combinations
	* @param integer $language
	* @return bool
	* @see $this->createAttributeGroups
	* @see $this->setArrayElementForLinkRewrite
	* @see $this->getCodeColor
	* @see PrestashopImageHandler getIdImageByName()
	* @see PrestashopImageHandler insertImageInPrestashop()
	*
	*/
    private function updateCombinantionsForPrestashop($id_product, $url_photo, $triple_cod_col_siz, $array_combinations, $language = 1){
        $product = new Product((int)$id_product);
        $id_new_images = array();

        $price = '0.000';//(float)$product->price;
        $reference = $product->reference;
        $id_supplier = (int)$product->id_supplier;

        foreach($triple_cod_col_siz as $triple){
            $array_attributes_and_values = $array_combinations[$triple];

            $attributes = $array_attributes_and_values["Attributi"];
            $values = $array_attributes_and_values["Valori"];
            $image = trim($array_attributes_and_values["Immagine"]);
            $quantity = (int)$array_attributes_and_values["Qta"];

            $variable_tmp_attributes = explode(",",$attributes);
            $variable_tmp_values = explode(",",$values);

            $id_attributes_for_combinations = array();
            $flag_just_exist_color = 1;
            $flag_just_exist_size = 1;

            for($i = 0; $i < sizeof($variable_tmp_attributes); $i++){
                $code = "";
                if($variable_tmp_values[$i] != ""){
                    $id_attribute_group = $this->createAttributeGroups($variable_tmp_attributes[$i], $language);
					$id_attributes_for_not_repeat = $this->getAttributeColorAndSize($variable_tmp_values[$i], $language);

                    if(strtolower($variable_tmp_attributes[$i]) === "colore" || strtolower($variable_tmp_attributes[$i]) === "colori"){
                        $product_attribute_for_not_reply = $product->getAttributeCombinations($language);
                        if(empty($product_attribute_for_not_reply)){
							if($id_attributes_for_not_repeat === '-1'){
								$code = $this->getCodeColor(strtolower($variable_tmp_values[$i]));
								if($code != ""){
									$code = "#".$code;
									$attribute_for_product = new Attribute();
									$attribute_for_product->name = $this->setArrayElementForLinkRewrite($variable_tmp_values[$i], true, $language);
									$attribute_for_product->color = $code;
									$attribute_for_product->id_attribute_group = $id_attribute_group;
									$attribute_for_product->add();
									array_push($id_attributes_for_combinations, $attribute_for_product->id);
								}
							}else{
								array_push($id_attributes_for_combinations, (int)$id_attributes_for_not_repeat);
							}
                        }else{
                            $flag = 1;
                            foreach($product_attribute_for_not_reply as $more_attributes){
                                if($more_attributes['attribute_name'] === $variable_tmp_values[$i]){
                                    array_push($id_attributes_for_combinations, (int)$more_attributes['id_attribute']);
                                    $flag = 0;
                                    $flag_just_exist_color = 0;
                                    break;
                                }
                            }
                            if($flag){
								if($id_attributes_for_not_repeat === '-1'){
									$code = $this->getCodeColor(strtolower($variable_tmp_values[$i]));
									if($code != ""){
										$code = "#".$code;
										$attribute_for_product = new Attribute();
										$attribute_for_product->name = $this->setArrayElementForLinkRewrite($variable_tmp_values[$i], true, $language);
										$attribute_for_product->color = $code;
										$attribute_for_product->id_attribute_group = $id_attribute_group;
										$attribute_for_product->add();
										array_push($id_attributes_for_combinations, $attribute_for_product->id);
									}
								}else{
									array_push($id_attributes_for_combinations, (int)$id_attributes_for_not_repeat);
								}
                            }
                        }
                    }

                    if(strtolower($variable_tmp_attributes[$i]) === "taglia" || strtolower($variable_tmp_attributes[$i]) === "taglie"){
                        $product_attribute_for_not_reply = $product->getAttributeCombinations($language);
                        if(empty($product_attribute_for_not_reply)){
							if($id_attributes_for_not_repeat === '-1'){
								$attribute_for_product = new Attribute();
								$attribute_for_product->name = $this->setArrayElementForLinkRewrite($variable_tmp_values[$i], true, $language);
								$attribute_for_product->id_attribute_group = $id_attribute_group;
								$attribute_for_product->add();
								array_push($id_attributes_for_combinations, $attribute_for_product->id);
							}else{
								array_push($id_attributes_for_combinations, (int)$id_attributes_for_not_repeat);
							}
                        }else{
                            $flag = 1;
                            foreach($product_attribute_for_not_reply as $more_attributes){
                                $vrbls_1 = trim($more_attributes['attribute_name']);
                                $vrbls_2 = trim($variable_tmp_values[$i]);
                                if(gettype($vrbls_1) === "string" && gettype($vrbls_2) === "string"){
                                    if($vrbls_1 === $vrbls_2){
                                        array_push($id_attributes_for_combinations, (int)$more_attributes['id_attribute']);
                                        $flag = 0;
                                        $flag_just_exist_size = 0;
                                        break;
                                    }
                                }
                                if(gettype($vrbls_1) === "integer" && gettype($vrbls_2) === "integer"){
                                    if($vrbls_1 == $vrbls_2){
                                        array_push($id_attributes_for_combinations, (int)$more_attributes['id_attribute']);
                                        $flag = 0;
                                        $flag_just_exist_size = 0;
                                        break;
                                    }
                                }

                            }
                            if($flag){
								if($id_attributes_for_not_repeat === '-1'){
									$attribute_for_product = new Attribute();
									$attribute_for_product->name = $this->setArrayElementForLinkRewrite($variable_tmp_values[$i], true, $language);
									$attribute_for_product->id_attribute_group = $id_attribute_group;
									$attribute_for_product->add();
									array_push($id_attributes_for_combinations, $attribute_for_product->id);
								}else{
									array_push($id_attributes_for_combinations, (int)$id_attributes_for_not_repeat);
								}
                            }
                        }
                    }
                }else{
                    if(strtolower($variable_tmp_attributes[$i]) === "colore" || strtolower($variable_tmp_attributes[$i]) === "colori"){
                        $flag_just_exist_color = 0;
                    }
                    if(strtolower($variable_tmp_attributes[$i]) === "taglia" || strtolower($variable_tmp_attributes[$i]) === "taglie"){
                        $flag_just_exist_size = 0;
                    }
                }

            }
            
            if($flag_just_exist_color || $flag_just_exist_size){
                $id_images = array();
                $tmp_photo = explode(".jpg,",$image);
                for($i = 0; $i < sizeof($tmp_photo); $i++){
                    if(!empty($tmp_photo[$i])){
                        $image_for_prestashop = new PrestashopImageHandler();
                        $id_image = $image_for_prestashop->getIdImageByName(trim($tmp_photo[$i]));
                        if(!empty($id_image))
                            array_push($id_images, $id_image);
                        else{
                            $id_image = $image_for_prestashop->insertImageInPrestashop($id_product, trim($url_photo), trim($tmp_photo[$i]));
                            array_push($id_images, $id_image);
                            array_push($id_new_images,$id_image.";".trim($tmp_photo[$i]).'.jpg');
                        }
                    }
                }

                $id_product_attributes = $product->addProductAttribute($price, 0, 0, 0, $quantity, "", $reference, $id_supplier, 0, 1);

                $combinations = new CombinationCore((int)$id_product_attributes);

                $combinations->setAttributes($id_attributes_for_combinations);
                $combinations->setImages($id_images);
            }else{
                $id_images = array();
                $tmp_photo = explode(".jpg,",$image);
                for($i = 0; $i < sizeof($tmp_photo); $i++){
                    if(!empty($tmp_photo[$i])){
                        $image_for_prestashop = new PrestashopImageHandler();
                        $id_image = $image_for_prestashop->getIdImageByName(trim($tmp_photo[$i]));
                        if(empty($id_image)){
                            $id_image = $image_for_prestashop->insertImageInPrestashop($id_product, trim($url_photo), trim($tmp_photo[$i]));
                            array_push($id_images, $id_image);
                            array_push($id_new_images,$id_image.";".trim($tmp_photo[$i]).".jpg");
                        }
                    }
                }		

                if(!empty($id_images)){

                    $array_product_attribute_combinations_get = $product->getCombinationImages($language);
                    for($i = 0; $i < sizeof($id_images); $i++){
                        $id_della_immagine = $id_images[$i];

                        foreach($array_product_attribute_combinations_get as $single_array_of_attribute_product){
                            foreach($single_array_of_attribute_product as $array_di_combinazioni){
                                $id_arr_comb = $array_di_combinazioni['id_product_attribute'];

                                $combinazioni_attributi = new CombinationCore((int)$id_arr_comb);
                                $attribute_combinations = $combinazioni_attributi->getAttributesName($language);

                                $attr_comb1 = $attribute_combinations[0];
                                $attr_comb2 = $attribute_combinations[1];
                                $id_attr_comb1 = $attr_comb1['id_attribute'];
                                $id_attr_comb2 = $attr_comb2['id_attribute'];

                                $id_attributi_input = $id_attributes_for_combinations;
                                $id_color_or_size = $id_attributi_input[0];
                                $id_size_or_color = $id_attributi_input[1];

                                if($id_color_or_size == $id_attr_comb1 || $id_color_or_size == $id_attr_comb2){
                                    if($id_size_or_color == $id_attr_comb1 || $id_size_or_color == $id_attr_comb2){
                                        $new_image = array();
                                        $new_array_image = $combinazioni_attributi->getWsImages();
                                        for($j = 0; $j < sizeof($new_array_image); $j++){
                                            $tmp_image_ = $new_array_image[$j];
                                            array_push($new_image,(int)$tmp_image_['id']);
                                        }
                                        array_push($new_image,$id_della_immagine);
                                        $fff = $product->updateProductAttribute($id_arr_comb, 0, 
                                                                                $price, 0, 0, 0, $new_image, 
                                                                                $reference, $id_supplier, 0, 1, null, null, 1, 
                                                                                '0000-00-00');
                                    }
                                }

                            }
                        }

                    }
                }

            }
        }	
        
        return $id_new_images;
    }

}

ini_set('max_execution_time', 3600);

?>