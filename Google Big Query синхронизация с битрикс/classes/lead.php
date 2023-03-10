<?php
namespace bigQuery;

require_once('bigQueryInterfase.php');
require_once('bigQueryTrait.php');

/**
 * The class responsible for leads
 */
class Lead implements BigQueryInterfase
{
    use bigQueryTrait;

    private array $data;
    private static array $fields;

    function __construct()
	{
        self::$fields = json_decode(file_get_contents(__DIR__ . "/../config/leadFields.json"), true);
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
            $objLead = \CCrmLead::GetList(
                ['ID' => 'ASC'],
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

            if (!$objLead) return [
                "status" => "no",
                "title" => "Prospectos",
                "message" => "No hay datos para sincronizar"
            ];

            $responseBG = $this->dataCastomization($objLead);
            $response[] = $responseBG;

            if ($responseBG["status"] == "ok") $offset = $responseBG["limit"];
            if ($responseBG["status"] == "no") return $response;
        }
    }

    /**
     * @see BigQueryInterfase::dataCastomization
     */
    public function dataCastomization(object $objLead)
    :array
    {
        $leadStatusNames = \CCrmLead::GetStatusNames();

        while ($arrLead = $objLead->Fetch()) {
            if ($leadStatusNames) $this->getStatusValue($arrLead, $leadStatusNames);

            $this->getWebForm($arrLead);
            $this->getSourceValue($arrLead);
            $this->getUtm($arrLead);
            $this->getObserver($arrLead);
            $this->getUserForId($arrLead);
            $this->data[$arrLead["ID"]] = $arrLead;
            $idLeads[] = $arrLead["ID"];
        }

        if(empty($this->data)) return [
            "status" => "no",
            "title" => "Prospectos",
            "message" => "No hay datos para sincronizar",
        ];

        if (!empty($idLeads)) $this->getPhonesOrEmails($idLeads);
        if (!empty($idLeads)) $this->getProducts($idLeads);
        $this->getFinallyData($this->getUserFields());

        return $this->sendData($this->data, "Actividades_de_leads", "Prospectos");
    }

    /**
     * Get phone numbers and email addresses of the lead
     * @param array $idLeads = specific lead
     * @return void
     */
    private function getPhonesOrEmails($idLeads)
    :void
    {
        $objData = \CCrmFieldMulti::GetList(
            [],
            [
                "ENTITY_ID" => \CCrmOwnerType::LeadName,
                "ELEMENT_ID" => $idLeads,
                "TYPE_ID" => ["PHONE", "EMAIL"]
            ]
        );

        $isOne = [
            "PHONE" => true,
            "EMAIL" => true
        ];

        while ($arrData = $objData->Fetch()) {
            switch ($arrData["TYPE_ID"]) {
                case "PHONE":
                    $typeId = "PHONE";
                    break;
                case "EMAIL":
                    $typeId = "EMAIL";
                    break;
            }

            if (!empty($data[$arrData["ELEMENT_ID"]][$typeId])) $isOne[$typeId] = false;

            if ($isOne[$typeId]){
                $this->data[$arrData["ELEMENT_ID"]][$typeId] = $arrData["VALUE"];
            } else{
                $this->data[$arrData["ELEMENT_ID"]][$typeId] .= ", ".$arrData["VALUE"];
            }
        }
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

    /**
     * Get the name of the lead status
     * @param array $idLeads = specific lead, $leadStatusNames = status names
     * @return void
     */
    private function getStatusValue(&$arrLead, $leadStatusNames)
    :void
    {
        $arrLead["STATUS_ID"] = $leadStatusNames[$arrLead["STATUS_ID"]];
    }

    /**
     * Get the name of the web form
     * @param array $idLeads = specific lead
     * @return void
     */
    private function getWebForm(&$arrLead)
    :void
    {
        $arrLead["WEBFORM_ID"] = \Bitrix\Crm\Activity\Provider\WebForm::getTypeName(\CCrmLead::GetByID($arrLead["ID"], false)["WEBFORM_ID"]);
    }

    /**
     * Get the name of the source receipt lead
     * @param array $idLeads = specific lead
     * @return void
     */
    private function getSourceValue(&$arrLead)
    :void
    {
        $arrLead["SOURCE_ID"] = \CCrmStatus::GetStatusList('SOURCE')[$arrLead["SOURCE_ID"]];
    }

    /**
     * Get user data
     * @param array $idLeads = specific lead
     * @return void
     */
    private function getUserForId(&$arrLead)
    :void
    {
        $nameIdUsers = [
            "ASSIGNED_BY_ID",
            "CREATED_BY_ID",
            "MODIFY_BY_ID",
            "UF_CRM_1651169867",
            "UF_CRM_1653922231",
            "UF_CRM_1658758488",
        ];

        foreach ($nameIdUsers as $nameIdUser){
            if (empty($arrLead[$nameIdUser])) continue;

            $arrLead[$nameIdUser."_ID"] = $arrLead[$nameIdUser];

            $userIds[] = $arrLead[$nameIdUser];
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
                if ($arrLead[$nameIdUser] == $user["ID"]){
                    $arrLead[$nameIdUser] = $user["NAME"] . " " . $user["LAST_NAME"];
                    break;
                }
            }
        }
    }

    /**
     * Get UTM tag data
     * @param array $idLeads = specific lead
     * @return void
     */
    private function getUtm(&$arrLead)
    :void
    {
        $dataLead = \CCrmLead::GetByID($arrLead["ID"], false);
        if ($dataLead) {
            foreach (["UTM_SOURCE", "UTM_MEDIUM", "UTM_CAMPAIGN", "UTM_CONTENT", "UTM_TERM"] as $utm) {
                $arrLead[$utm] = $dataLead[$utm] ?? "";
            }
        }
    }

    /**
     * Get the full name of the responsible
     * @param array $idLeads = specific lead
     * @return void
     */
    private function getObserver(&$arrLead)
    :void
    {
        $this->data[$arrLead["ID"]]["OBSERVER_ID"] = "";
        $observerManagers = \Bitrix\Crm\Observer\ObserverManager::getEntityObserverIDs(\CCrmOwnerType::Lead, $arrLead["ID"]);

        $arrLead["OBSERVER_ID_ID"] = implode(" | ", $observerManagers);

        if($observerManagers) {
            $objUser = \CUser::GetList(
                "",
                "",
                [
                    "ID" => implode(" | ", $observerManagers)
                ],
                [
                    "FIELDS" => ["NAME", "LAST_NAME"]
                ]
            );
        }

        if ($objUser) {
            $concat = "";
            $i = 0;
            while ($arrUser = $objUser->Fetch()) {
                if ($i > 0) $concat = ", ";

                $arrLead["OBSERVER_ID"] .= $concat . implode(" ", $arrUser);

                $i++;
            }
        }
    }

    /**
     * Get product data
     * @param array $idLeads = specific lead
     * @return void
     */
    private function getProducts($idLeads)
    :void
    {
        $arrParams = ["ID", "OWNER_ID", "PRODUCT_NAME", "PRICE", "QUANTITY"];
        $objData = \CCrmProductRow::GetList(
            [],
            [
                'OWNER_TYPE' => 'L',
                'OWNER_ID' => $idLeads
            ],
            false,
            false,
            $arrParams
        );
        if ($objData) {
            while ($arrData = $objData->Fetch()){
                foreach ($arrParams as $arrParam) {
                    if ($arrParam == "ID" || $arrParam == "OWNER_ID") continue;

                    if (!empty($this->data[$arrData["OWNER_ID"]][$arrParam])) {
                        $contact = ", ";
                    } else {
                        $contact = "";
                        $this->data[$arrData["OWNER_ID"]][$arrParam] = "";
                    }

                    $this->data[$arrData["OWNER_ID"]][$arrParam] .= $contact . $arrData[$arrParam];
                }
            }
        }
    }
}