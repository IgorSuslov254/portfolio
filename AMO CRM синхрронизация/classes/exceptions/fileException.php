<?php
namespace amo;

require_once("exceptionTrait.php");

/**
 * file exception
 * @author SuslovIgor <IUSuslov@1cbit.ru>
 * @see ExceptionTrait::log()
 */
class FileException extends \Exception
{
    use ExceptionTrait;

    public function __construct($message = "", $path = [], $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->log([$message, $path]);
    }
}