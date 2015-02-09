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
    public function def($srcPath = 'src', $logPath = 'build/logs')
    {
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }
        if (!is_dir($logPath)) {
            echo "Can not create logdir, $logPath\n";
            return false;
        }

        $output='';
        $buildOk=$this->runCommandSilent('phpmd', [$srcPath, "xml", "phpmd.xml", "--reportfile", "$logPath/pmd.xml"],$output);
        if(0==$buildOk){
            return true;
        }
        echo $output;

        return true;

    }

}