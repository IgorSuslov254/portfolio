<?php
namespace bigQuery;

require_once('bigQueryInterfase.php');
require_once('bigQueryTrait.php');

/**
 * The class responsible for working with activities
 */
class Activity implements BigQueryInterfase
{
    use bigQueryTrait;

    private array $data;
    private static array $fields;
    private static string $chema;
    private static string $tabel;

    function __construct()
    {
        self::$fields = json_decode(file_get_contents(__DIR__ . "/../config/activityFields.json"), true);
    }

    /**
     * @see BigQueryInterfase::getList
     */
    public function getList(string $typeEntity, string $chema, string $tabel)
    :array
    {
        self::$chema = $chema;
        self::$tabel = $tabel;

        if ($tabel == "Actividades_prospectos"){
            self::$fields["OWNER_ID"]["name"] = "ID_LEAD";
        }

        $date = date('d.m.Y');
        $dateBefore = date('d.m.Y', strtotime($date .' -1 day'));

        $offset = 0;
        while (true){
            unset($this->data);

            $objActivity = \CCrmActivity::GetList(
                ["ID" => "ASC"],
                [
                    "CHECK_PERMISSIONS" => "N",
                    "OWNER_TYPE_ID" => \CCrmOwnerType::ResolveID($typeEntity),
                    '>ID' => $offset,
                    ">=LAST_UPDATED" => $dateBefore . " 00:00:00",
                    "<LAST_UPDATED" => $date . " 00:00:00",
                ],
                false,
                [
                    "nPageSize" => 10000
                ],
            );

            if (!$objActivity) return [
                "status" => "no",
                "title" => self::$tabel,
                "message" => "No hay datos para sincronizar"
            ];

            $responseBG = $this->dataCastomization($objActivity);
            $response[] = $responseBG;

            if ($responseBG["status"] == "ok") $offset = $responseBG["limit"];
            if ($responseBG["status"] == "no") return $response;
        }
    }

    /**
     * @see BigQueryInterfase::dataCastomization
     */
    public function dataCastomization(object $data)
    :array
    {
        while ($arrActivity = $data->Fetch()) {
            $this->getFullResponibleName($arrActivity);
            $this->data[$arrActivity["ID"]] = $arrActivity;
        }

        if(empty($this->data)) return [
            "status" => "no",
            "title" => self::$tabel,
            "message" => "No hay datos para sincronizar"
        ];

        $this->getFinallyData();

        return $this->sendData($this->data, self::$chema, self::$tabel);
    }

    /**
     * Converts name in full name
     * @param array &$arrActivity = specific activity data
     * @return void
     */
    private function getFullResponibleName(&$arrActivity)
    :void
    {
        $arrActivity["RESPONSIBLE_NAME"] = $arrActivity["RESPONSIBLE_NAME"]." ".$arrActivity["RESPONSIBLE_LAST_NAME"]." ".$arrActivity["RESPONSIBLE_SECOND_NAME"];
    }
}