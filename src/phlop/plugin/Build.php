<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:08
 */

namespace phlop\plugin;

use phlop\Fs;
use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;


class Build extends \phlop\Plugin
{

    public function def($srcPath = 'src', $targetPath = 'dist')
    {

        $clear = new Clear();
        $clear->def([$targetPath]);
        mkdir($targetPath);
        $this->runCommandSilent('cp', ['-rp', $srcPath . '/.', $targetPath]);
        return !$this->buildComposer($targetPath);

    }

    public function buildComposer($targetPath)
    {
        if (!is_file('composer.json')) {
            return false;
        }
        copy('composer.json', $targetPath . '/composer.json');
        $cwd = getcwd();
        chdir($targetPath);
        $retval=$this->runCommand('composer', ["install","--no-dev", "--no-scripts", "--optimize-autoloader"]);
        chdir($cwd);
        return $retval;
    }
}
