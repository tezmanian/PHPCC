<?php

namespace Tez\PHPcc\Parser;

class MaybeParser extends Parser
{

    private $parser;

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
        if ($res[0]->failed()) {
            return array(
                ParseResult::lambda(),
                $tks
            );
        } else {
            return $res;
        }
    }

    public function print_tree()
    {
        return '[' .
            $this->parser->print_tree() .
            ']';
    }

    public function process($result)
    {
        if ($result != null) {
            return $this->parser->process($result);
        } else {
            return $result;
        }
    }

    public function setError($err)
    {

    }

}
