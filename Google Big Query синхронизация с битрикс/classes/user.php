<?php
namespace bigQuery;

require_once('bigQueryInterfase.php');
require_once('bigQueryTrait.php');

/**
 * The class responsible for users
 */
class User implements BigQueryInterfase
{
    use bigQueryTrait;

    private array $data;
    private static array $fields;

    function __construct()
    {
        self::$fields = json_decode(file_get_contents(__DIR__ . "/../config/userFields.json"), true);
    }

    /**
     * @see BigQueryInterfase::getList
     */
    public function getList(string $typeEntity, string $tabel, string $chema)
    :array
    {
        $date = date('d.m.Y')." 00:00:00";
        $dateBefore = date('d.m.Y', strtotime($date .' -1 day'))." 00:00:00";

        $objUser = \CUser::GetList(
            ($by="date_register"),
            ($order="asc"),
            [
                "CHECK_PERMISSIONS" => "N",
                "DATE_REGISTER_1" => $dateBefore,
                "DATE_REGISTER_2" => $dateBefore
            ],
        );

        if (!$objUser) return [
            "status" => "no",
            "title" => "Empleados",
            "message" => "No hay datos para sincronizar"
        ];

        return $this->dataCastomization($objUser);
    }

    /**
     * @see BigQueryInterfase::dataCastomization
     */
    public function dataCastomization(object $data)
    :array
    {
        while ($arrUser = $data->Fetch()) {
            $this->getUserById($arrUser);
            $this->data[$arrUser["ID"]] = $arrUser;
        }

        if(empty($this->data)) return [
            "status" => "no",
            "title" => "Empleados",
            "message" => "No hay datos para sincronizar"
        ];

        $this->getFinallyData();
        
        return $this->sendData($this->data, "Actividades_de_leads", "Empleados");
    }

    /**
     * Get detailed information about the user
     * @param array &$arrUser = data about a specific user
     * @return void
     */
    private function getUserById(&$arrUser)
    :void
    {
        $objectUser = \CUser::GetByID($arrUser["ID"], false);

        while ($data = $objectUser->Fetch()){
            $dataUser = $data;
        }

        if ($dataUser) {
            foreach (["UF_PHONE_INNER", "UF_DEPARTMENT", "UF_EMPLOYMENT_DATE"] as $value) {
                $arrUser[$value] = $dataUser[$value] ?? "";
            }
        }
    }
}