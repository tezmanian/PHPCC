<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tez\PHPcc\Parser;

/**
 * Description of ParseInput
 *
 * @author halberstadt
 */
class ParseInput
{

    public $partials = [];
    public $str = NULL;
    private $nts = [];
    private $rdnts = [];

    public function __construct($str)
    {
        Parser::check_memory_limit();
        Parser::checkMaxParseReq('ParseInput', 4000);
        $this->str = $str;
    }

    public function getStr()
    {
        return $this->str;
    }

    public function setStr($str)
    {
        $this->str = $str;
    }

    public function addPartial($name, $res)
    {
        $this->partials[$name] = $res;
    }

    public function getPartial($name)
    {
        return array_key_exists($name, $this->partials) ? $this->partials[$name] : NULL;
    }

    public function pushNonTerminal($nt)
    {
        array_push($this->nts, $nt);
    }

    public function popNonTerminal($nt)
    {
        array_pop($this->nts);
    }

    public function includesNonTerminal($nt)
    {
        return in_array($nt, $this->nts);
    }

    public function redescendNonTerminal($nt)
    {
        $this->rdnts[$nt] = $nt;
    }

    public function shouldReDescend($nt)
    {
        $b = isset($this->rdnts[$nt]);
        //unset($this->rdnts[$nt]);
        return $b;
    }

    public function isBetterMatchThan($input)
    {
        return strlen($this->str) < strlen($input->str);
    }

}
