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
    public function tgz($filenmameFormat = "{composer.name.project}-{semver}", $input = 'dist')
    {
        return $this->def(__FUNCTION__, $filenmameFormat, $input);
    }

    public function tbz($filenmameFormat = "{composer.name.project}-{semver}", $input = 'dist')
    {
        return $this->def(__FUNCTION__, $filenmameFormat, $input);
    }

    public function tar($filenmameFormat = "{composer.name.project}-{semver}", $input = 'dist')
    {
        return $this->def(__FUNCTION__, $filenmameFormat, $input);
    }

    public function def($packageType = 'tgz', $filenameFormat = "{composer.name.project}-{semver}", $input = 'dist')
    {
        $args = [];
        $args[] = '-c';
        $args[] = '--transform';
        $args[] = $this->interpolate("s,dist,$filenameFormat,");
        $args[] = '-f';
        $args[] = $this->interpolate($filenameFormat . '.' . $packageType);
        if ($packageType == 'tgz') {
            $args[] = '-z';
        }
        if ($packageType == 'tgb') {
            $args[] = '-j';
        }
        $args[] = $input;

        return !$this->runCommand('tar', $args);

    }

}

//TODO:
/*
 * composer.phar
 */