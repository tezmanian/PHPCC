<?php

namespace Tez\PHPcc\Parser;

class ListParser extends Parser
{

    private $errorBuffer = array();
    private $parser;
    private $sep;

    public function __construct($parser, $separator)
    {
        parent:: __construct();
        $this->sep = $separator;
        $this->parser = $parser;
    }

    public function setParent($parent, $grammar)
    {
        parent:: setParent($parent, $grammar);
        $this->sep->setParent($this, $grammar);
        $this->parser->setParent($this, $grammar);
    }

    public function parse($tks)
    {
        Parser::check_memory_limit();
        /* first, we parse the list */
        $mp = new MultiParser(new SeqParser(array(
            $this->parser,
            $this->sep
        )));
        $mp->setParent($this, $this->grammar);
        $res = $mp->parse($tks);
        /* then, we parse again, in the tail of the list */
        $res1 = $this->parser->parse($res[1]);
        /* if the tail failed, the parse failed */
        if ($res1[0]->failed() || $res1[0]->isLambda()) {
            parent::setError($this->errorBuffer);
            return array(
                ParseResult::fail(),
                $tks
            );
        }
        /* we collect the last parsed token, in the first position of the subarray (as all the other ones) */
        $res[0]->match[] = array(
            $res1[0]->match
        );
        return array(
            ParseResult::match($res[0]->match),
            $res1[1]
        );
    }

    public function setError($err)
    {
        $this->errorBuffer = array_merge($err, $this->errorBuffer);
    }

    public function print_tree()
    {
        return '{' .
            $this->parser->print_tree() .
            ';' .
            $this->sep->print_tree() .
            '}';
    }

    public function process($res)
    {
        $ret = [];
        for ($i = 0; $i < count($res); $i++) {
            $ret [] = $this->parser->process($res[$i][0]);
            if (isset($res[$i][1])) {
                $ret [] = $this->sep->process($res[$i][1]);
            }
        }
        return $ret;
    }

}
