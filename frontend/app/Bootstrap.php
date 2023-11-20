<?php


/**
 * This is the bootstrap of the app
 * 1. Defines root path of the app
 * 2. Requires config file
 * 3. Requires and initializes autoloader
 * 4. Initiates local component library if installed
 */

define('BASEPATH', dirname(__FILE__) . '/');


require_once BASEPATH . 'config.php';


//Load config
require __DIR__ . '/config/microservice.php'; 

// Register the autoloader
if (file_exists(BASEPATH . 'vendor/autoload.php')) {
  require BASEPATH . '/vendor/autoload.php';
}

//Load component library
$blade = \NavetSearch\Helper\Enviroment::loadInstalledComponentLibrary();