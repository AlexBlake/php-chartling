<?php
namespace Chartling\Palletes\Shapes;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Line extends \Chartling\Palletes\Shape {

    private $start;
    private $end;

    public function __construct($start, $end, $fill = null, $lineWidth = 1) {
        parent::__construct($start+$end, $fill, $fill, $lineWidth);
        $this->start = $start;
        $this->end = $end;
    }

    public function doRender(&$chart) {
        
        if($this->fill != null)
        {
            $color = $this->interpretColor($chart, $this->fill);
            $done = imageline($chart->image, 
                $this->start[0], $this->start[1], 
                $this->end[0], $this->end[1], 
                $color);
            if(!$done) { return false; }
        }

        return true;
    }
}