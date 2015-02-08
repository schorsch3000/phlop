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
    public function def($srcPath = 'src', $shallBreakBuild = false, $logPath = 'build/logs')
    {
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
        $args[]="$logPath/pmd-cpd.xml";

        $args[] = $srcPath;

        $buildOk = $this->runCommand('phpcpd', $args, $output);
        if (0 == $buildOk) {
            return true;
        }
        echo "Found Errors in checkstyle\n",$output;

        return !$shallBreakBuild;

    }

}