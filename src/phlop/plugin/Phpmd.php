<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 18:35
 */

namespace phlop\plugin;


use phlop\Plugin;
use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

class Phpmd extends Plugin
{
    protected $defaultParamsDef=["srcPath" => 'src', "logPath" => 'build/logs'];
    public function def($params)
    {
        $srcPath = 'src'; $logPath = 'build/logs';
        extract($params);
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }
        if (!is_dir($logPath)) {
            echo "Can not create logdir, $logPath\n";
            return false;
        }

        $output='';
        $retval=$this->runCommandSilent('phpmd', [$srcPath, "xml", "phpmd.xml", "--reportfile", "$logPath/pmd.xml"], $output);
        if($retval) {
            return $retval;
        }


        return $retval;

    }

}