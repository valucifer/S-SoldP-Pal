<?php

/**
 * This class checks if two images are equals or not. Image 1 needs to be a color analysis text, image 2 needs to be a valid path to image file.
 * This class is based on colorextractor.php library
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */

require_once("ColorChecker.php");
class ModifiedImageChecker{
    
    private $colorChecker = null;
    var $start = null;

    public function __construct(){
        $this->colorChecker = new ColorChecker();
        echo 'The class "', __CLASS__, '" is initiated!<BR/>';
    }

    /**
	 * Compares two images' color analysis. First parameter in this function is a text that represent a color analysis executed previously
	 *
	 * @params string $colorAnalysisImage1, string $pathImage2, int $colorsNumber, int $delta
	 * @return true if images are equals, false otherwise
	 */
    private function checkImageDiff( $colorAnalysisImage1, $pathImage2, $colorsNumber, $delta ){
        $imageAnalysisResults = null;
        if( $colorChecker->initializeParameters( $pathImage2, $colorsNumber, $delta ) ){
            echo "First of all, let's check MD5 diget on image...";
            $imageAnalysisResults = $colorChecker->startColorAnalysisAndReturnResultsAsText();
            if( !strcmp($imageAnalysisResults, $colorAnalysisImage1) )
                return false;
            else return true;
        }else echo "...for that reason, I can't understand if your images are equals.<br/>";
    }

    /**
	 * Compares two images through color analysis
	 *
	 * @params string $firstImagePath, string $secondImagePath, int $colorsNumber, int $delta
	 * @return true if images are different, false otherwise
	 */
    public function areImagesDifferent( $firstImagePath, $secondImagePath, $colorsNumber, $delta ){
        $this->start = microtime(true);
        $firstImageAnalysisResults = null;
        $secondImageAnalysisResults = null;

        echo "I'm calculating MD5 digest to check if files are not the same...<br/>";
        
        if( !file_exists($firstImagePath) ){
            throw new Exception("No valid path for first argument!");
            return false;
        }
        
        if( !file_exists($secondImagePath) ){
            throw new Exception("No valid path for second argument!");
            return false;
        }

        if( !strcmp( md5_file($firstImagePath), md5_file($secondImagePath) ) ){
            //digests are equals, so there aren't any differences
            return false;
        }

        echo "Images seems to be different, let's take a check on their colors...<br/>";

        $this->colorChecker->initializeParameters( $firstImagePath, $colorsNumber, $delta );
        $firstImageAnalysisResults = $this->colorChecker->startColorAnalysisAndReturnResultsAsText();
        $this->colorChecker->initializeParameters( $secondImagePath, $colorsNumber, $delta );
        $secondImageAnalysisResults = $this->colorChecker->startColorAnalysisAndReturnResultsAsText();

        if( !strcmp($firstImageAnalysisResults,$secondImageAnalysisResults) ){
            return false;
        }else return true;

    }
    
    function __destruct() {
        echo "Completed in ", microtime(true) - $this->start, " Seconds<br/>";
    }

}

?>