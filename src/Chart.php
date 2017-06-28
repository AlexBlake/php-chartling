<?php
namespace Chartling;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Class for generating chart images.
*/

class Chart {
    
    public $image = null;

    private $height = 0;
    private $width = 0;

    protected $alpha_channel = false;

    private $colors = [];
    private $background_color = null;


    /**
    *   Class Constructor - Chartling\Chart
    *
    *   @param Int $height  Height of chart
    *   @param Int $width   Height of chart
    *   
    *   Optional
    *   @param Boolean $alpha   if the chart should facilitate alpha channel colors or not
    *   @param Int $bg  background color of the chart
    */
    public function __construct($height, $width, $alpha = false, $bg = null) {
        
        // Set class variables
        $this->height = $height;
        $this->width = $width;
        $this->alpha_channel = ( $alpha ? true : false );

        // Create the image per dimentions
        $this->image = imagecreatetruecolor($this->height, $this->width);
        
        // Add handle for alpha channel if needed
        if($this->alpha_channel)
        {
            imagesavealpha($this->image, true);
            imagealphablending($this->image, false);
        }

        // Handle background colour
        if(is_array($bg) && count($bg) >= 3 && count($bg) <= 4)
        {
            $this->background_color = new \Chartling\Palletes\Color($this->image, 'background', $bg);
            $this->setBackground($this->background_color);
        }
    }

    /**
    *   Function to render PNG of image to either a path or return output
    *   
    *   Optional
    *   @param String $path    Path to desitnation for image file
    *   @param Int $quality     Integer scale representation of compression used for image
    *   @param Int $filters     Bitmask of PHP constants for filters to apply to image
    */
    public function renderPNG($path = null, $quality = 6, $filters = null){
        if($path != null)
        {
            if(is_writable($path))
            {
                return imagepng($this->image, $path, $quality, $filters);
            }
            return false;
        }
        return imagepng($this->image);
    }

    /**
    *   Function to set background color of the image
    *   
    *   @param \Chartling\Palletes\Color $color    Color object reference to maintained color
    */
    public function setBackground($color) {
        // only handle out colour objects and references to maintained colors to avoid mishaps
        if($color instanceof Color) {
            $this->background_color = $color;
        }
        elseif(is_string($color) && isset($this->colors[$color]))
        {
            $this->background_color = $this->colors[$color];
        }

        // Set the image backrgound color
        $res = imagefill($this->image, 0, 0, $this->background_color->value());
        if($res !== false)
        {
            return $this;
        }
        return false;
    }

    /**
    *   Function to add a color to the images pallete
    *   
    *   @param String $name    String value for name reference to color
    *   @param Int $r    Integer value for Red hue in the range of 0-255
    *   @param Int $g    Integer value for Red hue in the range of 0-255
    *   @param Int $b    Integer value for Red hue in the range of 0-255
    *   
    *   Optional
    *   @param Int $a    Integer value for alpha opacity in the range of 0-127
    */
    public function setColor($name, $r, $g, $b, $a = null) {
        $color = new \Chartling\Palletes\Color($this->image, $name, ( $a == null ? [$r, $g, $b] : [$r, $g, $b, $a] ));
        if($color !== false)
        {
            $this->colors[$name] = $color;
            return $this;
        }
        return false;
    }

    public function getColor($name) {
        return $this->colors[$name];
    }

    public function addShape($shape) {
        $shape->render($this);
        return $this;
    }

}