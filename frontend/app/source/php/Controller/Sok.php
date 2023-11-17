<?php 

namespace NavetSearch\Controller;

use \NavetSearch\Helper\Redirect as Redirect; 
use \NavetSearch\Helper\Curl as Curl;
use \NavetSearch\Helper\User as User;
use \NavetSearch\Helper\Sanitize as Sanitize;
use \NavetSearch\Helper\Validate as Validate;
use \NavetSearch\Helper\Format as Format;

class Sok Extends BaseController {
  
  public function __construct() {
    parent::__construct(__CLASS__);

    //Prevent uninlogged users 
    if(!User::isAuthenticated()) {
      new Redirect('/', ['action' => 'not-authenticated']); 
    }
    
    //Get current user
    $this->data['user'] = User::get();
    
  }

  public function actionSok(array $req) {

    $req = (object) array_merge([
      'pnr' => false
    ], $req);

    //Validate that pnr is correct format
    if(!Validate::pnr($req->pnr)) {
      new Redirect('/sok/', ['action' => 'search-pnr-malformed']); 
    }

    //Sanitize pnr
    $this->data['searchFor']      = Format::socialSecuriyNumber(
      Sanitize::number($req->pnr)
    );

    //Get data
    $this->data['searchResult'] = (bool) $person = $this->searchPerson(
      $this->data['searchFor']
    );

    $this->data['readableResult'] = $this->createReadableText(
      $person, 
      Format::socialSecuriyNumber($req->pnr)
    );

    $this->data['basicData']  = $this->createBasicDataList(
      $person, 
      Format::socialSecuriyNumber($req->pnr)
    );

    $this->data['adressData'] = $this->createAdressDataList($person);
  }

  private function searchPerson($pnr) {
    $curl = new Curl('POST', MS_NAVET . '/lookUpAddress', 
      [
        "personNumber"=> Sanitize::number($pnr)
      ],
      'json',
      [
        'X-ApiKey' => MS_NAVET_AUTH,
        'accept' => 'application/json'
      ]
    );

    if($curl->isValid) {
      return $curl->response;
    }

    return false;
  }

  private function createReadableText($data, $pnr) {
    return $data->givenName . " " . $data->additionalName . " " . $data->familyName . " är " . Format::getCurrentAge($pnr). " år gammal och är bosatt på " . Format::capitalize($data->address->streetAddress) . " i ". Format::capitalize($data->address->addressLocality) . " kommun."; 
  }

  private function createBasicDataList($data, $pnr) {
    return [
      ['columns' => [
        'Personnummer:', 
        $pnr ?? ''
      ]],
      ['columns' => [
        'Namn:', 
        $data->givenName ?? ''
      ]],
      ['columns' => [
        'Mellannamn:', 
        $data->additionalName ?? ''
      ]],
      ['columns' => [
        'Efternamn:', 
        $data->familyName ?? ''
      ]]
    ]; 
  }

  private function createAdressDataList($data) {
    return [
      ['columns' => [
        'Kommun:', 
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