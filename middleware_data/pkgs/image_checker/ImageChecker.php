<?php

/**
 * This class checks if two images are equals or not.
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */

require_once("ColorChecker.php");
require_once("HandleOperationsException.php");

class ImageChecker{
    
    private $colorChecker = null;
    var $start = null;

    /**
	 * Initializes class and allocate needed variables.
	 *
	 * @params 
	 * @return 
	 */
    public function __construct(){
        $this->colorChecker = new ColorChecker();
    }
    
    /**
	 * Analyzes an image from an existing path and returns a textual representation of colors
	 *
	 * @params $colorAnalysisImage1, $colorsNumber, $delta
	 * @return $imageAnalysisResults a string representation of color analysis
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
	 * @params $colorAnalysisImage1, $pathImage2, $colorsNumber, $delta
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
	 * Compares two images through color analysis. This method checks first if md5 digest on images is the same; if digests are the same, then images are surely equals. If not, it proceeds analyzing color images.
	 * @exception HandleOperationsException thrown when parameters does not represent a valid path.
	 * @params $firstImagePath, $secondImagePath, $colorsNumber, $delta
	 * @return true if images are different, false otherwise
	 */
    public function areImagesDifferent( $firstImagePath, $secondImagePath, $colorsNumber, $delta ){
        //$this->start = microtime(true);
        $firstImageAnalysisResults = null;
        $secondImageAnalysisResults = null;
        
        if( !file_exists($firstImagePath) ){
            throw new HandleOperationsException("No valid path for first argument!", "ERROR");
            return false;
        }
        
        if( !file_exists($secondImagePath) ){
            throw new HandleOperationsException("No valid path for second argument!", "ERROR");
            return false;
        }

        if( !strcmp( md5_file($firstImagePath), md5_file($secondImagePath) ) ){
            //digests are equals, so there aren't any differences
            return false;
        }

        $this->colorChecker->initializeParameters( $firstImagePath, $colorsNumber, $delta );
        $firstImageAnalysisResults = $this->colorChecker->startColorAnalysisAndReturnResultsAsText();
        $this->colorChecker->initializeParameters( $secondImagePath, $colorsNumber, $delta );
        $secondImageAnalysisResults = $this->colorChecker->startColorAnalysisAndReturnResultsAsText();

        if( !strcmp($firstImageAnalysisResults,$secondImageAnalysisResults) ){
            return false;
        }else return true;

    }
    
    function __destruct() {
        //echo "Completed in ", microtime(true) - $this->start, " Seconds<br/>";
    }

}

?>