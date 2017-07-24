<?php
namespace Chartling\Charts;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Class for generating chart images.
*/

class Pie extends \Chartling\Chart {
    
   protected $location;
   protected $dataset = [];

   protected $pie_colors = [];
   protected $line_color;
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
    public function __construct($height, $width, $dataset, $bg = null, $alpha = false) {
        parent::__construct($height, $width, $bg, $alpha);
        
        // set centre point locaiton for anchor
        $this->location = [ $width/2, $height/2 ];

        // set the dataset for the graph from input
        $this->setData($dataset);
    }


    public function render() {
        $last = 0;
        $color_idx = 0;
        foreach($this->dataset as $deg) {
            
            $fill = $this->pie_colors[$color_idx];
            $line = $this->colors[$this->line_color];
            $angle = $last+$deg;
            $angle = ($angle > 360 ? $angle-360 : $angle);
            // var_dump([$last,$angle]);
            if($deg > 0)
                $this->drawArc($last, $angle, $fill, $line, 1);
            
            $last = $angle;
            $color_idx++;
            if(count($this->pie_colors) < $color_idx) {
                $color_idx = 0;
            }
        }
    }

    public function setData($dataset) {
        // Validate the data comming in
        $this->validateDataset($dataset);

        $this->dataset = $dataset;
        return $this;
    }

    public function setPieColors($colors) {
        foreach($colors as $key) {
            $this->pie_colors[] = $key;
        }
        return $this;
    }

    public function setLineColor($color) {
        $this->line_color = $color;

        return $this;
    }

    protected function validateDataset($dataset, $can_have_arrays = null, $can_have_mixed = null, $can_be_odd = null) {
        parent::validateDataset($dataset, false, false, true);
        if(array_sum($dataset) > 360){
            throw new \Chartling\Exceptions\InvalidDatasetException("Values for chart type PIE cannot exceed total combined value of 360");
        }
        return true;
    }

    protected function validateColors($colors) {
        parent::validateDataset($dataset, false, false, true);
        if(array_sum($dataset) > 360){
            throw new \Chartling\Exceptions\InvalidDatasetException("Values for chart type PIE cannot exceed total combined value of 360");
        }
        return true;
    }



    protected function drawArc($start, $stop, $fill, $line = null, $thickness = 1) {
        $this->addShape(new \Chartling\Palletes\Shapes\Arc([$this->location[0], $this->location[1]], [$this->height,$this->width], [$start,$stop], $fill, $line, $thickness ));
    }
}