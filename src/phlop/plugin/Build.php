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

    protected $defaultParamsDef=['srcPath'=>'src','targetPath'=>'dist'];
    public function def($params)
    {

        $clear = new Clear();
        $clear->def(['delDirs'=>[$params['targetPath']]]);
        mkdir($params['targetPath']);
        $this->runCommandSilent('cp', ['-rp', $params['srcPath'] . '/.', $params['targetPath']]);
        return !$this->buildComposer($params['targetPath']);

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
