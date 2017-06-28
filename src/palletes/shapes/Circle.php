<?php
namespace Chartling\Palletes\Shapes;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Circle extends \Chartling\Palletes\Shape {

    private $size;
    private $arc = [0,0];

    public function __construct($vertices, $size = [0,0], $line = null, $fill = null) {
        parent::__construct($vertices, $line, $fill);
        $this->size = $size;
    }

    protected function render(&$image) {
        
        if($this->fill != null)
        {
            $done = imagefilledarc($image, 
                $this->vertices[0], $this->vertices[1], 
                $this->size[0], $this->size[1], 
                $this->arc[0], $this->arc[1],
                $this->fill->get(), IMG_ARC_PIE);
            if(!$done) { return false; }
        }

        if($this->line != null)
        {
            $done = imagefilledarc($image, 
                $this->vertices[0], $this->vertices[1], 
                $this->size[0], $this->size[1], 
                $this->arc[0], $this->arc[1],
                $this->line->get(), IMG_ARC_EDGED | IMG_ARC_NOFILL);
            if(!$done) { return false; }
        }

        return true;
    }
}