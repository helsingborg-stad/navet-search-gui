<?php 

namespace HbgStyleGuide\Controller;

use \HbgStyleGuide\Helper\Redirect as Redirect; 
use \HbgStyleGuide\Helper\Curl as Curl;
use HbgStyleGuide\Helper\Sanitize;
use \HbgStyleGuide\Helper\User as User;
use \HbgStyleGuide\Helper\Validate as Validate;

class Home Extends BaseController {
  public function __construct() {
    parent::__construct(__CLASS__);
  }

  /**
   * Login action
   *
   * @param array $req
   * @return void
   */
  public function actionLogin(array $req) {
    //Alwasy set vars that should be used
    $req = (object) array_merge([
      'username' => false,
      'password' => false
    ], $req); 
    
    //Basic validation of credentials
    if(!Validate::username($req->username)) {
      new Redirect('/', ['action' => 'login-error-username']); 
    }

    if(!Validate::password($req->password)) {
      new Redirect('/', ['action' => 'login-error-password']); 
    }
    
    //Fetch user
    $login = $this->fetchUser(
      $req->username, 
      $req->password
    ); 

    //Check that response is ok
    if(!$this->validateLogin($login, $req->username)) {
      new Redirect('/', [
        'action' => 'login-error', 
        'username' => $req->username
      ]); 
    }

    //Check if user is allowed to access application
    if(!$this->isAuthorized($login)) {
      new Redirect('/', ['action' => 'login-error-no-access']); 
    }

    //Set cookie & redirect to search
    if(User::set($login)) {
      new Redirect('/sok'); 
    }

    //Failed to set cookie
    new Redirect('/', ['action' => 'login-error']); 
  }

  private function fetchUser($username, $password) {
      //Create login post data
      $data = array(
          'username' => $username,
          'password' => Sanitize::password($password)
      );

      //Call auth API
      $curl = new Curl(
        'POST', 
        rtrim(MS_AUTH, "/") . '/user/current', 
        $data, 
        'json', 
        array('Content-Type: application/json')
      ); 

      //Check if is valid response
      if(!$curl->isValid && isset($curl->response->{0})) {
        new Redirect('/', ['action' => 'login-error']); 
      }

      //Return login response
      return $curl->response->{0};
  }
    
  private function isAuthorized() {
    return true;
  }

  /**
   * Validate that this is a true callback
   * @return bool / null
   */
  private function validateLogin($data, $username)
  {
    if (!is_object($data)) {
        return false;
    }

    if (isset($data->error)) {
        return false;
    }

    if (isset($data->samaccountname) && strtolower($data->samaccountname) == strtolower($username)) {
        return true;
    }

    return null;
  }
}