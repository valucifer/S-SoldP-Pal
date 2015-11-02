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
 		if( !is_string( $img ) ){
 			echo "Parameter you've passed is not a string! Also ensure it represents a valid path to image file.\n";
 			return;
 		}
 		if( !file_exists ( $img ) ){
 			echo "Parameter you've passed is not a valid path!\n";
 			return;
 		}
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
 		if(!is_numeric ( $numcolors )){
 			echo "Parameter you've passed is not a number!\n";
 			return;
 		}
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
 		if(!is_numeric ( $dlt )){
 			echo "Parameter you've passed is not a number!\n";
 			return;
 		}
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
 		
 		if( $this->getImage() != null && $this->getNumberOfColors() != 0 && $this->getDeltaValue() != 0 )
 			return true;
 		else return false;
 	}
 	
 	/**
	 * Starts color analysis on specified image. This method returns an array representation in "color":"percentage" format
	 *
	 * @params
	 * @return $colors 
	 */
 	public function startColorAnalysis(){
 		if( $this->image == null ){
 			echo "Please, set image file!\n";
 			return;
 		}
 		if( $this->nColors == 0 ){
 			echo "You have set 0 as number of colors. How can I analyze 0 colors? :P Please set a number greater than 0.\n";
 			return;
 		}
 		if( $this->delta == 0 ){
 			echo "Delta value is 0. Please set a number between 1 and 255.\n";
 			return;
 		}
 		
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