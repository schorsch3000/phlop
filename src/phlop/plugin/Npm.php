<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:09
 */

namespace phlop\plugin;



namespace phlop\plugin;
use phlop\Fs;


class Npm extends \phlop\Plugin
{
    public function __construct()
    {
        if(!is_file('package.json')) {
            throw new \Exception('package.json not found');
        }
    }
    public function updateOrInstall()
    {

        if(is_dir('node_modules')) {

            return $this->update();
        }else{
            return $this->install();
        }
    }
    public function install()
    {
        return $this->runCommand('npm', __FUNCTION__);
    }
    public function update()
    {
        return $this->runCommand('npm', __FUNCTION__);
    }
    public function clean()
    {
        Fs::rmrf('node_modules');
        return true;
    }
}