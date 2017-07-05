<?php
namespace Chartling\Charts;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Class for generating chart images.
*/

class Area extends \Chartling\Chart {
    
   protected $dataset = [];
   protected $titles = [];
   protected $scale;
   protected $steps;
   protected $padding;
   protected $lineThickness;

    /**
    *   Class Constructor - Chartling\Chart
    *
    *   @param Int $height  Height of chart
    *   @param Int $width   Height of chart
    *   
    *   Optional
    *   @param Int $bg  background color of the chart
    *   @param Boolean $alpha   if the chart should facilitate alpha channel blending or not
    */
    public function __construct($height, $width, $dataset, $titles, $steps, $colors, $lineThickness = 1, $bg = null, $alpha = false) {
        parent::__construct($height, $width, $bg, $alpha);

        $this->titles = $titles;
        $this->lineThickness = $lineThickness;
        $this->steps = $steps;

        $this->padding = [100,100];

        // Set the colors
        $this->setColors($colors);


        // set the dataset for the graph from input
        $this->setData($dataset);

        $this->scale = $this->getScale($dataset);

    }

    public function setData($dataset) {
        // Validate the data comming in
        $this->validateDataset($dataset);

        $this->dataset = $dataset;
        return $this;
    }


    public function render() {

        $color_idx = 0;
        foreach($this->dataset as $area) {
            $fill = array_values($this->colors)[$color_idx];

            $this->addShape(new \Chartling\Palletes\Shape($this->generatePolygon($area), $fill));

            $this->addShape(new \Chartling\Palletes\Shapes\Scale(
                [$this->padding[0],$this->padding[1]], 
                [$this->padding[0],$this->height-$this->padding[1]], 
                [$this->scale[1], $this->scale[0]], $this->steps[1], 15, -1, [0,0,0], $this->lineThickness ));
            $this->addShape(new \Chartling\Palletes\Shapes\Scale(
                [$this->padding[0],$this->height-$this->padding[1]], 
                [$this->width-$this->padding[0],$this->height-$this->padding[1]], 
                [$this->scale[0], $this->scale[1]], $this->steps[0], 15, 1, [0,0,0], $this->lineThickness ));

            $this->addText(new \Chartling\Palletes\Text([$this->padding[0]/3,$this->height/2], 18, $this->titles[1], [0,0,0], 90, null, 'middle'));
            $this->addText(new \Chartling\Palletes\Text([$this->width/2,$this->height-$this->padding[1]/3], 18, $this->titles[0], [0,0,0], 0, null, 'middle'));

            $color_idx++;
            if(count($this->colors) < $color_idx) {
                $color_idx = 0;
            }
        }
    }

    private function getScale($dataset) {

        $flat = array();
        array_walk_recursive($dataset, function($a) use (&$flat) { $flat[] = $a; });
        return [min($flat),max($flat)];
    }

    private function generatePolygon($dataset) {
        $polygon = [ $this->padding[0]+($this->lineThickness/2), ($this->height-$this->padding[1])-($this->lineThickness/2) ];

        foreach($dataset as $key => $value) {

            $x = ($this->padding[0]+($this->lineThickness/2)) + $key * (($this->width-($this->padding[0]*2)) / 10);
            $y = ($this->height-$this->padding[1]) - ($this->height-$this->padding[1])*($value/$this->height);

            $polygon[] = $x;
            $polygon[] = $y;
        }

        $polygon[] = ($this->width-$this->padding[0]) - ($this->lineThickness/2);
        $polygon[] = ($this->height-$this->padding[1]) - ($this->lineThickness/2);

        return $polygon;
    }

    protected function validateDataset($dataset, $can_have_arrays = null, $can_have_mixed = null, $can_be_odd = null) {
        parent::validateDataset($dataset, true, true, true);
        return true;
    }

}