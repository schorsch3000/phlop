<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:08
 */

namespace phlop\plugin;
use phlop\Fs;


class Composer extends \phlop\Plugin{
    public function __construct(){
        if(!is_file('composer.json')){
            throw new \Exception('composer.json not found');
        }
    }
    public function updateOrInstall(){

        if(is_file('composer.lock')){

            return $this->update();
        }else{
            return $this->install();
        }
    }
    public function install(){
        return $this->runCommand('composer',__FUNCTION__);
    }
    public function update(){
        return $this->runCommand('composer',__FUNCTION__);
    }
    public function clean(){
        Fs::rmrf('vendor');
        return true;
    }
}