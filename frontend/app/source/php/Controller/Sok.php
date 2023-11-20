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
        'pnr' => Sanitize::number($req->pnr) 
      ]); 
    }
  }

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

  private function createReadableText($data, $pnr) {
    return $data->givenName . " " . $data->familyName . " är " . Format::getCurrentAge($pnr). " år gammal och är bosatt på " . Format::capitalize($data->address->streetAddress) . " i ". Format::capitalize($data->address->addressLocality) . " kommun."; 
  }

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