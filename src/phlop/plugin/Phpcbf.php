<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 18:35
 */

namespace phlop\plugin;

use phlop\Plugin;

class Phpcbf extends Plugin
{

    protected $defaultParamsDef = [
        "srcPath" => 'src',
        "standard" => 'PSR2'


    ];

    public function def($params)
    {
        $srcPath = 'src';
        $standard = 'PSR2';
        extract($params);
        $output = '';
        $args = [];

        $args[] = '--standard=' . $standard;

        $args[] = $srcPath;

        $retval = $this->runCommandSilent('phpcbf', $args, $output);
        if (!$retval) {
            return $retval;
        }
        $this->warning("error while running phpcbf");
        $this->info($output);


        return $retval;

    }
}
