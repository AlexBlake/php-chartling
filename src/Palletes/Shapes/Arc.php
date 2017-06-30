<?php
namespace Chartling\Palletes\Shapes;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Arc extends \Chartling\Palletes\Shape {

    private $size;
    private $arc = [0,0];

    public function __construct($vertices, $size = [0,0], $arc = [0,0], $fill = null, $line = null, $lineWidth = 1) {
        parent::__construct($vertices, $fill, $line, $lineWidth);
        $this->size = $size;
        $this->arc = $arc;
    }

    public function doRender(&$chart) {
        
        if($this->fill != null)
        {
            $color = $this->interpretColor($chart, $this->fill);
            $done = imagefilledarc($chart->image, 
                $this->vertices[0], $this->vertices[1], 
                $this->size[0], $this->size[1], 
                $chart->getDegree($this->arc[0]), $chart->getDegree($this->arc[1]),
                $color, IMG_ARC_PIE);
            if(!$done) { return false; }
        }

        if($this->line != null)
        {
            $color = $this->interpretColor($chart, $this->line);
            $done = imagefilledarc($chart->image, 
                $this->vertices[0], $this->vertices[1], 
                $this->size[0], $this->size[1], 
                $chart->getDegree($this->arc[0]), $chart->getDegree($this->arc[1]),
                $color, ( $this->arc[1] != 0 ? IMG_ARC_EDGED | IMG_ARC_NOFILL : IMG_ARC_NOFILL ) );
            if(!$done) { return false; }
        }

        return true;
    }
}