<?php 

namespace HbgStyleGuide\Controller;

use \HbgStyleGuide\Helper\Redirect as Redirect; 
use \HbgStyleGuide\Helper\User as User;

abstract class BaseController {

  public $data = [];
  protected $action = null;
  protected $validate; 

  /**
   * Define that __construct should exists in all classes inherit 
   */
  public function __construct($child) {

    //Listen for actions 
    $this->action = $this->initActionListener(); 

    //Trigger action 
    if(method_exists($child, "action".ucfirst($this->action))) {
      $this->{"action".ucfirst($this->action)}($_REQUEST); 
    }

    //Trigger global action
    if(method_exists(__CLASS__, "action".ucfirst($this->action))) {
      $this->{"action".ucfirst($this->action)}($_REQUEST); 
    }

    //Manifest data
    $this->data['assets'] = $this->getAssets();

    //Is authenticated user
    $this->data['isAuthenticated'] = User::isAuthenticated();
  }

  public function actionLogout() {
    User::logout(); 
    new Redirect('/', ['action' => 'logoutmsg']); 
  }

  /**
   * Returns data from instance
   *
   * @return array
   */
  public function getData() : array 
  {
    return (array) $this->data; 
  }

  /**
   * Inits action listener
   *
   * @return void
   */
  public function initActionListener() {
    if(isset($_GET['action'])) {
      return $this->action = str_replace(' ', '', ucwords(str_replace('-', " ", $_GET['action'])));
    }
    return $this->action = false; 
  }

  public function getAssets() {

    $revManifest = rtrim(BASEPATH,"/") . "/assets/dist/manifest.json";

    if(file_exists($revManifest)) {
      $revManifestContents = file_get_contents($revManifest);
      if($revManifestContentsDecoded = json_decode($revManifestContents)) {
        return $revManifestContentsDecoded;
      }
    }
    return false;
  }
  
}

