<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:08
 */

namespace phlop\plugin;




class Sh extends \phlop\Plugin
{
    protected $defaultParamsDef=['command'=>false,'args'=>[]];

    public function def(array $params)
    {

        return $this->runCommand($params['command'], $params['args']);
    }
}