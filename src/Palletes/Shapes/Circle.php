<?php
namespace Chartling\Palletes\Shapes;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Circle extends \Chartling\Palletes\Shape {

    private $size;
    private $arc = [0,90];

    public function __construct($vertices, $size = [0,0], $line = null, $fill = null) {
        parent::__construct($vertices, $line, $fill);
        $this->size = $size;
    }

    public function render(&$chart) {
        
        if($this->fill != null)
        {
            $color = $this->interpretColor($chart, $this->fill);
            $done = imagefilledarc($chart->image, 
                $this->vertices[0], $this->vertices[1], 
                $this->size[0], $this->size[1], 
                $this->arc[0], $this->arc[1],
                $color, IMG_ARC_PIE);
            if(!$done) { return false; }
        }

        if($this->line != null)
        {
            $color = $this->interpretColor($chart, $this->line);
            $done = imagefilledarc($chart->image, 
                $this->vertices[0], $this->vertices[1], 
                $this->size[0], $this->size[1], 
                $this->arc[0], $this->arc[1],
                $color, ( $this->arc[1] != 0 ? IMG_ARC_EDGED | IMG_ARC_NOFILL : IMG_ARC_NOFILL ) );
            if(!$done) { return false; }
        }

        return true;
    }
}