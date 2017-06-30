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
    protected $color_value;

    public function __construct($name, $color) {
        $this->name = $name;
        $this->color = $this->validateColorArray($color);
    }

    public function getName() {
        return $this->name;
    }

    public function render(&$chart) {
        $this->image = $chart->image;
        // validate any new color
        switch(count($this->color)) {
            case 3:
                $this->color_value = imagecolorallocate($this->image, $this->color[0], $this->color[1], $this->color[2]);
            break;
            case 4:
                $this->color_value = imagecolorallocatealpha($this->image, $this->color[0], $this->color[1], $this->color[2], $this->color[3]);
            break;
        }

    }

    public function value() {
        return (int)$this->color_value;
    }

    public function validateColorArray($color) {
        // ensure we are dealing with indexed array
        $color = array_values($color);

        //handle wrong datatype of length
        if(! is_array($color) || count($color) > 4 || count($color) < 3 ) { 
            throw new \Chartling\Exceptions\InvalidColorException();
        }

        // ensure int values in range 0-255 for 0-2(rgb) and 0-127 for 3(alpha)
        $valid = count($color) == count(array_filter($color, function(&$v, $k) { 
            return is_int($v) && ( $k == 3 ? ($v <= 127 && $v >= 0 ) : ($v <= 255 && $v >= 0 ) ); 
        }, ARRAY_FILTER_USE_BOTH ));

        if(!$valid) {
            throw new \Chartling\Exceptions\InvalidColorException();
        }

        return $color;
    }
}