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
    protected $defaultParamsDef=['delDirs'=>['build','dist']];

    public function def(array $params)
    {
        $delDirs=$params['delDirs'];
        $this->info('clearing dirs: '.implode(", ",$delDirs));
        foreach ((array)$delDirs as $delDir) {
            Fs::rmrf($delDir);
        }

        return 0;
    }
}