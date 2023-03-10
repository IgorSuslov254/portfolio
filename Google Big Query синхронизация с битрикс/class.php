<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

require_once('classes/lead.php');
require_once('classes/deal.php');
require_once('classes/user.php');
require_once('classes/activity.php');

use Bitrix\Main\Loader;
use bigQuery;

Loader::includeModule('crm');
Loader::includeModule('main');

/**
 * The base class that implements the logic of the component
 */
class BitBigQuery extends \CBitrixComponent
{
    /**
     * The function in which the call arrives from the cron
     * Implemented polymorphism for the ability to send tables both all at once and one at a time
     * @param void
     * @return void
     */
    public function executeComponent()
    {
        echo "Hello World!";
        return;
        $nameClasses = ["Lead", "Deal", "User", "ActivityLead", "ActivityDeal"];

        if (!empty($this->arParams['className'])) $nameClasses = [$this->arParams['className']];

        foreach ($nameClasses as $nameClass){
            $typeEntity = "LEAD";
            $chema = "Actividades_de_leads";
            $tabel = "Actividades_prospectos";

            if ($nameClass == "ActivityDeal"){
                $typeEntity = "DEAL";
                $chema = "Actividades_Negociacion";
                $tabel = "Actividades_negociaciones";
            }

            if ($nameClass == "ActivityLead" || $nameClass == "ActivityDeal") $nameClass = "Activity";

            $class = "\bigQuery\\".$nameClass;

            $staticView = new $class();
            $answerBigQuery[] = $staticView->getList($typeEntity, $chema, $tabel);
        }

        self::sendTelegram($answerBigQuery);
    }

    /**
     * Sends a report in telegram
     * @param array response from GBQ
     * @return string response from telegrams
     */
    private static function sendTelegram(array $answerBigQuery)
    :string
    {
        $token = '5454763727:AAFvtgAs451HFuOjEiepYT8Tdc6d2HafNwM';
        $chatId = '-1001814038613';

        $ch = curl_init("https://api.telegram.org/bot".$token."/sendMessage?chat_id=".$chatId."&parse_mode=html&text=".json_encode($answerBigQuery));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }
}