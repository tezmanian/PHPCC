<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tez\PHPcc\Parser;

class EregSymbol extends Parser
{
    protected $sym;
    private $preg = NULL;

    public function __construct($sym)
    {
        parent:: __construct();
        $bars = explode('/', $sym);
        $mods = array_pop($bars);
        array_shift($bars);
        $spaces = '[\s\t\n]*';
        $this->preg = '/^' . $spaces . '(' . implode('/', $bars) . ')' . $spaces . '/' . $mods;
        $this->sym = $sym;
    }

    public function parse($tks)
    {
        Parser::check_memory_limit();
        $spaces = '[\s\t\n]*';
        if (preg_match($this->preg, $tks->str, $matches)) {
            return array(ParseResult::match($matches[1]), new ParseInput(substr($tks->str, strlen($matches[0]))));
        } else {
            $this->setError(array((string)strlen(preg_replace('/^' . $spaces . '/', '', $tks->str)) => $this->sym));
            return array(ParseResult::fail(), $tks);
        }
    }

    public function print_tree()
    {
        return $this->sym;
    }

}