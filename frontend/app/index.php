<?php

//Run application
require_once 'Bootstrap.php';

//Get config & run, or run without config
$configFile = __DIR__ . '/../config.json';
if(file_exists($configFile)) {
	new \NavetSearch\App(
		(array) json_decode(file_get_contents($configFile))
	);
} else {
	new \NavetSearch\App();
}
