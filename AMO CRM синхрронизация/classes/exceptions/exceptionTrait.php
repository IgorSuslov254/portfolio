<?php
namespace amo;

/**
 * Exception trait
 * @author Suslov Igor <IUSuslov@1cbit.ru>
 */
trait ExceptionTrait
{
    /**
     * log exception
     * @param array $logInfo
     * @return void
     */
    public function log(array $logInfo)
    :void
    {
        $log = date("Y-m-d H:i:s") . " " . print_r($logInfo, true);
        file_put_contents(__DIR__ . "/../../logs/error.log", $log . PHP_EOL, FILE_APPEND);
    }
}