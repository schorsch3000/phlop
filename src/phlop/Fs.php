<?php

namespace phlop;


class Fs
{
    static function rmrf($path)
    {
        return shell_exec('rm -rf '.escapeshellarg($path));
    }
}