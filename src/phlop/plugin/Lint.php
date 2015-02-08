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
    public function def($glob = 'src/**.php')
    {
        $buildOk=true;
        echo "Linting $glob\n";
        $files = Glob::glob(Path::makeAbsolute($glob,getcwd()));
        foreach($files as $file){
            $output='';
            $returnValue=$this->runCommandSilent('php',['-l',$file],$output);
            if($returnValue){
                echo "Linting error: $output\n";
                $buildOk=false;
            }
        }
        return $buildOk;
    }

}