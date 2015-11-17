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
//set_include_path(get_include_path() . PATH_SEPARATOR .__DIR__. '/files');



require ("PrestashopUpdate.php");


require ("UpdateTmpTables.php");


$update = new UpdateTmpTables();
$update-> updateImageField ('test2.jpg' , 1, 1);
echo"</br> ************************************** </br>";
$update-> insertImageField ('test3.jpg' , 1, 3);
echo"</br> ************************************** </br>";
$update-> updateImageField ('test1.jpg' , 1, 2);

/**

echo "inizio aggiornamento </br>";
$updatePS = new PrestashopUpdate('./files/A20151008161213_SEM.chk');
$updatePS->updatePsProduct();
echo "aggiornamento completato </br>";
**(
?>