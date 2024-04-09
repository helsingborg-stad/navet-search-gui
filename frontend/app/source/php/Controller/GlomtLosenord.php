<?php

namespace NavetSearch\Controller;

use \NavetSearch\Interfaces\AbstractServices as AbstractServices;

class GlomtLosenord extends BaseController
{
  public function __construct(AbstractServices $services)
  {
    parent::__construct(__CLASS__, $services);
  }
}
