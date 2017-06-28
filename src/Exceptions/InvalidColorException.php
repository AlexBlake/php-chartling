<?php
namespace Chartling\Exceptions;

class InvalidColorException extends \Exception {

    public function __construct($message = null, $code = null, $previous = null) {
        if($message == null)
        {
            $message = "Invalid color parameters, expected 3 integer values 0-255 and optional 4th 0-127";
        }
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}