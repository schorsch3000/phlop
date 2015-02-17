<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 07.02.15
 * Time: 12:08
 */

namespace phlop\plugin;

use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;


class Upload extends \phlop\Plugin
{

    protected $defaultParamsDef = [
        'uploadFiles' => './dist/packages/**',
        'nameFrom' => 'composer',
        'versionFrom' => 'semverFile',
        'semverFile' => '.semver',
        'composerFile' => 'composer.json',
        'npmFile' => 'packages.json',
        'nameFile' => '.name',
        'repositoryUrl' => 'http://repo.shnbk.de'
    ];
    protected $params;

    public function def($params)
    {
        $this->params = $params;
        $files = Glob::glob(Path::makeAbsolute($this->params['uploadFiles'], getcwd()));
        $name = $this->getName();
        $version = $this->getVersion();

        foreach ($files as $file) {
            $queryString=http_build_query(['name'=>$name,'version'=>$version,"filename"=>basename($file)]);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->params['repositoryUrl'].'?'.$queryString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: ' . mime_content_type($file)));
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file));
            echo curl_exec($ch);
        }

    }

    protected function getName()
    {
        $methodName = "getNameFrom" . ucfirst(mb_strtolower($this->params['nameFrom']));
        if (!method_exists($this, $methodName)) {
            throw new \Exception('can not get name from ' . $this->params['nameFrom']);
        }
        return $this->$methodName();
    }

    protected function getVersion()
    {
        $methodName = "getVersionFrom" . ucfirst(mb_strtolower($this->params['versionFrom']));
        if (!method_exists($this, $methodName)) {
            throw new \Exception('can not get version from ' . $this->params['versionFrom'] . " (using $methodName)");
        }
        return $this->$methodName();
    }

    protected function getNameFromComposer()
    {
        $data = json_decode(file_get_contents($this->params['composerFile']));
        return $data->name;
    }

    protected function getNameFromNpm()
    {
        $data = json_decode(file_get_contents($this->params['npmFile']));
        return $data->name;
    }

    protected function getNameFromNamefile()
    {
        return trim(file_get_contents($this->params['nameFile']));
    }

    protected function getVersionFromSemverfile()
    {
        return trim(file_get_contents($this->params['semverFile']));
    }

    protected function getVersionFromNpm()
    {
        $data = json_decode(file_get_contents($this->params['npmFile']));
        return $data->version;
    }


}

