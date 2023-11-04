<?php

namespace Minigyima\Warden\Errors;

use Exception;
use Throwable;

class WardenException extends Exception
{
    protected int $defaultCode = 500;
    protected string $defaultMessage = 'Undefined Warden exception.';

    public function __construct(
		string $message = 'Undefined Warden exception.',
		int $code = 500,
		?Throwable $previous = null
	) {

		$message = $message != 'Undefined Warden exception.' ? $message : $this->defaultMessage ?? $message;
		$code = $code != 500 ? $code : $this->defaultCode ?? $code;

		return parent::__construct($message, $code, $previous);
	}
}
