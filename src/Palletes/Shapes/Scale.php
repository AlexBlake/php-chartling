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
    private $scaleSide;

    public function __construct($start, $end, $scale, $scaleLength, $scaleSide, $fill = null, $lineWidth = 1) {
        parent::__construct($start+$end, $fill, $fill, $lineWidth);
        $this->start = $start;
        $this->end = $end;
        $this->scale = $scale;
        $this->scaleLength = $scaleLength;
        $this->scaleSide = $scaleSide;
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



            // get distance between start and end
            // $distance = $this->end[1] - $this->start[1];
            $distance = $this->distance($this->start, $this->end);
            // var_dump($distance);die;
            // calculate the step
            $step = $distance / $this->scale;
            
            // start drawing scale from start of line
            $coords = $this->start;
            for( $i = 0; $i <= $this->scale; $i++ )
            {
                $point = $this->pointPerpendicular($coords);
                imageline($chart->image, $coords[0], $coords[1], $point[0], $point[1], $color);

                $coords = $this->pointOnLineAtDistance($step*$i, $distance);
            }

            $point = $this->pointPerpendicular($coords);
            imageline($chart->image, $coords[0], $coords[1], $point[0], $point[1], $color);
        }

        return true;
    }

    private function distance($p1, $p2) {
        return sqrt( pow(($p2[0] - $p1[0]), 2) + pow(($p2[1] - $p1[1]), 2) );
    }

    private function lineType() {
        return ( $this->start[0] == $this->end[0] ? 'X' : ($this->start[1] == $this->end[1] ? 'Y' : false ) );
    }

    private function slope() {
        return ( ($this->start[1] - $this->end[1]) / ($this->start[0] - $this->end[0]) );
    }
    
    private function offset() {
        return ( $this->start[1] - ( $this->slope() * $this->start[0] ) );
    }

    private function pointOnLineAtDistance($step, $distance, $line = null) {
        if($line == null)
        {
            $line = [ $this->start, $this->end ];
        }
        switch($this->lineType()) {
            case 'X':
                return [
                    $line[0][0],
                    $line[0][1] + $step
                ];
            break;
            case 'Y':
                return [
                    $line[0][0] + $step,
                    $line[0][1]
                ];
            break;
            case false:
                return [
                    ( $line[0][0] + ($step/$distance)*($line[1][0] - $line[0][0]) ),
                    ( $line[0][1] + ($step/$distance)*($line[1][1] - $line[0][1]) )
                ];
            break;
        }
    }

    private function pointPerpendicular($coords) {
        if($coords == null)
        {
            $coords = [ $this->start, $this->end ];
        }
        switch($this->lineType()) {
            case 'X':
                return [
                    $coords[0] + ($this->scaleSide*$this->scaleLength),
                    $coords[1]
                ];
            break;
            case 'Y':
                return [
                    $coords[0],
                    $coords[1] + ($this->scaleSide*$this->scaleLength)
                ];
            break;
            case false:
                $slope = -1*(1/$this->slope());
                $offset = $coords[1] - $slope * $coords[0];

                $x = $coords[0] + ($this->scaleSide*$this->scaleLength);
                $y = ( $slope * $x ) + $offset;
                $x = ($y - $offset ) /  $slope;
                return [
                    $x, $y
                ];
            break;
        }
    }


    // private function getScaleSide(&$chart) {

    //     switch( join('-', [$this->getQuadrant($chart, $this->start), $this->getQuadrant($chart, $this->end)] ) ){
    //         // Starting quad 1
    //         case '1-1':
    //             if($this->start[0] < $this->end[0])
    //             {
    //                 return 1
    //             }
    //         break;
    //         case '1-2':

    //         break;
    //         case '1-3':
            
    //         break;
    //         case '1-4':
            
    //         break;
    //         // Starting quad 2
    //         case '2-1':

    //         break;
    //         case '2-2':

    //         break;
    //         case '2-3':
            
    //         break;
    //         case '2-4':
            
    //         break;
    //         // Starting quad 3
    //         case '3-1':

    //         break;
    //         case '3-2':

    //         break;
    //         case '3-3':
            
    //         break;
    //         case '3-4':
            
    //         break;
    //         // Starting quad 4
    //         case '4-1':

    //         break;
    //         case '4-2':

    //         break;
    //         case '4-3':
            
    //         break;
    //         case '4-4':
            
    //         break;
    //     }
        
    // }

    private function getQuadrant(&$chart, $point) {
        switch(join('-',[( $point[0] <= $chart->width/2 ? 0 : 1 ),( $point[1] <= $chart->height/2 ? 1 : 0 )])) {
            case '0-1':
                return 1;
            break;
            case '1-1':
                return 2;
            break;
            case '1-0':
                return 3;
            break;
            case '0-0':
                return 4;
            break; 
        }
    }
}