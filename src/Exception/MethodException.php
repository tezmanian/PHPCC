<?php

namespace Tez\PHPcc\Exception;

use RuntimeException;
use Throwable;

/**
 * Description of MethodException
 *
 * @author halberstadt
 */
class MethodException extends RuntimeException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
