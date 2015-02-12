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
    protected $defaultParamsDef=['srcPath' => 'src', 'testPath' => 'tests', 'logPath' => 'build'];
    public function def($params)
    {
        $srcPath =  $testPath = $logPath = '';
        extract($params);
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }
        if (!is_dir($logPath)) {
            echo "Can not create logdir, $logPath\n";
            return 1;
        }
        $output='';
        $retval=$this->runCommandSilent('phploc', ['--count-tests', '--log-csv', $logPath . '/phploc.csv', '--log-xml', 'phploc.xml', $srcPath, $testPath],$output);
        if($retval){
            return $retval;
        }
        echo $output;
        return $retval;

    }

}