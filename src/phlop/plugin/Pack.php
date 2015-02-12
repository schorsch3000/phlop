<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:08
 */

namespace phlop\plugin;

use phlop\Fs;


class Pack extends \phlop\Plugin
{

    protected $defaultParamsDef = ["type" => 'tgz', "filenameFormat" => "{composer.name.project}-{semver}", "input" => 'dist'];

    public function def($params)
    {
        $type=$filenameFormat=$input='';
        extract($params);
        mkdir('dist/packages');
        $args = [];
        $args[] = '-c';
        $args[] = '--transform';
        $args[] = $this->interpolate("s,dist,$filenameFormat,");
        $args[] = '-f';
        $args[] = $this->interpolate('dist/packages/' . $filenameFormat . '.' . $type);
        $args[] = '--exclude';
        $args[] = 'dist/packages';
        if ($type == 'tgz') {
            $args[] = '-z';
        }
        if ($type == 'tgb') {
            $args[] = '-j';
        }
        $args[] = $input;

        return !$this->runCommandSilent('tar', $args);

    }

}

//TODO:
/*
 * composer.phar
 */