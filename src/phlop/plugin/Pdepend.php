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

class Pdepend extends Plugin
{
    public function def($srcPath = 'src', $logPath = 'build/logs', $chartPath='build/pdepend')
    {
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }
        if (!is_dir($logPath)) {
            echo "Can not create logdir, $logPath\n";
            return false;
        }
        if (!is_dir($chartPath)) {
            mkdir($chartPath, 0777, true);
        }
        if (!is_dir($chartPath)) {
            echo "Can not create chartdir, $chartPath\n";
            return false;
        }
        $output='';
        $buildOk=$this->runCommandSilent('pdepend', ["--jdepend-xml=".$logPath."/jdepend.xml","--jdepend-chart=".$chartPath."/dependencies.svg", "--overview-pyramid=".$chartPath."/overview-pyramid.svg", $srcPath],$output);
        if(0==$buildOk){
            return true;
        }
        echo $output;
        var_dump($buildOk);
        return false;

    }

}