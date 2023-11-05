<?php

namespace Minigyima\Warden\Errors;


class WardenAuthorizationException extends WardenException
{
    protected $defaultMessage = 'Warden authorization failed';
    protected $defaultCode = 403;
}
