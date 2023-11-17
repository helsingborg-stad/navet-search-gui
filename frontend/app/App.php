<?php

namespace HbgStyleGuide;

use \HbgStyleGuide\View;

/**
 * Class App
 * @package HbgStyleGuide
 */
class App
{
    protected $default = 'home'; //Home
    private   $blade; // Blade

    /**
     * App constructor.
     * @param $blade
     */
    public function __construct($blade)
    {
        //Load config
        require __DIR__ . '/config/microservice.php'; 

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
        $data['componentLibraryIsInstalled']    = \HbgStyleGuide\Helper\Enviroment::componentLibraryIsInstalled();
        $data['isLocalDomain']                  = \HbgStyleGuide\Helper\Enviroment::isLocalDomain();
        
        //Render page 
        $view = new \HbgStyleGuide\View();

        return $view->show(
            $page,
            $data,
            $blade
        );
    }
}
