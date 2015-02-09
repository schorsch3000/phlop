<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:08
 */

namespace phlop\plugin;

use phlop\Fs;


class Phpunit extends \phlop\Plugin
{
    public function def()
    {


        return !$this->runCommand('phpunit');
    }
}