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
    protected $lineWidth;

    public function __construct($vertices, $fill = null, $line = null, $lineWidth = 1) {
        $this->vertices = $vertices;
        $this->line = $line;
        $this->fill = $fill;
        $this->lineWidth = $lineWidth;
    }

    public function render(&$chart) {

        $this->preRender($chart);

        $this->doRender($chart);

        $this->postRender($chart);

        return true;
    }

    public function preRender(&$chart) {
        if($this->lineWidth != null)
        {
            imagesetthickness($chart->image, $this->lineWidth);
        }
    }

    public function postRender(&$chart) {
        if($this->lineWidth != null)
        {
            imagesetthickness($chart->image, 1);
        }
    }

    public function doRender(&$chart) {
        
        if($this->fill != null)
        {
            $color = $this->interpretColor($chart, $this->fill);
            $done = imagefilledpolygon($chart->image, $this->vertices, count($this->vertices)/2, $color);
            if(!$done) { return false; }
        }

        if($this->line != null)
        {
            $color = $this->interpretColor($chart, $this->line);
            $done = imagepolygon($chart->image, $this->vertices, count($this->vertices)/2, $color);
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