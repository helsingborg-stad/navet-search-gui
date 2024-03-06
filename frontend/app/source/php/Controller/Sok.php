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

    //Santitize
    $req->pnr = Sanitize::number($req->pnr);

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

      //Get family relations
      $this->data['searchResultFamilyRelations'] = $this->searchFamilyRelations(
        $this->data['searchFor']
      );

      //Get property data
      $this->data['searchResultPropertyData'] = $this->getPropertyData(
        $this->data['searchFor']
      );

      $this->data['basicData'] = [];

      if($this->isDeregistered($person)) {

        //Create deregistration state
        $this->data['isDeregistered'] = true;
        $this->data['deregistrationReason'] = $this->getDeristrationSentence(
          $person->deregistrationReason
        );

        $this->data['basicData']  = $this->createBasicDataList(
          $person, 
          Format::socialSecuriyNumber($req->pnr),
          $this->getCivilStatus($this->data['searchFor'])
        );

      } else {

        //Is not deregistered
        $this->data['isDeregistered'] = false;

        //Request basic data table
        $this->data['basicData']  = $this->createBasicDataList(
          $person, 
          Format::socialSecuriyNumber($req->pnr),
          $this->getCivilStatus($this->data['searchFor'])
        );

        //Request the readable string
        $this->data['readableResult'] = $this->createReadableText(
          $person, 
          Format::socialSecuriyNumber($req->pnr)
        );

        //Request adress data table
        $this->data['adressData'] = $this->createAdressDataList(
          $person
        );
      }

    } else {
      new Redirect('/sok/', [
        'action' => 'search-no-hit',
        'pnr' => Format::socialSecuriyNumber($req->pnr),
        'code' => Validate::getStatusCode($person)
      ]); 
    }
  }

  /**
   * Checks if a person is deregistered.
   *
   * @param object $person The person object to check.
   * @return bool Returns true if the person is deregistered, false otherwise.
   */
  public function isDeregistered($person) {
    if(isset($person->deregistrationCode)) {
      return true;
    }
    return false;
  }

  /**
   * Returns a sentence indicating that a person has been deregistered and their status.
   *
   * @param string $reason The reason for deregistration.
   * @return string The sentence indicating the deregistration status.
   */
  public function getDeristrationSentence($reason) {
    return "Personen är avregistrerad och har fått statusen: " . $reason; 
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
   * Search for family relations using the specified personal number (PNR) and retrieve relevant information.
   *
   * @param string $pnr The personal number for which family relations are to be searched.
   * @param string $relevantKey The key in the API response containing relevant family relation data. Default is 'relationsToFolkbokforda'.
   *
   * @return false|object Returns false if no relevant data is found, otherwise returns an object with processed family relations data.
   *
   * @throws \Exception If there is an issue with the Curl request or processing the API response.
   */
  private function searchFamilyRelations($pnr, $relevantKey = 'relationsToFolkbokforda') {
    $request = new Curl(MS_NAVET . '/lookUpFamilyRelations', true);
    $request->setHeaders([
        'X-ApiKey' => MS_NAVET_AUTH
    ]);
    $response = $request->post([
      "personNumber"=> Sanitize::number($pnr),
      "searchedBy"  => User::get()->samaccountname
    ]);

    $stack = false;
    $predefinedCodes = ['FA', 'MO', 'VF', 'B', 'M'];

    if (!empty($response->{$relevantKey}) && is_array($response->{$relevantKey})) {
        $stack = [];

        foreach ($response->{$relevantKey} as $item) {
var_dump($item);
            $item = Format::convertToArray($item);

            $identityNumber = $item['identityNumber'];

            // Initialize an empty array for the identity number
            if(!isset($stack[$identityNumber])) {
              $stack[$identityNumber] = array_fill_keys($predefinedCodes, false);
            }
            
            // Set the value to true for the corresponding code
            $stack[$identityNumber][
              $item['type']['code']
            ] = !empty($item['custodyDate']) ? Format::date($item['custodyDate']) : true;
        }
    }

    if($stack === false) {
      return false;
    }
 
    return (object) $this->createRelationsDataList($stack);
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
    if(empty((array) $data->address)) {
      return $data->givenName . " " . $data->familyName . " är " . Format::getCurrentAge($pnr). " år gammal och har ingen registrerad bostadsadress."; 
    }
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
  private function createBasicDataList($data, $pnr, $civilStatus) {
    return [
      ['columns' => [
        'Personnummer:', 
        $pnr ?? ''
      ]],
      ['columns' => [
        'Kön:', 
        Format::sex($pnr, true) ?? ''
      ]],
      ['columns' => [
        'Civilstatus:', 
        $civilStatus['description'] ? $civilStatus['description'] . " " . Format::addPharanthesis($civilStatus['date']) : ''
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
    if(empty((array) $data->address)) {
      return false;
    }

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

  /**
   * Creates a property data list based on the provided data.
   *
   * @param object $data The data containing property registration history.
   * @return array|false The property data list or false if the data is invalid or empty.
   */
  private function getCivilStatus($pnr, $relevantKey = 'civilStatus') {

    $request = new Curl(MS_NAVET . '/lookUpFamilyRelations', true);
    $request->setHeaders([
        'X-ApiKey' => MS_NAVET_AUTH
    ]);
    $response = $request->post([
      "personNumber"=> Sanitize::number($pnr),
      "searchedBy"  => User::get()->samaccountname
    ]);

    if(!isset($response->{$relevantKey})) {
      return false;
    }

    if(empty((array) $response->{$relevantKey})) {
      return false;
    }

    return [
      'code' => $response->{$relevantKey}->code,
      'description' => $response->{$relevantKey}->description,
      'date' => Format::date($response->{$relevantKey}->date)
    ];
  }

  /**
   * Creates a property data list based on the provided data.
   *
   * @param object $data The data containing property registration history.
   * @return array|false The property data list or false if the data is invalid or empty.
   */
  private function getPropertyData($pnr, $relevantKey = 'propertyRegistrationHistory') {

    $request = new Curl(MS_NAVET . '/lookUpFamilyRelations', true);
    $request->setHeaders([
        'X-ApiKey' => MS_NAVET_AUTH
    ]);
    $response = $request->post([
      "personNumber"=> Sanitize::number($pnr),
      "searchedBy"  => User::get()->samaccountname
    ]);

    if(!isset($response->{$relevantKey})) {
      return false;
    }

    if(empty((array) $response->{$relevantKey})) {
      return false;
    }

    $list = [];
    foreach($response->{$relevantKey} as $property) {
      $list[] = [
        'columns' => [
          $property->property->designation ?? '',
          $property->type->description ?? '',
          Format::date($property->registrationDate) ?? '',
          $property->municipalityCode ?? '',
          $property->countyCode ?? '',
        ]
      ];
    }

    return [
      'title' => "Adresshistorik",
      'headings' => ['Fastighetsbeteckning', 'Händelse', 'Datum', 'Kommunkod', 'Län'],
      'list' => $list
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
  private function createRelationsDataList($data) {
var_dump($data);
    $stack = []; 
    foreach($data as $identityNumber => $relations) {
      $stack[] = [
        'columns' => [
          '<a href="/sok/?action=sok&pnr='.$identityNumber.'">' . Format::socialSecuriyNumber($identityNumber) . '</a>',
          $relations['FA'] ? '✕' . Format::addPharanthesis(Sanitize::string($relations['FA'])) : '-',
          $relations['MO'] ? '✕' . Format::addPharanthesis(Sanitize::string($relations['MO'])) : '-',
          $relations['VF'] ? '✕' . Format::addPharanthesis(Sanitize::string($relations['VF'])) : '-',
          $relations['B'] ? '✕' . Format::addPharanthesis(Sanitize::string($relations['B'])) : '-',
          $relations['M'] ? '✕' . Format::addPharanthesis(Sanitize::string($relations['M'])) : '-'
        ] 
      ];
    }

    return $stack;
  }
}