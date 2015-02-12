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

class Phpcpd extends Plugin
{
    protected $defaultParamsDef = ["srcPath" => 'src', "shallBreakBuild" => false, "logPath" => 'build/logs'];

    public function def($params)

    {
        $srcPath = 'src';
        $shallBreakBuild = false;
        $logPath = 'build/logs';
        extract($params);
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }
        if (!is_dir($logPath)) {
            echo "Can not create logdir, $logPath\n";
            return false;
        }

        $output = '';
        $args = [];

        $args[] = "--log-pmd";
        $args[] = "$logPath/pmd-cpd.xml";

        $args[] = $srcPath;

        $retval = $this->runCommand('phpcpd', $args, $output);
        if (!$retval) {
            return $retval;
        }
        $this->warning("Found Errors in copy paste detection");
        $this->info($output);

        if($shallBreakBuild){
            return $retval;
        }
        return 0;

    }

}