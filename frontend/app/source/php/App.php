<?php

namespace NavetSearch;

use \NavetSearch\View;

/**
 * Class App
 * @package NavetSearch
 */
class App
{
    protected $default = 'home'; //Home

    /**
     * App constructor.
     * @param $blade
     */
    public function __construct($blade, array $config = array())
    {
        //Setup constants
        $this->setUpEnvironment();

        //Load config
        $this->configure($config);

        //Load current page
        $this->loadPage(
            $blade, 
            $this->getCurrentPath(),
            $this->getAction()
        );
    }

    /**
     * Configure the application environment variables.
     *
     * This function retrieves environment variables from the system or uses default values from the provided configuration.
     *
     * @param array $config An associative array containing values for environment variables.
     *
     * @return void
     */
    private function configure($config) {
        //Get env vars
        $env = array(
          'MS_AUTH' => getenv('MS_AUTH'), 
          'MS_NAVET' => getenv('MS_NAVET'),
          'MS_NAVET_AUTH' => getenv('MS_NAVET_AUTH')
        );
        
        //Fallback to default
        foreach($env as $key => $item) {
          if($item === false) {
            $env[$key] = $config[$key] ?? false;
          }
        }

        //Validate
        if(count(array_filter($env)) != 3) {
            die("Configuration incomplete, please define env-variables or via config.json according to documentation."); 
        }

        //Set
        foreach($env as $key => $item) {
            define($key, $item); 
        }
    }

    private function setUpEnvironment() {
        define('VIEWS_PATH', BASEPATH . 'views/');
        define('CACHE_PATH', BASEPATH . 'cache/');
        define('LOCAL_DOMAIN', '.local');
    }

    /** 
     * Find out the current page 
     */
    private function getCurrentPath() {
        $url = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
        $url = rtrim($url, '/'); 
        return ($url !== "") ? $url : $this->default;
    }

    /**
     * Get the requested action data
     */
    private function getAction() {
        return isset($_GET['action']) ? $_GET['action'] : false;
    }

    /**
     * Loads a page and it's navigation
     * @return bool Returns true when the page is loaded
     */
    public function loadPage($blade, $page, $action)
    {
        //Current page 
        $data['pageNow']                        = $page;
        $data['action']                         = $action; 

        //Component library
        $data['componentLibraryIsInstalled']    = \NavetSearch\Helper\Enviroment::componentLibraryIsInstalled();
        $data['isLocalDomain']                  = \NavetSearch\Helper\Enviroment::isLocalDomain();
        
        //Render page 
        $view = new \NavetSearch\View();

        return $view->show(
            $page,
            $data,
            $blade
        );
    }
}
