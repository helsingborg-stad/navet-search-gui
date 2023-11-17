<?php 

namespace HbgStyleGuide\Helper;

class Sanitize
{

  /** Make string numbers only */
  public static function number($string) {
    return preg_replace('/[^0-9.]+/', '', $string);
  }

  /** Santitize password to comply with active directory issues */
  public static function password($password) {
    $password = stripslashes($password);
    $password = preg_replace('/(["\/\\\])/', '\\\\$1', $password);
    return $password;
  }
}