<?php

require_once('ModifiedImageChecker.php');

$modifiedImageChecker = new ModifiedImageChecker();

$modifiedImageChecker->areImagesDifferent( "./test1.jpg", "./test2.jpg", 40, 1);


?>