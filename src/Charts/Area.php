<?php
namespace Chartling\Charts;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Class for generating chart images.
*/

class Area extends \Chartling\Chart {
    
   protected $dataset = [];
   protected $titles = [];
   protected $steps;
   protected $padding;
   protected $lineThickness;
   protected $fontSize;

   public $scaleFormat;

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
    public function __construct($height, $width, $dataset, $titles, $steps, $colors, $lineThickness = 1, $fontSize = 1, $bg = null, $alpha = false) {
        parent::__construct($height, $width, $bg, $alpha);

        $this->titles = $titles;
        $this->lineThickness = $lineThickness;
        $this->fontSize = (int)$fontSize;
        $this->steps = $steps;

        $this->padding = [100,100];

        // Set the colors
        $this->setColors($colors);


        // set the dataset for the graph from input
        $this->setData($dataset);

    }

    public function setData($dataset) {
        // Validate the data comming in
        $this->validateDataset($dataset);

        $this->dataset = $dataset;
        return $this;
    }

    public function formatScale($callbackX, $callbackY) {
        $this->scaleFormat = [$callbackX,$callbackY];
    }


    public function render() {

        $color_idx = 0;
        foreach($this->dataset as $area) {
            $fill = array_values($this->colors)[$color_idx];
            $scale = $this->getScale($area);
            $this->addShape(new \Chartling\Palletes\Shape($this->generatePolygon($area, $scale), $fill));


            // Scale Y
            $this->addShape(new \Chartling\Palletes\Shapes\Scale(
                [$this->padding[0],$this->padding[1]], 
                [$this->padding[0],$this->height-$this->padding[1]], 
                [$scale[1][1], $scale[1][0]], $this->steps[1], 15, -1, [0,0,0], $this->lineThickness, ($this->fontSize/2), $this->scaleFormat[1]));
            // Scale Y Text
            $this->addText(new \Chartling\Palletes\Text([$this->width/2,$this->height-$this->padding[1]/3], $this->fontSize, $this->titles[0], [0,0,0], 0, null, 'middle'));


            // Scale X
            $this->addShape(new \Chartling\Palletes\Shapes\Scale(
                [$this->padding[0],$this->height-$this->padding[1]], 
                [$this->width-$this->padding[0],$this->height-$this->padding[1]], 
                [$scale[0][0], $scale[0][1]], $this->steps[0], 15, 1, [0,0,0], $this->lineThickness, ($this->fontSize/2), $this->scaleFormat[0]));
            // Scale X Text
            $this->addText(new \Chartling\Palletes\Text([$this->padding[0]/3,$this->height/2], $this->fontSize, $this->titles[1], [0,0,0], 90, null, 'middle'));


            $color_idx++;
            if(count($this->colors) < $color_idx) {
                $color_idx = 0;
            }
        }
    }

    private function getScale($dataset) {

        $flatX =  array();
        $flatY = array();
        foreach($dataset as $key => $item){
            if(is_array($item))
            {
                $flatY[] = $item[1];
                $flatX[] = $item[0];
            }
            else
            {
                $flatY[] = $item;
                $flatX[] = $key;
            } 
        }
        return [ [min($flatX),max($flatX)], [min($flatY),max($flatY)] ];
    }

    private function generatePolygon($dataset, $scale) {
        $polygon = [ $this->padding[0]+($this->lineThickness/2), ($this->height-$this->padding[1])-($this->lineThickness/2) ];

        foreach($dataset as $key => $value) {
            if(is_array($value))
            {
                $x_offset = (($value[0]-$scale[0][0]) / ($scale[0][1]-$scale[0][0])) * ($this->width-($this->padding[0]*2));
                $x = ($this->padding[0]+($this->lineThickness/2)) + $x_offset;

                $y_offset = (($value[1]-$scale[1][0]) / ($scale[1][1]-$scale[1][0])) * ($this->height-($this->padding[1]*2));
                $y = ($this->height-$this->padding[1]-($this->lineThickness/2)) - $y_offset;

                // var_dump($x_offset);
                // var_dump($value, $x_offset, $x, $y, ($scale[0][1]-$scale[0][0]), $scale);die;
                // var_dump($value[1], $y);
            }
            else
            {
                $x = ($this->padding[0]+($this->lineThickness/2)) + $key * (($this->width-($this->padding[0]*2)) / $this->steps[0]);
                $y = ($this->height-$this->padding[1]) - ($this->height-$this->padding[1])*($value/$this->height);
            }

            $polygon[] = $x;
            $polygon[] = $y;
        }
        // die;
        $polygon[] = ($this->width-$this->padding[0]) - ($this->lineThickness/2);
        $polygon[] = ($this->height-$this->padding[1]) - ($this->lineThickness/2);

        return $polygon;
    }

    protected function validateDataset($dataset, $can_have_arrays = null, $can_have_mixed = null, $can_be_odd = null) {
        parent::validateDataset($dataset, true, true, true);
        return true;
    }

}