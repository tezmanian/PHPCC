<?php

namespace Tez\PHPcc\Parser;

class Symbol extends EregSymbol
{

    public function __construct($ss)
    {
        parent:: __construct('/' . preg_quote($ss) . '/');
        $this->sym = '"' . $ss . '"';
    }

}
