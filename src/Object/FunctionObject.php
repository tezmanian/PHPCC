<?php

namespace Tez\PHPcc\Object;

use Tez\PHPcc\Exception\MethodException;

/**
 * Encapsulates a method from an object
 */
class FunctionObject
{

    private $target;
    private $method_name;
    private $params;

    public function __construct($target, $method_name, $params = array())
    {
        #@gencheck
        if ($target == null) {
            //if(!public function_exists($method_name)) { print_backtrace('Function ' . $method_name . ' does not exist');        }
        } else {
            if (!method_exists($target, $method_name)) {
                throw new MethodException(sprintf('Method %s does not exist in %s', $method_name, get_class($target)));
            }
        }//@#

        $this->setTarget($target);
        $this->method_name = $method_name;
        $this->params = $params;
    }

    public function getMethodName()
    {
        return $this->method_name;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function execute()
    {
        $method_name = $this->method_name;
        eval($this->executeString($method_name) . '($this->params);');
    }

    public function executeString($method)
    {
        if ($this->target === null) {
            return $method;
        } else {
            return '$t = $this->getTarget(); $t->' . $method;
        }
    }

    public function executeWith($params)
    {
        $method_name = $this->method_name;
        eval($this->executeString($method_name) . '($params, $this->params);');
    }

    public function executeWithWith($param1, $param2)
    {
        $method_name = $this->method_name;
        eval($this->executeString($method_name) . '($param1, $param2, $this->params);');
    }

    /**
     *  Permission checking
     */
    public function hasPermissions()
    {
        $m = $this->method_name;
        $msg = 'check' . ucfirst($m) . 'Permissions';
        if (method_exists($this->target, $msg)) {
            return $this->target->$msg($this->params);
        } else {
            return true;
        }
    }

    public function callWithWith($param1, $param2)
    {
        $method_name = $this->method_name;
        $ret = '';
        eval($this->callString($method_name) . '($param1, $param2, $this->params);');
        return $ret;
    }

    public function callString($method)
    {
        if ($this->target === null) {
            return '$ret = ' . $method;
        } else {
            return '$t = $this->getTarget(); $ret = $t->' . $method;
        }
    }

    public function getValue()
    {
        return $this->call();
    }

    public function call()
    {
        $method_name = $this->method_name;
        $ret = '';
        eval($this->callString($method_name) . '($this->params);');
        return $ret;
    }

    /* We may want to use public function objects as ValueHolders. Similar to Aspect adaptors */

    public function setValue($value)
    {
        return $this->callWith($value);
    }

    public function callWith($params)
    {
        $method_name = $this->method_name;
        $ret = '';
        eval($this->callString($method_name) . '($params, $this->params);');
        return $ret;
    }

    public function printString()
    {
        return $this->primPrintString($this->target->printString() . '->' . $this->method_name);
    }

    public function primPrintString($str)
    {
        return '[' . get_class($this) . ' ' . $str . ']';
    }

    public function debugPrintString()
    {
        return $this->primPrintString($this->target->debugPrintString() . '->' . $this->method_name);
    }

}

//function callback($target, $selector)
//{
//  return new FunctionObject($target, $selector);
//}
