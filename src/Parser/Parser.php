<?php

namespace Tez\PHPcc\Parser;

use Tez\PHPcc\Exception\MemoryException;
use Tez\PHPcc\Exception\ParserException;

class Parser
{

    private static $memory_limit;
    protected $grammar;
    protected $errorHandler = [];

    // Check Memory Limits (running into memory Problems with to much statments)

    public function __construct()
    {
        Parser::check_memory_limit();
    }

    public static function check_memory_limit()
    {
        if (empty(self::$memory_limit)) {
            Parser::get_memory_limit();
        }
        $usage = memory_get_usage(true);
        if (self::$memory_limit > 0 && $usage > self::$memory_limit) {
            throw new MemoryException('memory limit reached');
        }
        return $usage;
    }

    // END Check Memory

    public static function get_memory_limit()
    {
        $limit = ini_get('memory_limit');

        if ($limit > 0) {
            preg_match('!([^0-9]+)!', $limit, $matches);
            if (count($matches) > 1) {
                switch (strtoupper(trim($matches[0]))) {
                    case 'G':
                        $limit = (((int)$limit) * 1024 * 1024 * 1024);
                    case 'M':
                        $limit = (((int)$limit) * 1024 * 1024);
                    case 'KB':
                        $limit = (((int)$limit) * 1024);
                    case 'B':
                        $limit = ((int)$limit);
                }
            }
            self::$memory_limit = (int)($limit - ($limit / 100 * 2));
        } else {
            self::$memory_limit = $limit;
        }

        return self::$memory_limit;
    }

    public static function checkMaxParseReq($key, $limit = NULL)
    {
        $key = sprintf('phpcc_%s', $key);
        if (!isset($GLOBALS[$key])) {
            $GLOBALS[$key] = 0;
        } else {
            $GLOBALS[$key]++;
            if (!is_null($limit) && ((int)$GLOBALS[$key] <= (int)$limit)) {
                return true;
            }
            throw new ParserException('');
        }
        return true;
    }

    public function get($name)
    {
        $gr = $this->getGrammar();
        return $gr->get($name);
    }

    public function getGrammar()
    {
        return $this->grammar;
    }

    public function setGrammar($grammar)
    {
        $this->grammar = $grammar;
    }

    public function setParent($parent, $grammar)
    {
        $this->setGrammar($grammar);
        $this->setErrorHandler($parent);
    }

    public function setErrorHandler($eh)
    {
        $this->errorHandler[] = $eh;
    }

    public function popErrorHandler()
    {
        $eh = array_pop($this->errorHandler);
        return $eh;
    }

    public function process($result)
    {
        return $result;
    }

    public function setError($err)
    {
        //echo "<br/>setting $err from ".get_class($this) . " to ".get_class($this->errorHandler[count($this->errorHandler)-1]);
        $this->errorHandler[count($this->errorHandler) - 1]->setError($err);
    }

}
