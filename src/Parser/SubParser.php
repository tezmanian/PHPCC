<?php

namespace Tez\PHPcc\Parser;

use Exception;

class SubParser extends Parser
{

    private $subName;

    public function __construct($name)
    {
        parent:: __construct();
        $this->subName = $name;
    }

    public function parse($tks)
    {
        Parser::check_memory_limit();
        if (($res = $tks->getPartial($this->subName)) === null) {
            if ($tks->includesNonTerminal($this->subName)) {
                $tks->reDescendNonTerminal($this->subName);
                return array(ParseResult::fail(), $tks);
            }
            $p = $this->get($this->subName);
            $g = $this->getGrammar();
            if ($p === null) {
                throw new Exception($this->subName . ' does not exist');
            }
            $tks->pushNonTerminal($this->subName);
            $p->setErrorHandler($this);
            $res = $p->parse($tks);
            $tks->popNonTerminal($this->subName);
            $next = $res;
            $str = $tks->str;
            $tks->str = '';
            $parts = $tks->partials;
            $tks->partials = array();
            while ($tks->shouldReDescend($this->subName) && !$next[0]->failed() && !$next[0]->isLambda()) {
                $tks->addPartial($this->subName, $res);
                $res = $next;
                $next = $p->parse($tks);
            }
            $tks->str = $str;
            $tks->partials = $parts;
            $tks->addPartial($this->subName, $res);
            $p->popErrorHandler();
        }
        return $res;
    }

    public function setError($err)
    {
        $eh = $this->popErrorHandler();
        $eh->setError($err);
        $this->setErrorHandler($eh);
    }

    public function getParser()
    {
        return $this->get($this->subName);
    }

    public function print_tree()
    {
        return '<' . $this->subName . '>';
    }

    public function process($res)
    {
        $p = $this->get($this->subName);
        $ret = $p->process($res);
        $g = $this->getGrammar();
        return $g->process($this->subName, $ret);
    }

}
