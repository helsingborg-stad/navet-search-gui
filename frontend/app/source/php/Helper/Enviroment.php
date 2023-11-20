<?php

namespace NavetSearch\Helper;

use \HelsingborgStad\GlobalBladeEngine as Blade; 

class Enviroment
{
    public static function componentLibraryIsInstalled()
    {
        if (file_exists(BASEPATH . "source/library/source/php/Init.php")) {
            return true; 
        }
        return false;
    }

    public static function loadInstalledComponentLibrary()
    {
        if (file_exists(BASEPATH . "source/library/source/php/Init.php")) {
            require_once BASEPATH . "source/library/load.php";
            
            $init = new \ComponentLibrary\Init([BASEPATH . 'views']);
            
            return $init->getEngine();

        }
        return false;
    }
}