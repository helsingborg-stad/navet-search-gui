<?php

namespace NavetSearch\Controller;

use \NavetSearch\Interfaces\AbstractServices as AbstractServices;

class E404 extends BaseController
{
  public function __construct(AbstractServices $services)
  {
    parent::__construct(__CLASS__, $services);
  }
}
