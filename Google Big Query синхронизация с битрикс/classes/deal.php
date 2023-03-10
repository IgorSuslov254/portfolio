<?php
namespace bigQuery;

require_once('bigQueryInterfase.php');
require_once('bigQueryTrait.php');

/**
 * The class responsible for transactions
 */
class Deal implements BigQueryInterfase
{
    use bigQueryTrait;

    private array $data;
    private static array $fields;

    function __construct()
    {
        self::$fields = json_decode(file_get_contents(__DIR__ . "/../config/dealFields.json"), true);
    }

    /**
     * @see BigQueryInterfase::getList
     */
    public function getList(string $typeEntity, string $tabel, string $chema)
    :array
    {
        $date = date('d.m.Y');
        $dateBefore = date('d.m.Y', strtotime($date .' -1 day'));

        $offset = 0;
        while (true){
            unset($this->data);

            $objDeal = \CCrmDeal::GetList(
                ['DATE_CREATE' => 'ASC'],
                [
                    'CHECK_PERMISSIONS' => 'N',
                    '>ID' => $offset,
                    [
                        'LOGIC' => 'OR',
                        [
                            '>=DATE_CREATE' => $dateBefore . " 00:00:00",
                            '<DATE_CREATE' => $date . " 00:00:00",
                        ],
                        [
                            '>=DATE_MODIFY' => $dateBefore . " 00:00:00",
                            '<DATE_MODIFY' => $date . " 00:00:00",
                        ]
                    ]
                ],
                [],
                1000
            );

            if (!$objDeal) return [
                "status" => "no",
                "title" => "Negociaciones",
                "message" => "No hay datos para sincronizar"
            ];

            $responseBG = $this->dataCastomization($objDeal);
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
        while ($arrDeal = $data->Fetch()) {
            $this->getDealById($arrDeal);
            $this->getObserver($arrDeal);
            $this->getUserForId($arrDeal);
            $this->getNameCategory($arrDeal);
            $this->data[$arrDeal["ID"]] = $arrDeal;
        }

        if(empty($this->data)) return [
            "status" => "no",
            "title" => "Negociaciones",
            "message" => "No hay datos para sincronizar"
        ];

        $this->getFinallyData($this->getUserFields());

        return $this->sendData($this->data, "Actividades_Negociacion", "Negociaciones");
    }

    /**
     * Receives detailed deal data
     * @param array &$arrDeal = data of a specific deal
     * @return void
     */
    private function getDealById(&$arrDeal)
    :void
    {
        $dataDeal = \CCrmDeal::GetByID($arrDeal["ID"], false);
        if ($dataDeal) {
            foreach ([
                "CATEGORY_ID",
                "IS_RETURN_CUSTOMER",
                "STAGE_ID",
                "SOURCE_ID",
                "SOURCE_DESCRIPTION",
                "COMPANY_ID",
                "CONTACT_ID",
                "MOVED_BY_ID",
                "WEBFORM_ID",
                "UTM_SOURCE",
                "UTM_MEDIUM",
                "UTM_CAMPAIGN",
                "UTM_CONTENT",
                "UTM_TERM"
            ] as $value) {
                if ($value == "STAGE_ID") $dataDeal[$value] = \CCrmDeal::GetStageName($dataDeal[$value], $dataDeal["CATEGORY_ID"]) ?? "";
                if ($value == "SOURCE_ID") $dataDeal[$value] = \CCrmStatus::GetStatusList('SOURCE')[$dataDeal[$value]] ?? "";
                if ($value == "COMPANY_ID") $dataDeal[$value] = \CCrmCompany::GetByID($dataDeal[$value], false)["TITLE"] ?? "";
                if ($value == "CONTACT_ID") $dataDeal[$value] = \CCrmContact::GetByID($dataDeal[$value], false)["FULL_NAME"] ?? "";
                if ($value == "WEBFORM_ID") $dataDeal[$value] = \Bitrix\Crm\Activity\Provider\WebForm::getTypeName($dataDeal[$value]) ?? "";

                $arrDeal[$value] = $dataDeal[$value] ?? "";
            }
        }
    }

    /**
     * Retrieves user data
     * @param array &$arrDeal = data of a specific deal
     * @return void
     */
    private function getUserForId(&$arrDeal)
    :void
    {
        $nameIdUsers = [
            "ASSIGNED_BY_ID",
            "CREATED_BY_ID",
            "MOVED_BY_ID",
            "UF_CRM_627164E163913",
            "UF_CRM_6294DDFEE8EF0",
            "UF_CRM_62DEAE3B4DF2C",
            "UF_CRM_1653660634",
            "UF_CRM_62AA4FA66F28A",
            "UF_CRM_62B1D774E94C9"
        ];

        foreach ($nameIdUsers as $nameIdUser){
            if (empty($arrDeal[$nameIdUser])) continue;

            $arrDeal[$nameIdUser."_ID"] = $arrDeal[$nameIdUser];

            $userIds[] = $arrDeal[$nameIdUser];
        }

        if(empty($userIds)) return;

        $objUser = \CUser::GetList(
            "",
            "",
            [
                "ID" => implode(" | ", $userIds)
            ],
            [
                "FIELDS" => ["ID", "NAME", "LAST_NAME"]
            ]
        );


        if (!$objUser) return;

        while ($arrUser = $objUser->Fetch()) {
            $users[] = $arrUser;
        }

        if (empty($users)) return;

        foreach ($nameIdUsers as $nameIdUser){
            foreach ($users as $user){
                if ($arrDeal[$nameIdUser] == $user["ID"]){
                    $arrDeal[$nameIdUser] = $user["NAME"] . " " . $user["LAST_NAME"];
                    break;
                }
            }
        }
    }

    /**
     * Get the data of the responsible
     * @param array &$arrDeal = data of a specific deal
     * @return void
     */
    private function getObserver(&$arrDeal)
    :void
    {
        $arrDeal["OBSERVER_ID"] = implode(" | ", \Bitrix\Crm\Observer\ObserverManager::getEntityObserverIDs(\CCrmOwnerType::Deal, $arrDeal["ID"]));
    }

    /**
     * Get the category name
     * @param array &$arrDeal = data of a specific deal
     * @return void
     */
    private function getNameCategory(&$arrDeal)
    :void
    {
        $arrDeal["CATEGORY_NAME"] = \Bitrix\Crm\Category\DealCategory::get($arrDeal["CATEGORY_ID"])["NAME"];
    }

    /**
     * Get the value of the UF fields
     * @return array
     */
    private function getUserFields()
    :array
    {
        $response = [];

        $objUserTypeEntity = \CUserTypeEntity::GetList();
        while ($arrUserTypeEntity = $objUserTypeEntity->GetNext()) {
            $userFieldsByIds[$arrUserTypeEntity["ID"]] = $arrUserTypeEntity["FIELD_NAME"];
        }

        $objUserFieldEnum = \CUserFieldEnum::GetList();
        while ($arrUserFieldEnum = $objUserFieldEnum->GetNext()) {
            $response[$userFieldsByIds[$arrUserFieldEnum['USER_FIELD_ID']]][$arrUserFieldEnum["ID"]] = $arrUserFieldEnum["VALUE"];
        }

        return $response;
    }
}