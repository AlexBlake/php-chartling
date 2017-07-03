<?php
namespace Chartling\Exceptions;

class InvalidDatasetException extends \Exception {

    public function __construct($message = null, $code = null, $previous = null) {
        if($message == null)
        {
            $message = "Invalid dataset, expected array of values or array of coordinate sets";
        }
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}