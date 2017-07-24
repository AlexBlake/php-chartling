<?php
namespace Chartling;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Class for generating chart images.
*/

class Chart {
    
    public $image = null;

    public $height = 0;
    public $width = 0;

    protected $alpha_channel = false;

    public $colors = [];
    private $background_color = null;

    private $attributes = [
        'offset-degree' => 0,
    ];


    /**
    *   Class Constructor - Chartling\Chart
    *
    *   @param Int $height  Height of chart
    *   @param Int $width   Height of chart
    *   
    *   Optional
    *   @param Int $bg  background color of the chart
    *   @param Boolean $alpha   if the chart should facilitate alpha channel blending or not
    */
    public function __construct($height, $width, $bg = null, $alpha = false) {
        
        // Set class variables
        $this->height = $height;
        $this->width = $width;
        $this->alpha_channel = $alpha;

        // Create the image per dimentions
        $this->image = imagecreatetruecolor($this->width, $this->height);
        
        // Add handle for alpha channel if needed
        if($this->alpha_channel)
        {
            imagesavealpha($this->image, true);
            imagealphablending($this->image, true);
            imagesetinterpolation($this->image, IMG_TRIANGLE);
        }

        // Handle background colour
        if(is_array($bg) && count($bg) >= 3 && count($bg) <= 4)
        {
            $this->background_color = new \Chartling\Palletes\Color('background', $bg);
            $this->background_color->render($this);
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
        // check if anything needs to be rendered and render it
        
        if(method_exists($this, 'render')) {
            $this->render();
        }
        
        // do pathing checks
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
        if($color instanceof \Chartling\Palletes\Color) {
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
        $color = new \Chartling\Palletes\Color($name, ( $a == null ? [$r, $g, $b] : [$r, $g, $b, $a] ));
        if($color !== false)
        {
            if($name == null) {
                $this->colors[] = $color;
                $color->render($this);
            }
            $this->colors[$name] = $color;
            $this->colors[$name]->render($this);
            return $this;
        }
        return false;
    }

    public function setColors($colors) {
        foreach($colors as $key => $color) {
            if(! $color instanceof \Chartling\Palletes\Color)
            {
                $color = new \Chartling\Palletes\Color($key, ( count($color) == 3 ? [$color[0], $color[1], $color[2]] : [$color[0], $color[1], $color[2], $color[3]] ));
            }
            $this->colors[$key] = $color;
            $this->colors[$key]->render($this);
            if($color === false)
            {
                throw new InvalidColorException();
            }
        }
        return $this;
    }

    public function getColor($name) {
        return $this->colors[$name];
    }

    public function addShape($shape) {
        $shape->render($this);
        return $this;
    }

    public function addText($text) {
        $text->render($this);
        return $this;
    }

    public function setAttribute($name, $value) {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function getAttribute($name) {
        return $this->attributes[$name];
    }

    public function getDegree($val) {
        $value = $val + $this->attributes['offset-degree'];
        return ($value >= 0 ? $value : 360 - abs($value));
    }

    public function toBase64() {
        ob_start();
        $this->renderPNG();
        $contents = ob_get_contents();
        ob_end_clean();
        return "data:image/png;base64," . base64_encode($contents);
    }

    protected function validateDataset($dataset, $can_have_arrays = null, $can_have_mixed = null, $can_be_odd = null) {
        if(!is_array($dataset)) { 
            throw new \Chartling\Exceptions\InvalidDatasetException("Expected Array or values Or Array of location sets, got ".get_class($dataset));
        }

        //Ensure there are items in the array else fail with empty data
        if(count($dataset) == 0)
        {
            throw new \Chartling\Exceptions\InvalidDatasetException("Expected Array or values Or Array of location sets, got empty dataset");
        }

        // validate if there is an array present anywhere
        $arrArr = array_filter($dataset, "is_array");
        if(count($arrArr) > 0)
        {
            // cannot mix types
            if(count($arrArr) != count($dataset) && !$can_have_mixed)
            {
                throw new \Chartling\Exceptions\InvalidDatasetException("Expected Array or values Or Array of location sets, got Mixed");
            }
            $arrArr = array_filter($dataset, function(&$el){ return count($el) == 2; });
            // need data locations with 2 coords
            if(count($arrArr) != count($dataset) && !$can_be_odd)
            {
                throw new \Chartling\Exceptions\InvalidDatasetException("Expected Array of location sets [X,Y], some location sets missing or have extra data");
            }
        }
        // Validate if its a continuous dataset
        else
        {
            // validate that there are an even quantity of x/y values
            if(count($dataset) % 2 == 1)
            {
                if(!$can_be_odd) {
                    throw new \Chartling\Exceptions\InvalidDatasetException("Expected Array of values corresponding to x/y, dataset has invalid quantity of values (odd)");
                }
            }
        }
        return true;
    }
}