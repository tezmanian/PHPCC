<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tez\PHPcc\Parser;

/**
 * Description of ParseResult
 *
 * @author halberstadt
 */
class ParseResult
{

    public static function fail()
    {
        Parser::check_memory_limit();
        //return ParseResult::match(FALSE);
        $pr = new ParseResult();
        $pr->failed = true;
        return $pr;
    }

    public static function match($result)
    {
        Parser::check_memory_limit();
        $pr = new ParseResult();
        $pr->match = $result;
        return $pr;
    }

    public static function lambda()
    {
        Parser::check_memory_limit();
        $pr = new ParseResult();
        $pr->lambda = true;
        $pr->match = null;
        return $pr;
    }

    public function isLambda()
    {
        Parser::check_memory_limit();
        return isset($this->lambda);
    }

    public function failed()
    {
        Parser::check_memory_limit();
        return isset($this->failed);
    }

}
