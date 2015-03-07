<?php


namespace phlop;


use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

class Application
{
    use LoggerAwareTrait;
    use LoggerTrait;

    private $phlopFile = "phlop.json";
    private $phlopJson;
    private $phlopData = [];
    private $ctx;
    protected $logger;

    public function log($level, $message, array $context = array())
    {
        if (is_object($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }

    public function setPhlopFile($file)
    {
        $this->phlopFile = $file;
        $this->debug('set phlopfile to' . $file);
        return $this;

    }

    public function setPhlopJson($jsonString)
    {
        $this->phlopJson = $jsonString;
        return $this;

    }

    public function init()
    {
        if ($this->phlopJson === null) {
            $this->loadPhlopFile();
        }
        $this->parseJson();
        $this->getContext();
        return $this;
    }

    public function runStages($stageNames)
    {
        $retval = true;
        foreach ((array)$stageNames as $stageName) {
            $retval = $retval && $this->runStage($stageName);
            if($retval) {
                return $retval;
            }
        }
        return $retval;

    }

    public function runStage($stageName)
    {
        $this->notice('---------------------------------------------------');
        $this->notice('running stage '.$stageName);
        $this->notice('---------------------------------------------------');
        $stage = $this->getStage($stageName);
        foreach ($stage as $pluginData) {
            $pluginName=$pluginAction=$pluginParams=[];
            $retval=0;
            extract($this->parsePluginData($pluginData));
            $this->notice("Running task $pluginName");
            $buildStatus = $this->runPluginAction($pluginName, $pluginAction, $pluginParams);
            $retval += $buildStatus;
            if ($retval) {
                $this->critical("$pluginName failed\n");
                return $retval;
            }
            $this->notice("Done running $pluginName");

        }
        return 0;
    }
    private function parsePluginData($pluginData)
    {
        $pluginData = (array)$pluginData;
        $pluginName = array_shift($pluginData);
        $stageDataLength = count($pluginData);
        if ($stageDataLength === 1 && is_scalar($pluginData[0])) {
            $pluginAction = $pluginData[0];
            $pluginParams = [];
        } elseif ($stageDataLength === 1 && is_array($pluginData[0])) {
            $pluginAction = false;
            $pluginParams = $pluginData[0];
        } elseif ($stageDataLength === 2 && is_scalar($pluginData[0]) && is_array($pluginData[1])) {
            $pluginAction = $pluginData[0];
            $pluginParams = $pluginData[1];
        } elseif ($stageDataLength === 0) {
            $pluginAction = false;
            $pluginParams = [];
        } else {
            throw new \Exception("params for plugin $pluginName are wrong" . print_r($pluginData, 1) . "\n");
        }
        return compact('pluginName', 'pluginAction', 'pluginParams');
    }

    private function getContext()
    {
        @$this->ctx['composer'] = json_decode(file_get_contents('composer.json'), true);
        @$composerName = explode('/', $this->ctx['composer']['name'], 2);
        @$this->ctx['composer']['name'] = ['vendor=>' => $composerName[0], 'project' => $composerName[1]];
        @$this->ctx['npm'] = json_decode(file_get_contents('package.json'), true);
        @$this->ctx['bower'] = json_decode(file_get_contents('bower.json'), true);
        @$this->ctx['semver'] = json_decode(file_get_contents('.semver'), true);
    }

    public function runPluginAction($pluginName, $pluginAction, Array $pluginParams = [])
    {
        $this->notice('---------------------------------------------------');
        $this->notice('running plugin '.$pluginName);
        $this->notice('---------------------------------------------------');
        $className = '\\phlop\\plugin\\' . ucfirst(strtolower($pluginName));
        $this->debug("Using class $className for plugin $pluginName");
        if (!class_exists($className, true)) {
            $msg="Did not found plugin:'$pluginName'";
            $this->critical($msg);
            throw new \Exception($msg);
        }
        $plugin = new $className;
        /**
 * @var $plugin \phlop\Plugin 
*/
        $plugin->setLogger($this->logger);
        if($pluginAction===false) {
            $pluginAction=$plugin->getDefaultAction();
        }
        if (!method_exists($plugin, $pluginAction)) {
            $msg="Plugin $pluginName has no action: '$pluginAction'";
            $this->critical($msg);
            throw new \Exception($msg);
        }
        $plugin->setCtx($this->ctx);
        $plugin->setConfig($this->phlopData);
        $pluginParams=$plugin->getMergedParams($pluginAction, $pluginParams);
        $retval=call_user_func([$plugin, $pluginAction], $pluginParams);
        $this->notice('---------------------------------------------------');
        $this->notice('DONE: running plugin '.$pluginName);
        if($retval>0) {
            $this->emergency("Plugin $pluginName FAILED");
        }
        $this->notice('---------------------------------------------------');

        return $retval;
    }


    private function getStage($name)
    {
        if (!isset($this->phlopData[$name])) {
            throw new \Exception("Stage $name not found");
        }
        return $this->phlopData[$name];
    }

    private function loadPhlopFile()
    {
        if (!is_file($this->phlopFile)) {
            throw new \Exception('PHPLOPFILE NOT FOUND');
        }
        $this->setPhlopJson(file_get_contents('phlop.json'));
    }

    private function parseJson()
    {
        $this->phlopData = json_decode($this->phlopJson, true);
        if ($this->phlopData === null) {
            throw new \Exception('JSON SEEMS WIRED');
        }
    }

}