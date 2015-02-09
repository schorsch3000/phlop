<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:16
 */

namespace phlop;


class Plugin
{
    protected $ctx;
    protected $config;


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
        echo "Running $cmd\n";
        $cmd.=$this->runPrefix();
        system($cmd, $retVal);
        $this->cleanup();
        return $retVal;
    }

    protected function runCommandSilent($command, $args = '', &$stdout = '')
    {
        $cmd = $command;
        foreach ((array)$args as $arg) {
            $cmd .= ' ';
            $cmd .= escapeshellarg($arg);
        }
        echo "Running $cmd\n";
        $cmd.=$this->runPrefix();
        exec($cmd, $stdout, $retVal);
        $this->cleanup();
        $stdout = join("\n", $stdout);
        return $retVal;
    }
    protected function runPrefix(){
        if(isset($this->config->phpVersion)){
            return "phpenv local ".escapeshellarg($this->config->phpVersion).'; ';
        }
        return '';
    }
    protected function cleanup(){
         unlink('.php-version');
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