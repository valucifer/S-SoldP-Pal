<?php
require_once('ImageComparison.php');

$imgComparison = new ImageComparison();

echo "Testing first 2 images; expected true...<br/>";
$imgComparison->initialize('test1.jpg','test2.jpg');
$results = $imgComparison->compareImages();
echo "...result is ".$results." <BR/>";

echo "Testing other 2 images; expected false...<br/>";
$imgComparison->initialize('test2.jpg','test3.jpg');
$results = $imgComparison->compareImages();
echo "...result is ".$results." <BR/>";

?>