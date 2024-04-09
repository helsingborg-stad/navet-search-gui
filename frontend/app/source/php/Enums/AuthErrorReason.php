<?php

namespace NavetSearch\Enums;

enum AuthErrorReason: int
{
    case HttpError = 0;
    case InvalidCredentials = 1;
    case Unauthorized = 2;
}
