<?php

require_once('ImageChecker.php');

$modifiedImageChecker = new ImageChecker();

$res = $modifiedImageChecker->areImagesDifferent( "./test1.jpg", 40, 1, "A055CF22BDA7974856C76FF0700C9D0C","");

var_dump($res);

?>