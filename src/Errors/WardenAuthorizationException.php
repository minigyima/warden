<?php

namespace Minigyima\Warden\Errors;


class WardenAuthorizationException extends WardenException
{
    protected $message = 'Warden authorization failed';
    protected $code = 403;
}
