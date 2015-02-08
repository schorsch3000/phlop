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

class Loc extends Plugin
{
    public function def($srcPath = 'src', $testPath = 'tests', $logPath = 'build')
    {
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }
        if (!is_dir($logPath)) {
            echo "Can not create logdir, $logPath\n";
            return false;
        }
        $output='';
        $buildOk=$this->runCommandSilent('phploc', ['--count-tests', '--log-csv', $logPath . '/phploc.csv', '--log-xml', 'phploc.xml', $srcPath, $testPath],$output);
        if(!$buildOk){
            return true;
        }
        echo $output;
        return false;

    }

}