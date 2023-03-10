<?php
namespace amo;

require_once("exceptionTrait.php");

/**
 * amoCRM exception
 * @author SuslovIgor <IUSuslov@1cbit.ru>
 * @see ExceptionTrait::log()
 */
class AMOException extends \Exception
{
    use ExceptionTrait;

    public function __construct($message = "", $response, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->log([$message, $response, $code]);
    }
}