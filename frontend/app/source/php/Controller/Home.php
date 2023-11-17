<?php 

namespace NavetSearch\Controller;

use \NavetSearch\Helper\Redirect as Redirect; 
use \NavetSearch\Helper\Curl as Curl;
use NavetSearch\Helper\Sanitize;
use \NavetSearch\Helper\User as User;
use \NavetSearch\Helper\Validate as Validate;

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
      $request = new Curl(rtrim(MS_AUTH, "/") . '/user/current', false);
      $response = $request->post([
        'username' => $username,
        'password' => Sanitize::password($password)
      ]);

      //Check if is valid response
      if(isset($response->error)) {
        new Redirect('/', ['action' => 'login-error']); 
      }

      //Return login response
      return array_pop($response);
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