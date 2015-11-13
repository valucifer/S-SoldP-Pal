<?php
include "UpdateTmpTables.php";
$update = new UpdateTmpTables();
$update-> updateImageField ('test2.jpg' , 1, 1);
echo"</br> ************************************** </br>";
$update-> insertImageField ('test3.jpg' , 1, 3);
echo"</br> ************************************** </br>";
$update-> updateImageField ('test1.jpg' , 1, 2);


?>