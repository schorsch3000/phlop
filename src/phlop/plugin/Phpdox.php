<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:09
 */

namespace phlop\plugin;




class Phpdox extends \phlop\Plugin{

    public function def(){
        return $this->runCommandSilent('phpdox',['-f','phpdox.xml']);
    }
}