<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:08
 */

namespace phlop\plugin;

use phlop\Fs;


class Clear extends \phlop\Plugin
{
    public function def($delDirs = ['build','dist'])
    {
        foreach ($delDirs as $delDir) {
            Fs::rmrf($delDir);
        }

        return true;
    }
}