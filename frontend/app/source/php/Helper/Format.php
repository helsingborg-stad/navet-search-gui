<?php 

namespace NavetSearch\Helper;

use DateTime;

class Format
{

  /* Social Security number */
  public static function socialSecuriyNumber($number) {
    if(strlen($number) == 12) {
      return substr($number, 0, 8) . "-" . substr($number, 8, 12);
    }
    return false;
  }

  /* Age */
  public static function getCurrentAge($pnr) {
    return (new DateTime(substr($pnr, 0, 4) . "-" . substr($pnr, 4, 2) . "-" . substr($pnr, 6, 2)))->diff(
      new DateTime('today')
    )->y;
  }

  /* PostalCode */
  public static function postalCode($postalCode) {
    return substr($postalCode, 0, 3) . " " . substr($postalCode, 3, 2); 
  }

  /* MunicipalityCode */
  public static function municipalityCode($municipalityCode) {

    $codes = [
      '25' => 'Helsingborg',
      '84' => 'Höganäs'
    ];

    if(isset($codes[$municipalityCode])) {
      return $codes[$municipalityCode] . " (" . $municipalityCode . ")";
    }

    return $municipalityCode; 
  }

  /* Capitalized Word */
  public static function capitalize($string) {
    return ucwords(mb_strtolower($string)); 
  }

  /* User object */
  public static function user($user) {
    if($user) {
      $userObject = [
        'firstname' => '', 
        'lastname' => '', 
        'administration' => $user->company ?? ''
      ]; 
      $userObject = array_merge(
        $userObject, 
        self::displayName($user->displayname, $user)
      );
      return (object) $userObject;
    }
    return false;  
  }

 /**
  * Parse display name to extract user firstname & lastname
  * @return array
  */
  public static function displayName($string, $data)
  {
      $response = ['firstname' => '', 'lastname' => ''];

      if (isset($data->sn) && !empty($data->sn)) {
          $names = explode(" - ", $string, 2);
          $response['firstname'] = trim(str_replace($data->sn, "", $names[0]));
          $response['lastname'] = $data->sn;
      } elseif (isset($data->mail) && strpos($data->mail, ".")) {
          list($response['firstname'], $response['lastname']) = explode(".", strtok($data->mail, "@"), 2);
      } else {
          $tempData = trim(explode(" - ", $string, 2)[0]);

          if (!empty($tempData)) {
              $tempData = explode(" ", $tempData);
              $response['lastname'] = $tempData[0];
              unset($tempData[0]);
              $response['firstname'] = implode(" ", $tempData);
          }
      }

      // Uppercase first letters
      $response['firstname'] = ucfirst($response['firstname']);
      $response['lastname'] = ucfirst($response['lastname']);

      return $response;
  }
}