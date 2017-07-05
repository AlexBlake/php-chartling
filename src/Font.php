<?php
namespace Chartling;
/**
*   Author: Alex Blake <cr3ch4@gmail.com>
*   Chart Color Class for generating chart colors for php images.
*/
class Font {
    protected static $path = __DIR__.'/assets/fonts/';

    protected static $default_font = "Lato";
    protected static $default_style = "Regular";

    public static function load($font, $style = null) {
        if($font == null && $style == null) {
            return self::loadDefault();
        }
        if(strpos('/', $font) > 0 || strpos('/', $style) > 0 )
        {
            return $font;    
        }
        return self::$path.$font.'/'.$font.'-'.($style != null ? $style : 'Regular').'.ttf';
    }

    public static function loadDefault() {
        return self::$path.self::$default_font.'/'.self::$default_font.'-'.(self::$default_style != null ? self::$default_style : 'Regular').'.ttf';   
    }
}