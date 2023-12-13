<?php 

namespace NavetSearch\Controller;

use \NavetSearch\Helper\Redirect as Redirect; 
use \NavetSearch\Helper\Curl as Curl;
use \NavetSearch\Helper\User as User;
use \NavetSearch\Helper\Sanitize as Sanitize;
use \NavetSearch\Helper\Validate as Validate;
use \NavetSearch\Helper\Format as Format;

class Sok Extends BaseController {
  
  /**
   * Class constructor for the specified class.
   *
   * Initializes the object by calling the parent constructor with the class name.
   * Additionally, it checks if the user is authenticated and redirects uninlogged users
   * to the home page with a 'not-authenticated' action. The current user's information
   * is then retrieved and stored in the object's data property.
   *
   * @throws RedirectException If the user is not authenticated, a RedirectException is thrown.
   */
  public function __construct() {
    parent::__construct(__CLASS__);

    //Prevent uninlogged users 
    if(!User::isAuthenticated()) {
      new Redirect('/', ['action' => 'not-authenticated']); 
    }
    
    //Get current user
    $this->data['user'] = User::get();
  }

  /**
   * Action method for searching with specified parameters.
   *
   * This method handles the search action with the provided parameters. It validates
   * the correctness of the provided personal number (pnr) format using the Validate class.
   * If the pnr is not in the correct format, it redirects to the search page with the 
   * 'search-pnr-malformed' action and the sanitized pnr. If the pnr is valid, it sanitizes
   * the input and retrieves data for the specified person. If the search is successful, 
   * it parses the data into readable formats, such as readable text, basic data list, 
   * and address data list. If the search is not successful, it redirects to the search page 
   * with the 'search-no-hit' action and the sanitized pnr.
   *
   * @param array $req An associative array of request parameters.
   *
   * @throws RedirectException If the pnr is not in the correct format or if the search is unsuccessful,
   *                           a RedirectException is thrown to redirect the user to the appropriate page.
   */
  public function actionSok(array $req) {
    $req = (object) array_merge([
      'pnr' => false
    ], $req);

    //Validate that pnr is correct format
    if(!Validate::pnr($req->pnr)) {
      new Redirect(
        '/sok/', 
        [
          'action' => 'search-pnr-malformed',
          'pnr' => Sanitize::number($req->pnr) 
        ]
      );
    }

    //Sanitize pnr
    $this->data['searchFor'] = Format::socialSecuriyNumber(
      Sanitize::number($req->pnr)
    );

    //Get data
    $person = $this->searchPerson(
      $this->data['searchFor']
    );

    $this->data['searchResult'] = !Validate::isErrorResponse($person);

    //Validate, if ok. Parse data
    if($this->data['searchResult']) {

      $this->data['readableResult'] = $this->createReadableText(
        $person, 
        Format::socialSecuriyNumber($req->pnr)
      );
  
      $this->data['basicData']  = $this->createBasicDataList(
        $person, 
        Format::socialSecuriyNumber($req->pnr)
      );
  
      $this->data['adressData'] = $this->createAdressDataList($person);

    } else {
      new Redirect('/sok/', [
        'action' => 'search-no-hit',
        'pnr' => Sanitize::number($req->pnr),
        'code' => Validate::getStatusCode($person)
      ]); 
    }
  }

  /**
   * Action method for searching with specified parameters.
   *
   * This method handles the search action with the provided parameters. It validates
   * the correctness of the provided personal number (pnr) format using the Validate class.
   * If the pnr is not in the correct format, it redirects to the search page with the 
   * 'search-pnr-malformed' action and the sanitized pnr. If the pnr is valid, it sanitizes
   * the input and retrieves data for the specified person. If the search is successful, 
   * it parses the data into readable formats, such as readable text, basic data list, 
   * and address data list. If the search is not successful, it redirects to the search page 
   * with the 'search-no-hit' action and the sanitized pnr.
   *
   * @param array $req An associative array of request parameters.
   *
   * @throws RedirectException If the pnr is not in the correct format or if the search is unsuccessful,
   *                           a RedirectException is thrown to redirect the user to the appropriate page.
   */
  private function searchPerson($pnr) {
    $request = new Curl(MS_NAVET . '/lookUpAddress', true);
    $request->setHeaders([
        'X-ApiKey' => MS_NAVET_AUTH
    ]);
    $response = $request->post([
      "personNumber"=> Sanitize::number($pnr),
      "searchedBy"  => User::get()->samaccountname
    ]);
    return (object) $response;
  }

  /**
   * Creates readable text based on the provided data and personal number (pnr).
   *
   * This private method takes in data representing a person and their personal number (pnr)
   * to construct a readable text string. The resulting text includes the person's full name,
   * current age derived from the pnr, and residential address information in a formatted manner.
   *
   * @param object $data An object containing information about the person.
   * @param string $pnr The personal number (pnr) used to calculate the person's current age.
   *
   * @return string The constructed readable text string with person's name, age, and address.
   */
  private function createReadableText($data, $pnr) {
    return $data->givenName . " " . $data->familyName . " är " . Format::getCurrentAge($pnr). " år gammal och är bosatt på " . Format::capitalize($data->address->streetAddress) . " i ". Format::capitalize($data->address->addressLocality) . "."; 
  }

  /**
   * Creates a basic data list based on the provided data and personal number (pnr).
   *
   * This private method takes in data representing a person and their personal number (pnr)
   * to construct a basic data list. The resulting list includes key-value pairs for essential
   * information such as personal number, first name, last name, and additional names.
   *
   * @param object $data An object containing information about the person.
   * @param string $pnr The personal number (pnr) associated with the person.
   *
   * @return array An array representing a basic data list with key-value pairs.
   */
  private function createBasicDataList($data, $pnr) {
    return [
      ['columns' => [
        'Personnummer:', 
        $pnr ?? ''
      ]],
      ['columns' => [
        'Förnamn:', 
        $data->givenName ?? ''
      ]],
      ['columns' => [
        'Efternamn:', 
        $data->familyName ?? ''
      ]],
      ['columns' => [
        'Övriga namn:', 
        $data->additionalName ?? ''
      ]],
    ]; 
  }

  /**
   * Creates an address data list based on the provided data.
   *
   * This private method takes in data representing a person and constructs an address data list.
   * The resulting list includes key-value pairs for essential address information such as municipality,
   * postal code, and street address. The address information is formatted for consistency.
   *
   * @param object $data An object containing information about the person's address.
   *
   * @return array An array representing an address data list with key-value pairs.
   */
  private function createAdressDataList($data) {
    return [
      ['columns' => [
        'Postort:', 
        Format::capitalize($data->address->addressLocality) ?? ''
      ]],
      ['columns' => [
        'Postnummer:', 
        Format::postalCode($data->address->postalCode) ?? ''
      ]],
      ['columns' => [
        'Gatuadress:', 
        Format::capitalize($data->address->streetAddress) ?? ''
      ]]
    ]; 
  }
}