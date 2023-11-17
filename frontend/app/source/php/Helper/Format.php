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

  /* Capitalized Word */
  public static function capitalize($string) {
    return ucwords(mb_strtolower($string)); 
  }

}