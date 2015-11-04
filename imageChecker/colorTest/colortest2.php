<?php

require_once('ImageChecker.php');

$modifiedImageChecker = new ImageChecker();

$modifiedImageChecker->areImagesDifferent( "./test1.jpg", "./test2.jpg", 40, 1);


?>