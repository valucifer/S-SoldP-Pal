<?php

/**
 * This class checks if two images are equals or not. Image 1 needs to be a color analysis text, image 2 needs to be a valid path to image file.
 * This class is based on colorextractor.php library
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */
 
 class ModifiedImageChecker(){
 
 	include_once("ColorChecker.php");
 	var $colorChecker = null;
 	
 	public function __constructor(){
 		$colorChecker = new ColorChecker;
 		echo 'The class "', __CLASS__, '" is initiated!\n';
 	}
 	
 	/**
	 * Compares two images' color analysis
	 *
	 * @params string $colorAnalysisImage1, string $pathImage2, int $colorsNumber, int $delta
	 * @return true if images are equals, false otherwise
	 */
 	public function isImageChanged( $colorAnalysisImage1, $pathImage2, $colorsNumber, $delta ){
 		var $imageAnalysisResults = null;
 		if( $colorChecker->initializeParameters( $pathImage2, $colorsNumber, $delta ) ){
 			$imageAnalysisResults = $colorChecker->startColorAnalysisAndReturnResultsAsText();
 			if( !strcmp($imageAnalysisResults, $colorAnalysisImage1) )
				return false;
			else return true;
 		}else echo "...for that reason, I can't understand if your images are equals.\n";
 	}

 }

?>