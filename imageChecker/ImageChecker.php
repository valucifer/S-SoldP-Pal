<?php

/**
 * This class checks if two images are equals or not. Image 1 needs to be a color analysis text, image 2 needs to be a valid path to image file.
 * This class is based on colorextractor.php library
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */

require_once("ColorChecker.php");
class ImageChecker{
    
    private $colorChecker = null;
    var $start = null;

    public function __construct(){
        $this->colorChecker = new ColorChecker();
        echo 'The class "', __CLASS__, '" is initiated!<BR/>';
    }
    
    /**
	 * This function analyzes an image from path and returns a textual representation of colors
	 *
	 * @params string $colorAnalysisImage1, string $pathImage2, int $colorsNumber, int $delta
	 * @return string $imageAnalysisResults
	 */
    public function analyzeImagesColors($imagePathToAnalyze, $colorsNumber, $delta){
    	$imageAnalysisResults = null;
		$colorChecker->initializeParameters( $imagePathToAnalyze, $colorsNumber, $delta );
    	$imageAnalysisResults = $this->colorChecker->startColorAnalysisAndReturnResultsAsText();
    	return $imageAnalysisResults;
    }

    /**
	 * Compares two images' color analysis. First parameter in this function is a text that represents a color analysis executed previously
	 *
	 * @params string $colorAnalysisImage1, string $pathImage2, int $colorsNumber, int $delta
	 * @return true if images are equals, false otherwise
	 */
    public function checkImageDiff( $colorAnalysisImage1, $pathImage2, $colorsNumber, $delta ){
        $imageAnalysisResults = null;
        $colorChecker->initializeParameters( $pathImage2, $colorsNumber, $delta );
        $imageAnalysisResults = $colorChecker->startColorAnalysisAndReturnResultsAsText();
        if( !strcmp($imageAnalysisResults, $colorAnalysisImage1) )
            return false;
        else return true;
    }

    /**
	 * Compares image information still existing to a new image color analysis
	 *
	 * @params string $imagePath, int $colorsNumber, int $delta, string $imageAnalysisResults, string $firstImageColorAnalysis
	 * @return "false" if images are the same, array containing new image informations otherwise
	 */
    public function areImagesDifferent( $imagePath, $colorsNumber, $delta, $firstImageMD5digest, $firstImageColorAnalysis ){
        $this->start = microtime(true);
        $firstImageAnalysisResults = null;
        $secondImageAnalysisResults = null;
        $imageMD5digest = 0;
        
        if( !is_string( $imagePath ) )
 			throw new Exception("Parameter imagePath you've passed is not a string!");
        
        if( !file_exists($imagePath) ){
            throw new Exception("No valid path for first argument!");
            return false;
        }
        
        if(!is_numeric ( $colorsNumber ))
 			throw new Exception("Parameter colorNumber you've passed is not a number!");
 			
 		if(!is_numeric ( $delta ))
 			throw new Exception("Parameter delta you've passed is not a number!");

 		if( !is_string( $firstImageMD5digest ) )
 			throw new Exception("Parameter digest you've passed is not a string!");	

 		if( !is_string( $firstImageColorAnalysis ) )
 			throw new Exception("Parameter colorAnalysis you've passed is not a string!");	
        
        $imageMD5digest = md5_file($imagePath);
        
        if( !strcmp( $firstImageMD5digest, $imageMD5digest ) ){
            //digests are equals, so there aren't any differences
            echo "Images have the same digest, so they are equals!<br/>";
            return "false";
        }

        echo "Images seems to be different, let's take a check on their colors...<br/>";

        $this->colorChecker->initializeParameters( $imagePath, $colorsNumber, $delta );
        $imageAnalysisResults = $this->colorChecker->startColorAnalysisAndReturnResultsAsText();
        
        if( !strcmp($firstImageColorAnalysis, $imageAnalysisResults) ){
        		return "false";
        }else{
        		return array($imageMD5digest,$imageAnalysisResults);
        }
    }
    
    function __destruct() {
        echo "Completed in ", microtime(true) - $this->start, " Seconds<br/>";
    }

}

?>