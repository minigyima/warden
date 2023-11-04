<?php

namespace Minigyima\Warden\Errors;

class ColdCacheAlreadySetException extends WardenException
{
    protected string $defaultMessage = 'Cold cache already set';
    protected int $defaultCode = 500;
}
