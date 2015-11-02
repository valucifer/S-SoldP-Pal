<?php

include('ColorChecker');

$colChecker = new ColorChecker;
$results = null;

if( $colorChecker->initializeParameters("./testimage.jpg", 40, 1) ){
	echo "ColorChecker is initialized correctly...let's continue!<br/>";
	$results = $colorChecker->startColorAnalysisAndReturnResultsAsText();
	$pieces = explode(";", $results);
	print_r($pieces);
}

?>