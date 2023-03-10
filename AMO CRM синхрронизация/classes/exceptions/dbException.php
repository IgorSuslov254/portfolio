<?php
namespace amo;

require_once("exceptionTrait.php");

/**
 * db exception
 * @author SuslovIgor <IUSuslov@1cbit.ru>
 * @see ExceptionTrait::log()
 */
class DBException extends \Exception
{
    use ExceptionTrait;

    public function __construct($message = "", $dbConfig = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->log([$message, $dbConfig]);
    }
}