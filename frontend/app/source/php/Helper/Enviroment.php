<?php

namespace NavetSearch\Helper;

use HelsingborgStad\GlobalBladeService\GlobalBladeService;
use ComponentLibrary\Init as ComponentLibraryInit;

/* The `class Enviroment` in the provided PHP code is a helper class that contains methods related to
checking and loading a component library using the Blade template engine. Here is a summary of what
the class is doing: */
class Enviroment
{
    private static $loader = BASEPATH . "vendor/helsingborg-stad/component-library/load.php";
    private static $blade;

    /**
     * The function checks if a component library is installed by verifying the existence of a specific
     * file.
     * 
     * @return bool a boolean value, either `true` if the component library is installed (if the file
     * specified by `self::` exists) or `false` if it is not installed.
     */
    public static function componentLibraryIsInstalled(): bool
    {
        if (file_exists(self::$loader)) {
            return true; 
        }
        return false;
    }

    /**
     * The function `loadInstalledComponentLibrary` initializes the Blade engine and loads the
     * component library if it is installed.
     * 
     * @return The method `loadInstalledComponentLibrary` is returning the Blade engine instance
     * `self::` if the component library is installed. If the component library is not installed,
     * it returns `false`.
     */
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

    /**
     * The function initializes the Blade template engine in PHP by setting up the view directories and
     * cache location.
     */
    public static function initBladeEngine(): void {
        self::$blade = GlobalBladeService::getInstance(
            [BASEPATH . 'views'],
            BASEPATH . 'cache'
        );
    }
}