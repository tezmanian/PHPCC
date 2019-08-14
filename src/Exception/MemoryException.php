<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tez\PHPcc\Exception;

use RuntimeException;
use Throwable;

/**
 * Description of MemoryException
 *
 * @author halberstadt
 */
class MemoryException extends RuntimeException
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
