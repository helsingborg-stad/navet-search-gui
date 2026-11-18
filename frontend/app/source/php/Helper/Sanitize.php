<?php

declare(strict_types=1);

namespace NavetSearch\Helper;

class Sanitize
{

  /** Make string numbers only */
  public static function number($string)
  {
    return preg_replace('/[^0-9.]+/', '', (string) $string);
  }

  /** Always return a string */
  public static function string(mixed $string)
  {
    if (is_string($string)) {
      return $string;
    }

    // Resembles a bool
    if ($string == 1 || $string == 0) {
      return "";
    }

    // Anything else, empty string
    return "";
  }

  /**
   * Preserve a password verbatim.
   *
   * Request::post() JSON-encodes the payload, so escaping or stripping
   * characters here changes the credential received by Active Directory.
   */
  public static function password(string $password): string
  {
    return $password;
  }
}
