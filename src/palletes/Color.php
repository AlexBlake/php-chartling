<?php
namespace Chartling\Palletes;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Color {

    protected $image;
    public $name;
    protected $color;

    public function __construct(&$image, $name, $color = null) {
        $this->image = $image;
        $this->name = $name;
        if($color != null)
        {
            $this->set($color);
        }
    }

    public function getName() {
        return $this->name;
    }

    public function get() {
        return $this->color;
    }

    public function set($color) {
        // validate any new color
        $color = $this->validateColorArray($color);
        switch(count($color)) {
            case 3:
                $this->color = imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
            break;
            case 4:
                $this->color = imagecolorallocatealpha($this->image, $color[0], $color[1], $color[2], $color[3]);
            break;
        }
    }

    public function validateColorArray($color) {
        // ensure we are dealing with indexed array
        $color = array_values($color);

        //handle wrong datatype of length
        if(! is_array($color) || count($color) > 4 || count($color) < 3 ) { 
            throw new \Chartling\Exceptions\InvalidColorException();
        }

        // ensure int values in range 0-255 for 0-2(rgb) and 0-127 for 3(alpha)
        $valid = count($color) != count(array_filter($color, function(&$v, $k) { 
            return is_int($v) && ( $k == 3 ? ($v <= 127 && $v >= 0 ) : ($v <= 255 && $v >= 0 ) ); 
        }, ARRAY_FILTER_USE_BOTH ));

        if(!$valid) {
            throw new \Chartling\Exceptions\InvalidColorException();
        }

        return $color;
    }
}