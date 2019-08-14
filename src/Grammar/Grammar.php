<?php

namespace Tez\PHPcc\Grammar;

use Tez\PHPcc\Parser\ParseInput;
use Tez\PHPcc\Parser\Parser;
use Tez\PHPcc\Parser\SubParser;

class Grammar
{

    private $pointcuts = [];
    private $errors = [];
    private $params = [];
    private $input = NULL;
    private $res = [];

    public function __construct($params)
    {
        $this->params = $params;
        foreach (array_keys($this->params['nt']) as $k) {
            $this->params['nt'][$k]->setParent($this, $this);
        }
    }

    public function get($name)
    {
        return $this->params['nt'][$name];
    }

    public function addPointCuts($ps)
    {
        $this->setPointcuts(array_merge($this->pointcuts, $ps));
    }

    public function setPointCuts($ps)
    {
        $this->pointcuts = $ps;
    }

    public function getGrammar()
    {
        return $this;
    }

    public function process($name, $data)
    {
        $p = $this->getProcessor($name);
        if ($p === null) {
            return $data;
        } else {
            return $p->callWith($data);
        }
    }

    public function getProcessor($name)
    {
        return array_key_exists($name, $this->pointcuts) ? $this->pointcuts[$name] : NULL;
    }

    /**
     *
     * @param type $str
     * @return \Tez\phpcc\Grammar\Grammar|boolean
     */
    public function compile($str)
    {
        Parser::check_memory_limit();
        $this->errors = array();
        $this->input = $str;
        $root = $this->getRoot();

        $this->res = $root->parse(new ParseInput($str));
        if (preg_match('/^[\s\t\n]*$/', $this->res[1]->str)) {
            return $root->process($this->res[0]->match); //$this->process($this->params['root'],$res1);
        } else {
            return $this->getError();
        }
    }

    public function getRoot()
    {
        $root = new SubParser($this->params['root']);
        $root->setParent($this, $this);
        return $root;
    }

    public function getError()
    {
        $str = $this->input;
        $ret = '';
        foreach ($this->errors as $remaining => $symbol) {
            if ($remaining == 0) {
                $rem = 'EOF';
                $prev = $str;
            } else {
                $rem = '"' . substr($str, -$remaining, 1) . '"';
                $prev = substr($str, 0, -$remaining);
            }
            $lines = explode("\n", $prev);
            $nl = count($lines);
            /* $ret .="\n".'Unexpected '.$rem.', expecting '.$symbol.
              ' on line '.$nl. ',character '.(strlen(array_pop($lines))+1); */
            $ret = FALSE;
        }

        return $ret;
    }

    public function isError()
    {
        return empty($this->errors);
    }

    public function setError($err)
    {
        $this->errors = $err;
    }

    public function print_tree()
    {
        $ret = "<" . $this->params['root'] . "(\n   ";
        foreach (array_keys($this->params['nt']) as $k) {
            $ret .= $k . '::=' .
                $this->params['nt'][$k]->print_tree() .
                ".\n   ";
        }
        return $ret . ")>";
    }

}
