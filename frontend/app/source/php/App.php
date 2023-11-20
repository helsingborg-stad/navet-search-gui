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
    public function __construct($blade)
    {
        //Load current page
        $this->loadPage(
            $blade, 
            $this->getCurrentPath(),
            $this->getAction()
        );
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
