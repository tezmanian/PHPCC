<?php

namespace Tez\PHPcc\Parser;

class MultiParser extends Parser
{

    protected $parser;
    protected $buffer;

    public function __construct($parser)
    {
        parent:: __construct();
        $this->parser = $parser;
    }

    public function setParent($parent, $grammar)
    {
        parent:: setParent($parent, $grammar);
        $this->parser->setParent($this, $grammar);
    }

    public function parse($tks)
    {
        Parser::check_memory_limit();
        $res = $this->parser->parse($tks);
        $ret = array();
        while ((!$res[0]->failed()) && !$res[0]->isLambda()) {
            $ret[] = $res[0]->match;
            $res = $this->parser->parse($res[1]);
        }
        if (empty($ret)) {
            return array(ParseResult::lambda(), $tks);
        } else {
            return array(ParseResult::match($ret), $res[1]);
        }
    }

    public function print_tree()
    {
        return '(' . $this->parser->print_tree() . ')*';
    }

    public function process($res)
    {
        $ret = array();
        foreach ($res as $r) {
            $ret [] = $this->parser->process($r);
        }
        return $ret;
    }

    public function setError($err)
    {
        $this->buffer = $err;
    }

}
