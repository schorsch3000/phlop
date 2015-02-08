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

class Phpcs extends Plugin
{
    public function def($srcPath = 'src', $shallBreakBuild = false, $logPath = 'build/logs', $extensions = 'php', $standard = 'PSR2', $ignore = '')
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
        $args[] = '--report=checkstyle';
        $args[] = "--report-file=$logPath/checkstyle.xml";
        $args[] = '--standard=' . $standard;
        $args[] = '--extensions=' . $extensions;
        if ($ignore) {
            $args[] = '--ignore=' . $ignore;
        }
        $args[] = $srcPath;

        $buildOk = $this->runCommandSilent('phpcs', $args, $output);
        if (0 == $buildOk) {
            return true;
        }
        echo "Found Errors in checkstyle\n",$output;

        return !$shallBreakBuild;

    }

}