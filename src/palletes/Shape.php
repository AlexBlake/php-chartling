<?php
namespace Chartling\Palletes;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Shape {

    private $vertices;
    private $line;
    private $fill;

    public function __construct($vertices, $line = null, $fill = null) {
        $this->vertices = $vertices;
        $this->line = $line;
        $this->fill = $fill;
    }

    public function render(&$image) {
        
        if($this->fill != null)
        {
            $done = imagefilledpolygon($image, $this->vertices, count($this->vertices)/2, $this->fill->get());
            if(!$done) { return false; }
        }

        if($this->line != null)
        {
            $done = imagepolygon($image, $this->vertices, count($this->vertices)/2, $this->line->get());
            if(!$done) { return false; }
        }

        return true;
    }
}