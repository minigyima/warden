<?php

namespace Minigyima\Warden\Errors;

class InvalidModeException extends WardenException
{
    protected string $defaultMessage = 'Invalid mode for this operation';
    protected int $defaultCode = 500;
}
