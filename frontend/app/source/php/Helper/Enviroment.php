<?php

namespace NavetSearch\Helper;

use HelsingborgStad\GlobalBladeService\GlobalBladeService;
use ComponentLibrary\Init as ComponentLibraryInit;

class Enviroment
{

    private static $loader = BASEPATH . "vendor/helsingborg-stad/component-library/load.php";
    private static $blade;

    public static function componentLibraryIsInstalled(): bool
    {
        if (file_exists(self::$loader)) {
            return true; 
        }
        return false;
    }

    public static function loadInstalledComponentLibrary()
    {
        self::initBladeEngine();
        if (self::componentLibraryIsInstalled()) {
            require_once self::$loader;
            new ComponentLibraryInit([]);
            return self::$blade; 
        }
        return false;
    }

    public static function initBladeEngine(): void {
        self::$blade = GlobalBladeService::getInstance([BASEPATH . 'views'], BASEPATH . 'cache');
    }
}