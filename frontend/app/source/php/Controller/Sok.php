<?php

namespace NavetSearch\Controller;

use \NavetSearch\Helper\Redirect as Redirect;
use \NavetSearch\Helper\Sanitize as Sanitize;
use \NavetSearch\Helper\Validate as Validate;
use \NavetSearch\Helper\Format as Format;
use \NavetSearch\Interfaces\AbstractServices;

class Sok extends BaseController
{
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
  public function __construct(AbstractServices $services)
  {
    parent::__construct(__CLASS__, $services);

    //Prevent uninlogged users 
    if (!$services->getSessionService()->isValid()) {
      new Redirect('/', ['action' => 'not-authenticated']);
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
  public function actionSok(array $req)
  {
    $req = (object) array_merge([
      'pnr' => false
    ], $req);

    //Santitize
    $req->pnr = Sanitize::number($req->pnr);

    //Validate that pnr is correct format
    if (!Validate::pnr($req->pnr)) {
      new Redirect(
        '/sok/',
        [
          'action' => 'search-pnr-malformed',
          'pnr' => $req->pnr
        ]
      );
    }
    //Get data
    $this->data = $this->services->getSearchService()->find(
      $req->pnr
    );

    //Validate, if ok. Parse data
    if (!$this->data['searchResult']) {
      new Redirect('/sok/', [
        'action' => 'search-no-hit',
        'pnr' => Format::socialSecuriyNumber($req->pnr),
        'code' => 200,
      ]);
    }
  }
}
