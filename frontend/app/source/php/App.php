<?php

namespace NavetSearch;

use \NavetSearch\View;
use ComponentLibrary\Init as ComponentLibraryInit;

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
    public function __construct(array $config = array())
    {
        //Setup constants
        $this->setUpEnvironment();

        //Load config
        $this->configure($config);

        //Load current page
        $this->loadPage(
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
          'MS_NAVET_AUTH' => getenv('MS_NAVET_AUTH'),
          'ENCRYPT_VECTOR' => getenv('ENCRYPT_VECTOR'),
          'ENCRYPT_KEY' => getenv('ENCRYPT_KEY'),
          'PREDIS' => getenv('PREDIS'),
          'DEBUG' => getenv('DEBUG'),
          'AD_GROUPS' => getenv('AD_GROUPS')
        );
        
        //Fallback to default
        foreach($env as $key => $item) {
          if($item === false) {
            if(isset($config[$key]) && is_object($config[$key])) {
                $config[$key] = (array) $config[$key];
            }
            $env[$key] = $config[$key] ?? false;
          }
        }

        //Set
        foreach($env as $key => $item) {
            define($key, $item); 
        }
    }

    private function setUpEnvironment() {
        define('VIEWS_PATH', BASEPATH . 'views/');
        define('BLADE_CACHE_PATH', BASEPATH . 'cache/');
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
    public function loadPage($page, $action)
    {
        $blade = (new ComponentLibraryInit([]))->getEngine();

        //Current page 
        $data['pageNow']                        = $page;
        $data['action']                         = $action; 

        //Render page 
        $view = new \NavetSearch\View();

        return $view->show(
            $page,
            $data,
            $blade
        );
    }
}