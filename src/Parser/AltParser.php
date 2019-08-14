<?php

namespace Tez\PHPcc\Parser;
/*
 * To find alternatives correctly, we should use a set of possible next symbols, and possible
 * next subparsers (to DFS).
 * If none of the symbols work, fail.
 * If one of them works, and it's only one, that's our alternative. Complete the parse in that branch.
 * If more than one work, ask for the following symbols on each alternative, and proceed again.
 *
 */

use Tez\PHPcc\Exception\ParserException;

class AltParser extends Parser
{

    protected $backtrack = false;
    protected $children = [];

    protected $errorBuffer = [];

    public function __construct($children = [], $backtrack = false)
    {
        if (!is_array($children)) {
            echo 'NOT ARRAY!';
            print_r($children);
            exit;
        }
        parent:: __construct();
        $this->backtrack = $backtrack;
        $this->children = $children;
    }

    public function setParent($parent, $grammar)
    {
        parent:: setParent($parent, $grammar);
        foreach (array_keys($this->children) as $k) {
            $this->children[$k]->setParent($this, $grammar);
        }
    }

    public function parse($tks)
    {
        Parser::check_memory_limit();
        $return = array(ParseResult::fail(), $tks);
        foreach (array_keys($this->children) as $k) {
            $c = $this->children[$k];
            $res = $c->parse($tks);
            if (!$res[0]->failed() && !$res[0]->isLambda()) {
                //if ($res[0]->isLambda()) print_backtrace(strtolower(get_class($c). ' '.htmlentities($this->print_tree()));
                if ($res[1]->isBetterMatchThan($return[1])) {
                    $res[0] = ParseResult::match(array('selector' => $k, 'result' => $res[0]->match));
                    $return = $res;
                }
            }
        }
        if ($return[0]->failed()) {
            parent::setError($this->errorBuffer);
        }
        $this->errorBuffer = array();
        return $return;
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
                $ret [] = $k . '=>' . $t;
            }
        }
        return implode('|', $ret);
    }

    public function process($result)
    {
        if (!$this->children[$result['selector']]) {
            throw new ParserException('wrong alternative');
        }
        $rets = $this->children[$result['selector']]->process($result['result']);
        $arr = array('selector' => $result['selector'], 'result' => $rets);
        return $arr;
    }

    public function setError($err)
    {
        $this->errorBuffer = array_merge($err, $this->errorBuffer);
    }

}
