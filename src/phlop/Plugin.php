<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:16
 */

namespace phlop;


use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

class Plugin
{
    use LoggerAwareTrait;
    use LoggerTrait;

    public function log($level, $message, array $context = array())
    {
        if (is_object($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }

    protected $ctx;
    protected $config;
    protected $defaultAction = 'def';

    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function setCtx($ctx)
    {
        $this->ctx = $ctx;
    }

    protected function runCommand($command, $args = '')
    {
        $cmd = $command;
        foreach ((array)$args as $arg) {
            $cmd .= ' ';
            $cmd .= escapeshellarg($arg);
        }
        $this->notice("Running command: $cmd");
        system($cmd, $retVal);
        return $retVal;
    }

    protected function runCommandSilent($command, $args = '', &$stdout = '')
    {
        $cmd = $command;
        foreach ((array)$args as $arg) {
            $cmd .= ' ';
            $cmd .= escapeshellarg($arg);
        }
        $this->notice("Running command: $cmd");
        exec($cmd, $stdout, $retVal);
        $stdout = join("\n", $stdout);
        return $retVal;
    }

    public function getMergedParams($action, $params)
    {
        $attrName = 'defaultParams' . ucfirst($action);
        if (!isset($this->$attrName)) {
            $this->debug("did not found default attributes for action $action");
            return [];
        }
        $ret = [];
        foreach ($this->$attrName as $k => $v) {
            if (isset($params[$k])) {
                $ret[$k] = $params[$k];
            } else {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }


    protected function interpolate($message)
    {
        $replaces = array();
        $context = $this->flatten($this->ctx);
        foreach ($context as $key => $val) {
            if (is_bool($val)) {
                $val = '[bool: ' . (int)$val . ']';
            } elseif (
                is_null($val)
                || is_scalar($val)
                || (is_object($val) && method_exists($val, '__toString'))
            ) {
                $val = (string)$val;
            } elseif (is_object($val)) {
                $val = '[object: ' . get_class($val) . ']';
            } else {
                $val = '[type: ' . gettype($val) . ']';
            }
            $replaces['{' . $key . '}'] = $val;
        }
        return strtr($message, $replaces);
    }

    protected function flatten(array $arr, $prefix = '')
    {
        $out = array();
        foreach ($arr as $k => $v) {
            $key = (!strlen($prefix)) ? $k : "{$prefix}.{$k}";
            if (is_array($v)) {
                $out += $this->flatten($v, $key);
            } else {
                $out[$key] = $v;
            }
        }
        return $out;
    }
}