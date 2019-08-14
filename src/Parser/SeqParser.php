<?php

namespace Tez\PHPcc\Parser;

class SeqParser extends Parser
{

    private $children = [];

    public function __construct($children)
    {
        if (!is_array($children)) {
            echo 'NOT ARRAY!';
            print_r($children);
            exit;
        }
        parent:: __construct();
        $this->children = $children;
    }

    public function setParent($parent, $grammar)
    {
        parent:: setParent($parent, $grammar);
        foreach (array_keys($this->children) as $k) {
            $this->children[$k]->setParent($this, $grammar);
        }
    }

    public function process($result)
    {
        $rets = [];
        foreach (array_keys($this->children) as $k) {
            $rets [$k] = $this->children[$k]->process($result[$k]);
        }
        return $rets;
    }

    public function parse($tks)
    {
        Parser::check_memory_limit();
        $res = array(FALSE, $tks);
        $ret = array();
        foreach (array_keys($this->children) as $k) {
            $res = $this->children[$k]->parse($res[1]);
            if ($res[0]->failed()) {
                return array(ParseResult::fail(), $tks);
            }
            $ret[$k] = $res[0]->match;
        }
        return array(ParseResult::match($ret), $res[1]);
    }

    public function print_tree()
    {
        $ret = [];
        foreach (array_keys($this->children) as $k) {
            $c = $this->children[$k];
            $t = $c->print_tree();
            if (strtolower(get_class($c)) == 'altparser') {
                $t = '(' . $t . ')';
            }
            if (is_numeric($k)) {
                $ret [] = $t;
            } else {
                $ret [] = $k . '->' . $t;
            }
        }
        return implode(' ', $ret);
    }

}
