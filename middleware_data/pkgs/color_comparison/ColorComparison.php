<?php
require("../../libs/color_lib/colors.inc.php");

/**
 * This class recognizes three main colors on image, except for background color.
 * This class is based on colorextractor.php library
 * @see http://www.phpclasses.org/package/3370-PHP-Extracts-the-most-common-colors-used-in-images.html
 *
 *
 * @author Nello Saulino <nello.saulino@gmail.com>
 */
class ColorComparison{

    private $colorChecker = null;

    public function __construct(){
        $this->colorChecker = new GetMostCommonColors();
    }

    /*
    * Returns first three colors most present on image except for white (image background).
    * @params $image_path image path
    */
    public function getColorsTable($image_path, $debug = false){
        $colors = $this->colorChecker->Get_Color($image_path, 10, false, false, 30);
        echo "<br/><br/>";
        $top_three_colors_percentage = array();
        $index = 0;
        foreach ( $colors as $hex => $count ){
            if( $index == 3 )
                break;
            if ( $hex !== "f0f0f0" ){
                //it's not white, so we proceed.
                $top_three_colors_percentage[$index] = $hex;
                if(debug)
                    echo "<div style='width:50px;height:50px;border:1px; background-color:#".$hex.";'></div><br/>";
                $index ++;
            }
        }
        return array($top_three_colors_percentage[0], $top_three_colors_percentage[1], $top_three_colors_percentage[2]);
    }

}

?>