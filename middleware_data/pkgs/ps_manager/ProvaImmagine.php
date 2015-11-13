<?php
include "../config/config.inc.php";
include "../init.php";

	//Create product
   /* $product = new Product(); 
    $product->ean13 = 12456;
	$product->name = array((int)Configuration::get('PS_LANG_DEFAULT') =>  'test');
    $product->link_rewrite = array((int)Configuration::get('PS_LANG_DEFAULT') => 'test');
    $product->id_category = 3;
    $product->id_category_default = 3;
    $product->redirect_type = '404';
    $product->price = 33;
    $product->wholesale_price = 25;
    $product->minimal_quantity = 1;
    $product->show_price = 1;
    $product->on_sale = 0;
    $product->online_only = 1;
    $product->meta_keywords = 'test';
    $product->id_tax_rules_group = 0;

    $product->add();
    $product->addToCategories(array(3));
    StockAvailable::setQuantity($product->id,'',10);
	*/
	//$imageManager = new ImageManager();
	
	//Add  main product image
    //$id_product = $product->id;
    $id_product = 61;
	echo "ID: $id_product";
    $url = 'C:\\Users\\Valentino\\Desktop\\KPBS03S05_2_001.jpg';
    $shops = Shop::getShops(true, null, true);    
    $image = new Image();
    $image->id_product = $id_product;
    //$image->position = Image::getHighestPosition($id_product) + 1;
    $image->cover = true; // or false;
    if (($image->validateFields(false, true)) === true &&
                    ($image->validateFieldsLang(false, true)) === true && $image->update())
                    {
                        $image->associateTo($shops);
                        //if (! copyImg($id_productt, $image->id, $url, 'products')
							if (! copyImg($id_product, 129, $url, 'products'))
                            {
                            $image->delete();
                        }
                    }
	
	
	  function copyImg($id_entity, $id_image = null, $url, $entity = 'products')
{
    $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
    $watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

    switch ($entity)
    {
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
    if (@copy($url, $tmpfile))
    {
        ImageManager::resize($tmpfile, $path.'.jpg');
        $images_types = ImageType::getImagesTypes($entity);
        foreach ($images_types as $image_type)
            ImageManager::resize($tmpfile, $path.'-'.stripslashes($image_type['name']).'.jpg', $image_type['width'],
             $image_type['height']);

        if (in_array($image_type['id_image_type'], $watermark_types))
            Hook::exec('actionWatermark', array('id_image' => $id_image, 'id_product' => $id_entity));
    }
    else
    {
        unlink($tmpfile);
        return false;
    }
    unlink($tmpfile);
    return true;
}
			
			
			
			
			
			
			
			
			
			
	
	
?>