<?php

class ImageComparison{

    public $firstImagePath = null;
    public $secondImagePath = null;
    private $start = null;

    public function __construct(){
        $this->start = microtime(true);
        echo "Class ".__CLASS__." is ready!<BR/>";
    }

    public function initialize($firstPath, $secondPath){
        if( !is_string( $firstPath ) )
            throw new Exception("First parameter you've passed is not a string! Also ensure it represents a valid path to image file.");

        if( !file_exists ( $firstPath ) )
            throw new Exception("First parameter you've passed is not a valid path!");

        if( !is_string( $secondPath ) )
            throw new Exception("Second parameter you've passed is not a string! Also ensure it represents a valid path to image file.");

        if( !file_exists ( $secondPath ) )
            throw new Exception("Second parameter you've passed is not a valid path!");

        $this->firstImagePath = $firstPath;
        $this->secondImagePath = $secondPath;
    }

    public function compareImages(){
        if( $this->firstImagePath == null )
            throw new Exception("First image is not set! Please call 'initialize(firstPath, secondPath) method!");

        if( $this->secondImagePath == null )
            throw new Exception("Second image is not set! Please call 'initialize(firstPath, secondPath) method!");

        $i1 = @imagecreatefromstring(file_get_contents($this->firstImagePath));
        $i2 = @imagecreatefromstring(file_get_contents($this->secondImagePath));

        // dimensions of the first image
        $sx1 = imagesx($i1);
        $sy1 = imagesy($i1);

        // compare dimensions
        if ($sx1 !== imagesx($i2) || $sy1 !== imagesy($i2)) {
            echo "The images are not even the same size";
            return false;
        }

        // create a diff image
        $diffi = imagecreatetruecolor($sx1, $sy1);
        $green = imagecolorallocate($diffi, 0, 255, 0);
        imagefill($diffi, 0, 0, imagecolorallocate($diffi, 0, 0, 0));

        // increment this counter when encountering a pixel diff
        $different_pixels = 0;

        // loop x and y
        for ($x = 0; $x < $sx1; $x++) {
            for ($y = 0; $y < $sy1; $y++) {

                $rgb1 = imagecolorat($i1, $x, $y);
                $pix1 = imagecolorsforindex($i1, $rgb1);

                $rgb2 = imagecolorat($i2, $x, $y);
                $pix2 = imagecolorsforindex($i2, $rgb2);

                if ($pix1 !== $pix2) { // different pixel
                    // increment and paint in the diff image
                    $different_pixels++;
                }

            }
        }


        if (!$different_pixels) {
            echo "images are the same!\n";
            return true;
        } else {
            echo "images are not the same!\n";
            return false;
        }

    }

    function __destruct() {
        echo "Completed in ", microtime(true) - $this->start, " Seconds\n";
    }


}


?>