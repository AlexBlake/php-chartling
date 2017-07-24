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
    private $scaleOffset;
    private $scalePoints;
    private $scaleInterval;
    private $scaleInvert;
    private $fontSize;
     
    private $scaleLength;
    private $scaleSide;

    private $scale_formater;

    public function __construct($start, $end, $scale, $scalePoints, $scaleLength, $scaleSide, $fill = null, $lineWidth = 1, $fontSize = 10, $formatCallback) {
        parent::__construct($start+$end, $fill, $fill, $lineWidth);
        
        $this->fontSize = $fontSize;

        $this->start = $start;
        $this->end = $end;

        $this->scale = $scale;
        $this->scaleOffset = min($this->scale);

        $this->scalePoints = $scalePoints;
        $this->scaleInterval = ( max($scale) - min($scale) ) / $scalePoints;
        $this->scaleInvert = ( $scale[0] > $scale[1] ? true : false );

        $this->scaleLength = $scaleLength;
        $this->scaleSide = $scaleSide;

        $this->formatScale($formatCallback);

    }

    public function __call($method, $args)
    {
        if(is_callable(array($this, $method))) {
            return call_user_func_array($this->$method, $args);
        }
        // else throw exception
    }

    public function formatScale($callback) {
        $this->scale_formater = $callback;
    }

    private function doScaleFormat($value) {
        if($this->scale_formater != null)
        {
            return $this->scale_formater($value);
        }
        return $value;
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
            $step = $distance / $this->scalePoints;
            
            // start drawing scale from start of line
            $coords = $this->start;
            for( $i = 0; $i <= $this->scalePoints; $i++ )
            {
                $point = $this->pointPerpendicular($coords, $this->scaleLength);
                imageline($chart->image, $coords[0], $coords[1], $point[0], $point[1], $color);

                $coords = $this->pointOnLineAtDistance($step*$i, $distance);

                $point_txt = $this->pointPerpendicular($coords, $this->textPadding());
                $this->drawScaleTextAtTick($chart, $point_txt, $i);
            }

            $point = $this->pointPerpendicular($coords, $this->scaleLength);
            imageline($chart->image, $coords[0], $coords[1], $point[0], $point[1], $color);

            $point_txt = $this->pointPerpendicular($coords, $this->textPadding());
            $this->drawScaleTextAtTick($chart, $point_txt, $this->scalePoints);
        }

        return true;
    }

    private function textPadding() {
        switch($this->lineType()) {
            case 'X':
                return $this->scaleLength+$this->lineWidth;
            break;
            case 'Y':
                return $this->scaleLength+$this->lineWidth;
            break;
            case false:
                return $this->scaleLength+$this->lineWidth;
            break;
        }
    }

    private function drawScaleTextAtTick(&$chart, $point, $tick) {

        // var_dump($this->scaleOffset, $this->scale, $this->scaleInterval, $tick);die;
        $value = $this->scaleOffset + ($this->scaleInvert ? ($this->scaleInterval * ($this->scalePoints - $tick)) : ($this->scaleInterval * $tick) );
        $angle = $this->getAngle();
        $anchor = 'middle';
        switch(true) {
            case $angle >= 0 && $angle < 90:
                $anchor = 'top';
            break;
            case $angle >= 90 && $angle < 180:
                $anchor = 'top';
                $angle = -1*$angle;
            break;
            case $angle >= 180 && $angle < 270:
                $anchor = 'top';
            break;
            case $angle >= 270 && $angle < 361:
                $anchor = 'top';
                $angle = -1*$angle;
            break;
        }

        $chart->addText(new \Chartling\Palletes\Text($point, $this->fontSize, $this->doScaleFormat($value), $this->fill, $angle, null, $anchor ));

    }

    private function distance($p1, $p2) {
        return sqrt( pow(($p2[0] - $p1[0]), 2) + pow(($p2[1] - $p1[1]), 2) );
    }

    private function lineType() {
        return ( $this->start[0] == $this->end[0] ? 'X' : ($this->start[1] == $this->end[1] ? 'Y' : false ) );
    }

    private function getAngle() {
        return atan2( ($this->end[1]-$this->start[1]), ($this->end[0]-$this->start[0])) * 180 / pi();
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

    private function pointPerpendicular($coords, $distance) {
        if($coords == null)
        {
            $coords = [ $this->start, $this->end ];
        }
        switch($this->lineType()) {
            case 'X':
                return [
                    $coords[0] + ($this->scaleSide*$distance),
                    $coords[1]
                ];
            break;
            case 'Y':
                return [
                    $coords[0],
                    $coords[1] + ($this->scaleSide*$distance)
                ];
            break;
            case false:
                $slope = -1*(1/$this->slope());
                $offset = $coords[1] - $slope * $coords[0];

                $x = $coords[0] + ($this->scaleSide*$distance);
                $y = ( $slope * $x ) + $offset;
                $x = ($y - $offset ) /  $slope;
                return [
                    $x, $y
                ];
            break;
        }
    }


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