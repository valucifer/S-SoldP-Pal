<?php

/**
 * A class gets colors form image and their presence ratio
 * This class is based on colorextractor.php library
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */

require_once("colors.inc.php");

 class ColorChecker{
 	public $image = null;
 	public $nColors = 0;
 	public $delta = 0;
    public $colorChecker = null;
     
 	public function __construct(){
        $this->colorChecker = new GetMostCommonColors();
 		echo 'The class "'. __CLASS__. '" is initiated!<br />';
 	}
 	
 	/**
	 * Sets the image path that represents a valid path to image file
	 *
	 * @params string $img 
	 * @return 
	 */
 	public function setImage($img){
 		if( !is_string( $img ) )
 			throw new Exception("Parameter you've passed is not a string! Also ensure it represents a valid path to image file.");
            
 		if( !file_exists ( $img ) )
 			throw new Exception("Parameter you've passed is not a valid path!");
 			
 		$this->image = $img;
 	}
 	
 	/**
	 * Gets the image path that represents a valid path to image file
	 *
	 * @params
	 * @return string $image
	 */
 	public function getImage(){
 		return $this->image;
 	}
 	
 	/**
	 * Sets the number of colors that will be analyzed
	 *
	 * @params int $numcolors
	 * @return 
	 */
 	public function setNumberOfColors($numcolors){
 		if(!is_numeric ( $numcolors ))
 			throw new Exception("Parameter you've passed is not a number!");
 			
 		$this->nColors = $numcolors;
 	}
 	
 	/**
	 * Gets the number of colors that will be analyzed
	 *
	 * @params 
	 * @return $nColors
	 */
 	public function getNumberOfColors(){
 		return $this->nColors;
 	}
 	
 	/**
	 * Sets the delta value of the image that will be analyzed
	 *
	 * @params int $dlt
	 * @return 
	 */
 	public function setDeltaValue($dlt){
 		if(!is_numeric ( $dlt ))
 			throw new Exception("Parameter you've passed is not a number!");

 		$this->delta = $dlt;
 	}
 	
 	/**
	 * Gets the delta value of the image that will be analyzed
	 *
	 * @params 
	 * @return int $delta
	 */
 	public function getDeltaValue(){
 		return $this->delta;
 	}
 	
 	/**
	 * Initialize class parameters to create a well-formed object to analyze colors in just one step
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
	 * Starts color analysis on specified image. This method returns an array representation in "color":"percentage" format
	 *
	 * @params
	 * @return $colors 
	 */
 	public function startColorAnalysis(){
 		if( $this->image == null )
 			throw new Exception("Please, set image file! You can call initializeParameters() method to set the environment to colors analysis.");
        
 		if( $this->nColors == 0 )
 			throw new Exception("You have set 0 as number of colors. How can I analyze 0 colors? :P Please set a number greater than 0. You can call initializeParameters() method to set the environment to colors analysis.");
        
 		if( $this->delta == 0 )
 			throw new Exception( "Delta value is 0. Please set a number between 1 and 255.  You can call initializeParameters() method to set the environment to colors analysis.");
 		
 		$colors = $this->colorChecker->Get_Color($this->image, $this->nColors, false, false, $this->delta);
 		
 		return $colors;
 	}
 	
 	/**
	 * Starts color analysis on specified image. This method is similar to initializeParameters() but returns a string representation of results.
	 *
	 * @params
	 * @return string $toReturn
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