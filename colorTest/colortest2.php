<?php



require_once('ColorChecker.php');

$colChecker = new ColorChecker();
$results = null;
$results2 = null;

if( $colChecker->initializeParameters("./img1.png", 40, 1) ){
	echo "ColorChecker is initialized correctly...let's continue!<br/>";
	$results = $colChecker->startColorAnalysisAndReturnResultsAsText();
}

if( $colChecker->initializeParameters("./img2.png", 40, 1) ){
	echo "ColorChecker is initialized correctly...let's continue!<br/>";
	$results2 = $colChecker->startColorAnalysisAndReturnResultsAsText();
}

if( !strcmp($results, $results2) ){
    echo "images are equals!";
}else echo "images are different!";


?>