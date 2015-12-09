<?php

require_once("colors.inc.php");
require_once("Logger.php");
require_once("HandleOperationsException.php");

/**
 * This class handles colors form an image and their presence ratio.
 * This class is based on colorextractor.php library
 * @see http://www.phpclasses.org/package/3370-PHP-Extracts-the-most-common-colors-used-in-images.html
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */
class ColorChecker{
    public $image = null;
    public $nColors = 0;
    public $delta = 0;
    public $colorChecker = null;

    /**
	 * Initializes class and allocate needed variables.
	 *
	 * @params 
	 * @return 
	 */
    public function __construct(){
        $this->colorChecker = new GetMostCommonColors();
    }

    /**
	 * Sets the image path that represents a valid path to image file
	 * @exception HandleOperationsException thrown when parameter is not a string or when it does not represent a valid path.
	 * @params $img a string that represents image path
	 * @return 
	 */
    public function setImage($img){
        if( !is_string( $img ) )
            throw new HandleOperationsException("Parameter you've passed is not a string! Also ensure it represents a valid path to image file.", "ERROR");

        if( !file_exists ( $img ) )
            throw new HandleOperationsException("Parameter you've passed is not a valid path!", "ERROR");

        $this->image = $img;
    }

    /**
	 * Gets the image path that represents a valid path to image file
	 *
	 * @params
	 * @return $image string representation of image path.
	 */
    public function getImage(){
        return $this->image;
    }

    /**
	 * Sets the number of colors that will be analyzed
	 * @exception HandleOperationsException thrown when parameter is not an integer.
	 * @params $numcolors an integer representation of number of colors used to analyze image.
	 * @return 
	 */
    public function setNumberOfColors($numcolors){
        if(!is_numeric ( $numcolors ))
            throw new HandleOperationsException("Parameter you've passed is not a number!", "ERROR");

        $this->nColors = $numcolors;
    }

    /**
	 * Gets the number of colors that will be analyzed
	 *
	 * @params 
	 * @return $nColors an integer representation of number of colors used to analyze image.
	 */
    public function getNumberOfColors(){
        return $this->nColors;
    }

    /**
	 * Sets the delta value of the image that will be analyzed
	 * @exception HandleOperationsException thrown when parameter is not an integer or when it does not belong to range [1-255].
	 * @params $dlt an integer representation in range [1-255] that represents the accuracy of color analysis.
	 * @return 
	 */
    public function setDeltaValue($dlt){
        if(!is_numeric ( $dlt ))
            throw new HandleOperationsException("Parameter you've passed is not a number!", "ERROR");
        $this->delta = $dlt;
    }

    /**
	 * Gets the delta value of the image that will be analyzed
	 *
	 * @params 
	 * @return $delta an integer representation that represents the accuracy of color analysis
	 */
    public function getDeltaValue(){
        return $this->delta;
    }

    /**
	 * Initializes class parameters to create a well-formed object to analyze colors in just one step.
	 *
	 * @params string $image, int $nColors, int $delta
	 * @return true if setting operations are ok, false otherwise
	 */
    public function initializeParameters($img, $nClrs, $dlt){
        $this->setImage($img);
        $this->setNumberOfColors($nClrs);
        $this->setDeltaValue($dlt);
    }

    /**
	 * Starts color analysis on a specified image.
	 * @exception HandleOperationsException thrown when parameters needed to analyze the image are not correctly set.
	 * @params
	 * @return $colors an array representation in "color percentage;" format that represents colors in specified image and their percentual presence.
	 */
    public function startColorAnalysis(){
        if( $this->image == null )
            throw new HandleOperationsException("Please, set image file! You can call initializeParameters() method to set the environment to colors analysis.", "ERROR");

        if( $this->nColors == 0 )
            throw new HandleOperationsException("You have set 0 as number of colors. How can I analyze 0 colors? :P Please set a number greater than 0. You can call initializeParameters() method to set the environment to colors analysis.", "ERROR");

        if( $this->delta == 0 )
            throw new HandleOperationsException( "Delta value is 0. Please set a number between 1 and 255.  You can call initializeParameters() method to set the environment to colors analysis.", "ERROR");

        $colors = $this->colorChecker->Get_Color($this->image, $this->nColors, false, false, $this->delta);

        return $colors;
    }

    /**
	 * Starts color analysis on specified image, within a number of colors and with a specified accuracy. This method is similar to startColorAnalysis() but returns a string representation of results.
	 *
	 * @params
	 * @return $toReturn a string representation of color analysis.
	 */
    public function startColorAnalysisAndReturnResultsAsText(){

        $colors = $this->startColorAnalysis();

        $toReturn = null;

        foreach ( $colors as $hex => $count ){
            if ( $count > 0 ){
                $toReturn .= $hex." ".$count.";";
            }
        }
        return $toReturn;

    }

}

?>