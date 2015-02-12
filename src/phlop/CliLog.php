<?php
/**
 * Created by IntelliJ IDEA.
 * User: dicky
 * Date: 12.02.15
 * Time: 19:24
 */

namespace phlop;


use Psr\Log\AbstractLogger;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;


class CliLog extends AbstractLogger
{
    private $minLogLevel = 0;

    /**
     * @param int $minLogLevel
     */
    public function setMinLogLevel($minLogLevel)
    {
        $this->minLogLevel = $minLogLevel;
    }

    public function log($level, $message, array $context = array())
    {
        $logLevelDetails = $this->getLogLevelDetail($level);
        if ($this->minLogLevel > $logLevelDetails[0]) {
            return false;
        }
        $realMessage = $logLevelDetails[1];
        $realMessage .= $this->interpolate($message, $context);
        $realMessage.=Color::RST_ALL;
        $realMessage.="\n";
        return fwrite(fopen('php://stderr','a'),$realMessage);
    }

    private function getLogLevelDetail($logLevel)
    {
        $logLevels = [];
        $counter = 0;
        $logLevels['debug'] = [$counter, Color::CYAN];
        $logLevels['info'] = [$counter++, Color::LIGHT_GREEN];
        $logLevels['notice'] = [$counter++, Color::GREEN];
        $logLevels['warning'] = [$counter++, Color::YELLOW];
        $logLevels['error'] = [$counter++, Color::LIGHT_RED];
        $logLevels['critical'] = [$counter++, Color::RED];
        $logLevels['alert'] = [$counter++, Color::BG_RED.Color::WHITE];
        $logLevels['emergency'] = [$counter++,Color::BG_RED.Color::WHITE.Color::UNDERLINE];


        return $logLevels[$logLevel];
    }

    private function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}