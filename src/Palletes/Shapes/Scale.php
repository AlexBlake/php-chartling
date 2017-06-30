<?php
namespace Chartling\Palletes\Shapes;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Scale extends \Chartling\Palletes\Shape {

    private $start;
    private $end;

    private $scale;
    private $scaleLength;

    public function __construct($start, $end, $scale, $scaleLength, $fill = null, $lineWidth = 1) {
        parent::__construct($start+$end, $fill, $fill, $lineWidth);
        $this->start = $start;
        $this->end = $end;
        $this->scale = $scale;
        $this->scaleLength = $scaleLength;
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

            // Get which side of the line to draw them on
            $direction = ( $this->start[0] <= $chart->width/2 ? -1 : 1 );

            // get distance between start and end
            $distance = $this->end[1] - $this->start[1];

            // calculate the step
            $step = $distance / $this->scale;
            
            // start drawing scale from start of line
            $coords = $this->start;
            for( $i = 0; $i <= $this->scale; $i++ )
            {

                // var_dump($i);
                imageline($chart->image, 
                $coords[0], $coords[1], 
                
                $coords[0] + ( $direction > 0 ? $this->scaleLength : -$this->scaleLength),
                $coords[1],// + ( $direction > 0 ? $this->scaleLength : -$this->scaleLength)
                
                $color);

                $coords[1] = $coords[1] + $step;
            }
            // die;
        }

        return true;
    }
}