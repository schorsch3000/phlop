<?php


namespace phlop;


class Application
{
    private $phlopFile = "phlop.json";
    private $phlopJson;
    private $phlopData = [];
    private $ctx;

    public function setPhlopFile($file)
    {
        $this->phlopFile = $file;
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
        $buildOk=true;
        foreach ((array)$stageNames as $stageName) {
            $stage = $this->getStage($stageName);
            foreach ($stage as $stageData) {
                $stageData=(array)$stageData;
                $pluginName = array_shift($stageData);
                $pluginAction = array_shift($stageData);
                if($pluginAction==''){
                    $pluginAction='def';
                }
                $pluginParams = $stageData;
                echo "Running task $pluginName\n";
                $buildStatus=$this->runPluginAction($pluginName,$pluginAction,$pluginParams);
                $buildOk=$buildOk && $buildStatus;
                if(!$buildOk){
                    echo "$pluginName failed\n";
                    return $buildOk;
                }
                echo "Done running $pluginName\n";
            }
        }
        return $buildOk;

    }
    private function getContext(){
        @$this->ctx['composer']=json_decode(file_get_contents('composer.json'),true);
        @$composerName=explode('/',$this->ctx['composer']['name'],2);
        @$this->ctx['composer']['name']=['vendor=>'=>$composerName[0],'project'=>$composerName[1]];
        @$this->ctx['npm']=json_decode(file_get_contents('package.json'),true);
        @$this->ctx['bower']=json_decode(file_get_contents('bower.json'),true);
        @$this->ctx['semver']=json_decode(file_get_contents('.semver'),true);
    }

    public function runPluginAction($pluginName, $pluginAction, Array $pluginParams = [])
    {
        $className = '\\phlop\\plugin\\' . ucfirst(strtolower($pluginName));

        if (!class_exists($className, true)) {
            throw new \Exception("PLUGIN $pluginName NOT FOUND");
        }
        $plugin = new $className;
        if (!method_exists($plugin, $pluginAction)) {
            throw new \Exception("PLUGIN $pluginName HAS NO ACTION $pluginAction");
        }
        $plugin->setCtx($this->ctx);
        $plugin->setConfig($this->phlopData);
        return call_user_func_array([$plugin, $pluginAction], $pluginParams);
    }


    private function getStage($name)
    {
        if (!isset($this->phlopData->$name)) {
            throw new \Exception("Stage $name not found");
        }
        return $this->phlopData->$name;
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
        $this->phlopData = json_decode($this->phlopJson);
        if ($this->phlopData === null) {
            throw new \Exception('JSON SEEMS WIRED');
        }
    }

}