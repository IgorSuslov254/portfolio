<?php
namespace amo;

require_once("classes/amoCrmApi.php");

/**
 * amoCrm class
 * @author Suslov Igor <IUSuslov@1sbit.ru>
 */
class AmoCrm
{
    /**
     * send data amo
     * @param array $data
     * @return void
     */
    public function sendData(array $data)
    : string
    {
        $amoCrmApi = new AmoCrmApi();
        return $amoCrmApi->index($data);
    }

    /**
     * get field amo
     * @return void
     */
    public function documentation()
    : void
    {
        $amoCrmApi = new AmoCrmApi();
        $amoCrmApi->getLinks();
    }
}

$amoCrm = new AmoCrm();
print $amoCrm->sendData($_POST);
//$amoCrm->documentation();