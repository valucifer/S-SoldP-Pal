<?php

require_once('ColorChecker.php');

$colChecker = new ColorChecker();
$results = null;

if( $colChecker->initializeParameters("./testimage.jpg", 40, 1) ){
	echo "ColorChecker is initialized correctly...let's continue!<br/>";
	$results = $colChecker->startColorAnalysisAndReturnResultsAsText();
	$pieces = explode(";", $results);
	foreach($pieces as $value){
        echo $value."<br/>";
    }
}

?>