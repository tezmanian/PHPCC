<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tez\PHPcc\Parser;

/**
 * Description of MultiOneParser
 *
 * @author halberstadt
 */
class MultiOneParser extends MultiParser
{

    public function parse($tks)
    {
        $res = parent::parse($tks);
        if (count($res[0]) == 0) {
            parent::setError($this->buffer);
            return array(ParseResult::fail(), $tks);
        } else {
            return $res;
        }
    }

    public function print_tree()
    {
        return '(' . $this->parser->print_tree() . ')+';
    }

}