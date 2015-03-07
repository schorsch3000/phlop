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

class Lint extends Plugin
{
    protected $defaultParamsDef=['glob'=>'src/**.php'];
    public function def($params)
    {
        $glob=$params['glob'];
        $this->info('Linting '.$glob);
        $files = Glob::glob(Path::makeAbsolute($glob, getcwd()));
        $returnValue=0;
        foreach ($files as $file) {
            $output='';
            $localRetval=$this->runCommandSilent('php', ['-l',$file], $output);
            if ($localRetval) {
                $this->error("Linting error: $output\n");
            }
            $returnValue=max($returnValue,$localRetval);
        }
        return $returnValue;
    }
}
