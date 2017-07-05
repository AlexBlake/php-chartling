<?php
namespace Chartling\Palletes;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Text {

    protected $location;
    protected $size;
    protected $string;
    protected $angle;
    protected $color;
    protected $font;
    protected $anchor;


    public function __construct($location, $size, $string, $color, $angle = false, $font = null, $anchor = null) {
        $this->location = $location;
        $this->size = $size;

        $this->string = $string;
        $this->angle = $angle;

        $this->color = $color;
        
        $this->font = \Chartling\Font::load( (isset($font[0]) ? $font[0] : null), (isset($font[1]) ? $font[1] : null));
            
        $this->anchor = ( $anchor ? $anchor : 'middle' );

    }

    public function setColor($color) {
        $this->color = $color;
    }

    public function render(&$chart) {

        $color = $this->interpretColor($chart, $this->color);
        $coords = $this->anchorOffset($this->location);
        // var_dump($font, file_exists($font));die;
        imagettftext( $chart->image, $this->size, $this->angle, $coords[0], $coords[1], $color, $this->font, $this->string );

        return true;
    }

    private function anchorOffset($coords) {
        // var_dump($coords);
        $bbox = imageftbbox($this->size, $this->angle, $this->font, $this->string);

        switch($this->anchor) {
            case 'middle':
                $coords[0] = $coords[0] - ($bbox[4] + $bbox[0]) / 2;
                $coords[1] = $coords[1] - ($bbox[5] + $bbox[1]) / 2;
            break;
            case 'top':
                $coords[0] = $coords[0] - ($bbox[6] + $bbox[4]) / 2;
                $coords[1] = $coords[1] - ($bbox[5] + $bbox[7]) / 2;
            break;
            case 'bottom':
                $coords[0] = $coords[0] - ($bbox[0] + $bbox[2]) / 2;
                $coords[1] = $coords[1] - ($bbox[1] + $bbox[3]) / 2;
            break;
            case 'left':
                $coords[0] = $coords[0] - ($bbox[0] + $bbox[6]) / 2;
                $coords[1] = $coords[1] - ($bbox[1] + $bbox[7]) / 2;
            break;
            case 'right':
                $coords[0] = $coords[0] - ($bbox[2] + $bbox[4]) / 2;
                $coords[1] = $coords[1] - ($bbox[3] + $bbox[5]) / 2;
            break;
        }

        // var_dump($bbox, [$x,$y], $coords);die;
        return $coords;
    }

    protected function interpretColor(&$chart, &$color) {
        if($color instanceof \Chartling\Palletes\Color)
        {
            return $color->value();
        }
        if(is_array($color))
        {
            $color = new \Chartling\Palletes\Color(null, $color);
            $chart->colors[] = $color;
            $color->render($chart);
            return $color->value();
        }
        if(is_string($color))
        {
            return $chart->getColor($color)->value();
        }
    }
}