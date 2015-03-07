<?php

namespace phlop;

class Fs
{
    public static function rmrf($path)
    {
        return shell_exec('rm -rf '.escapeshellarg($path));
    }
}
