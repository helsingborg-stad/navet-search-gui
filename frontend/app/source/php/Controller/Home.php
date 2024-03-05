<?php 

namespace NavetSearch\Controller;

use \NavetSearch\Helper\Redirect as Redirect; 
use \NavetSearch\Helper\Curl as Curl;
use \NavetSearch\Helper\Sanitize;
use \NavetSearch\Helper\User as User;
use \NavetSearch\Helper\Validate as Validate;

class Home Extends BaseController {
  public function __construct() {
    parent::__construct(__CLASS__);

    //You shall pass
    if(User::isAuthenticated()) {
      new Redirect('/sok'); 
    }
  }

  /**
   * Action method for user login.
   *
   * This method handles user login based on the provided username and password. It sets
   * default values for the required parameters and performs basic validation for the 
   * username and password. If the validation fails, it redirects to the home page with 
   * appropriate error actions. It then fetches the user using the provided credentials 
   * and validates the login response. If the login is unsuccessful, it redirects to the 
   * home page with a 'login-error' action and the username. It checks whether the user 
   * is authorized to access the application and, if not, redirects with a 'login-error-no-access'
   * action. If the user is authorized, it sets a cookie, logs in the user, and redirects 
   * to the search page. If setting the cookie fails, it redirects to the home page with 
   * a 'login-error' action.
   *
   * @param array $req An associative array of request parameters including username and password.
   *
   * @throws RedirectException If validation fails, login is unsuccessful, user is not authorized, 
   *                           or setting the cookie fails, a RedirectException is thrown to redirect
   *                           the user to the appropriate page with relevant error actions.
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

  /**
   * Fetches user information from the authentication server.
   *
   * This private method sends a POST request to the authentication server with the provided
   * username and sanitized password to fetch user information. It then checks if the response
   * is a valid user object. If an error occurs during the request, it redirects to the home 
   * page with a 'login-error' action. The method returns the fetched user information.
   *
   * @param string $username The username used for authentication.
   * @param string $password The sanitized password used for authentication.
   *
   * @return array The user information fetched from the authentication server.
   *
   * @throws RedirectException If there is an error in the response during the request,
   *                           a RedirectException is thrown to redirect the user to the home page
   *                           with a 'login-error' action.
   */
  private function fetchUser($username, $password) {
      $request = new Curl(rtrim(MS_AUTH, "/") . '/user/current', true);
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
    
  /**
   * Checks if the user is authorized to access the application.
   *
   * Matches if member of contains required key.
   *
   * @return bool The result indicating whether the user is authorized.
   */
  private function isAuthorized($login) {

    //No group lock defined
    if(!defined('AD_GROUPS')) {
      return true;
    }

    $memberOf = $this->parseMemberOf($login->memberof);

    if(array_key_exists('CN', $memberOf) && is_array(constant('AD_GROUPS')) && count(constant('AD_GROUPS'))) {
      foreach(constant('AD_GROUPS') as $group) {
        if(in_array($group, $memberOf['CN'])) {
          return true;
        }
      }
    }

    return false;
  }

  /**
   * This PHP function parses a string representing group memberships and returns an associative array
   * with group names as keys and an array of corresponding values.
   * 
   * @param memberOf The `parseMemberOf` function is designed to parse a string
   * containing group memberships. The function splits the input string by commas and then further
   * splits each part by the equal sign to extract the key-value pairs.
   * 
   * @return An associative array is being returned where the keys are extracted from the input string
   * `` and the values are arrays of corresponding values.
   */
  private function parseMemberOf($memberOf) {
    $groups = [];
    $parts = explode(',', $memberOf);
    foreach ($parts as $part) {
      $group = explode('=', $part);
      $key = $group[0];
      $value = $group[1];
      if (!isset($groups[$key])) {
        $groups[$key] = [];
      }
      $groups[$key][] = $value;
    }
    return $groups;
  }

  /**
   * Validates the login response data.
   *
   * This private method validates the login response data by checking if it is an object,
   * if it contains an error, and if the 'samaccountname' matches the provided username.
   * If the response data is not an object or contains an error, the validation fails.
   * If the 'samaccountname' matches the provided username, the validation succeeds.
   * If none of these conditions are met, the validation result is null.
   *
   * @param mixed $data The response data received from the authentication server.
   * @param string $username The username used for authentication.
   *
   * @return bool|null Returns true if the validation succeeds, false if it fails,
   *                   and null if the validation result is inconclusive.
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