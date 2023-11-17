<?php


/**
 * This is the bootstrap of the app
 * 1. Defines root path of the app
 * 2. Requires config file
 * 3. Requires and initializes autoloader
 * 4. Initiates local component library if installed
 */

define('BASEPATH', dirname(__FILE__) . '/');

require_once BASEPATH . 'vendor/autoload.php';
require_once BASEPATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once BASEPATH . 'config.php';

// Instantiate and register the autoloader
$loader = new HbgStyleGuide\Vendor\Psr4ClassLoader();
$loader->addPrefix('HbgStyleGuide', BASEPATH);
$loader->addPrefix('HbgStyleGuide', BASEPATH . 'source/php/');
$loader->addPrefix('BladeComponentLibrary', BASEPATH . 'source/library/src');
$loader->register();

//Register view path

//Load component library
$blade = \HbgStyleGuide\Helper\Enviroment::loadInstalledComponentLibrary();