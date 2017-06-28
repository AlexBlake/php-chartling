<?php
namespace Chartling\Palletes;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Shape {

    protected $vertices;
    protected $line;
    protected $fill;

    public function __construct($vertices, $line = null, $fill = null) {
        $this->vertices = $vertices;
        $this->line = $line;
        $this->fill = $fill;
    }

    public function render(&$chart) {
        
        if($this->fill != null)
        {
            $done = imagefilledpolygon($chart->image, $this->vertices, count($this->vertices)/2, $this->fill->value());
            if(!$done) { return false; }
        }

        if($this->line != null)
        {
            $done = imagepolygon($chart->image, $this->vertices, count($this->vertices)/2, $this->line->value());
            if(!$done) { return false; }
        }

        return true;
    }

    protected function interpretColor(&$chart, &$color) {
        if($color instanceof \Chartling\Palletes\Color)
        {
            return $color->value();
        }
        if(is_string($color))
        {
            return $chart->getColor($color)->value();
        }
    }
}