<?php
namespace bigQuery;

use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Core\ExponentialBackoff;

/**
 * Trait for working with the GBQ library
 */
trait bigQueryTrait
{
    /**
     * Sends data to GBQ
     * @param array|string $data = data to send, $dataset = name of the table pool, $tableName = table name
     * @return array ответ от GBQ
     */
    public function sendData($data = [], $dataset = "", $tableName = "")
    :array
    {
        if (empty($data) || empty($dataset) || empty($tableName)){
            $message = "No hay datos para sincronizar";
            if (empty($dataset)) $message = "dataset no especificado";
            if (empty($table)) $message = "table no especificado";

            return [
                "status" => "no",
                "title" => $table,
                "message" => $message
            ];
        }

        $bigQuery = new BigQueryClient([
            'keyFile' => [
                "type" => "service_account",
                "project_id" => "casas360-298116",
                "private_key_id" => "357870364a9b4c45862769a182cef69695bfa493",
                "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC4NBmqIfPAgut0\n0L9ghrsRXqByef9IC5l363O1KvuWrcQcz+pJ3Oec63/xvZqNwOjYUeEcrWIghBHQ\n2PVP1+IimN2Z7TXdJ6Ijwb3SnfYevmdnsExyL5qp0fHXVq0Ysjt+IU1gPo8TqhL+\noQBS87ZT05PimmSf5fWjoMtY1mrbzkQPE5jy6aIxN9By9yAYQ+Ut79DrDy0D6unQ\n6ruemU2LSVUxPHYFB7exQcoFZsmyoSGY1f9bC6kTVTDnOqkrfdzHbzlWWdxtft77\n0f4XJPXt2SCMt1hvQB2FGh+2S2jAfuhpMnke+Ur+IwFc/jrAAuMPgexDFMdQu60Q\nvuiycAINAgMBAAECggEAGwkEikik6Kiea7z2lbF4k8ZOaLbYnaecLPJY/2pRVi5n\nRe5LHe+m68xRsTaJkEwTsKolzspwyEJyBfPN31vZVAgZIC4lVXHrdXGKTXcDKER8\nU0bYFXtAqTOH2fnd+m2wVyUGoP1VjQaNdqNFM6eS5086mYvdAG3lJVCkf8Oql9t4\nQXdwofUVgER2C8/xk7ex0uWSu7RV+PQCHkaGdCDQBuMcIdNunRgt2mcyirvxYtED\nmRgPBpf97uqMSWvrmGRlANu31D96bfIN+jy4HqKJZXIofKkYQjwa5ufFxOsBPWpG\n/6DtREIIuUz2F1ie/KpC9X5tcREVYfHzdwiWJYe7UQKBgQD1LcxUwS6w2hVunq1Q\n1BRw0BlQ/MeH3wyYPamLRkssUCe6dEbVgXwTt0lKR/LmQMSy3sCzriv5Njuw37qQ\ncQN+UdoDuv6pzcHFmSNhj+UoCYDy92JcFmkDeSLEZHG86Wi0AaS8ta5xwbhQbhRg\nMvZtLpEb1fvKePsuRkEg7wbdPQKBgQDAVVwaYPBdvUF/6qGxu6ouMBcJcQQZVga5\neK0ScHNwgRB6NcS7FMz2BUs9JTDDVXqqbx5mu1GEauqc6efu71/9bcJGwRgmkBpb\n68uyzVJQXvT0IjpabYYCgy6cXhsPMDLq52Y3SVBAeNxFrmMfDmOxhaRbyZbxaTgL\nT24ozB+lEQKBgCJcndOPE50jo3dSc9XtM0QwViv8kXZgc2Ju2fE0E9sNDNRb8YWA\n1UPqHzJy7P8KOsca4wULtwwBZtI+OOZ/gE7W5+g37/MabDrmYIgO4739vv3OYBGp\n7mPXOWEu9qXUTKFVzaHJEL7OuKdFFkP5QIJC0YGGNvjEheeRXxVfiqW9AoGAM2Da\nGMXaj//1llk0Vok+PdZk4QXKxYXgh/0/ppRZki04HJ1ub1dwJSxm2++qZhPYj9zd\nLy5M6WWyRLaLWZ2ic821Pzdn3y1RjUqhAKOuoH+tSeY21l85lEQ82FKfQeBrSasE\n+DHieUaP+SFLo0kxwoNj0403gEfOS98tG0LkFTECgYBuoNL3K9aCBw/j8xs6Z/ly\nMgTFCw2vfei3wQUMYTWQoGyUmPAuVHtbWOHUrMwForXWfoRgwbrdDASJXTC/WmVv\ncf8Ttqk2XhFXJpC2en0fZclu8hDCStyui4fZHlIS7draznkCRKaJO07mvjSOgXN9\n2lMj4eTttQygyWj90uPRoA==\n-----END PRIVATE KEY-----\n",
                "client_email" => "intagration-for-bitrix24@casas360-298116.iam.gserviceaccount.com",
                "client_id" => "110897963754841696302",
                "auth_uri" => "https =>//accounts.google.com/o/oauth2/auth",
                "token_uri" => "https =>//oauth2.googleapis.com/token",
                "auth_provider_x509_cert_url" => "https =>//www.googleapis.com/oauth2/v1/certs",
                "client_x509_cert_url" => "https =>//www.googleapis.com/robot/v1/metadata/x509/intagration-for-bitrix24%40casas360-298116.iam.gserviceaccount.com"
            ]
        ]);

        $dataset = $bigQuery->dataset($dataset);
        $table = $dataset->table($tableName);

        $sendData = $limit = $since = "";
        $count = 0;

        foreach ($data as $key => $dataValue){
            $concat = ($count == (count($data) - 1)) ? "" : "\n";
            $sendData .= json_encode(str_replace(["\n","\r"],"",$dataValue)) . $concat;

            if ($count == 0) $since = $key;

            $limit = $key;
            $count = $count + 1;
        }

        $response = [
            "status" => "no",
            "title" => $tableName,
            "message" => "debug"
        ];

        $loadConfig = $table->load($sendData)->sourceFormat('NEWLINE_DELIMITED_JSON');
        $job = $table->runJob($loadConfig);

        $backoff = new ExponentialBackoff(10);
        $backoff->execute(function () use ($job) {
            print('Waiting for job to complete' . PHP_EOL);
            $job->reload();
            if (!$job->isComplete()) {
                throw new Exception('El trabajo aún no se ha completado', 500);
            }
        });

        /*$response = [
            "status" => "no",
            "title" => $tableName,
            "message" => "debug ".$job->info().'Datos del '.$since.' al '.$limit.' importados con exito'
        ];*/

        if (isset($job->info()['status']['errorResult'])) {
            $error = $job->info()['status']['errorResult']['message'];
            $response = [
              "status" => "no",
              "title" => $tableName,
              "message" => 'Error al ejecutar la tarea '.$since.' a '.$limit.': ' . $error
            ];
            printf('Error running job: %s' . PHP_EOL, $error);
        } else {
            $response = [
                "status" => "ok",
                "title" => $tableName,
                "message" => 'Datos del '.$since.' al '.$limit.' importados con exito',
                "limit" => $limit
            ];
            print('Data imported successfully' . PHP_EOL);
        }

        unset($sendData);
        unset($loadConfig);
        unset($job);
        unset($bigQuery);
        unset($dataset);
        unset($table);
        unset($backoff);

        return $response;
    }

    /**
     * Converts data to GBQ format
     * @param array $userFields = data to be replaced with
     * @return void
     */
    private function getFinallyData($userFields = [])
    :void
    {
        $finallyData = [];

        foreach ($this->data as $key => $data){
            foreach (self::$fields as $field => $fieldValue){
                if ($field == "UF_DEPARTMENT_NAME" && !empty($finallyData[$key]["UF_DEPARTMENT"])) $data[$fieldValue["name"]] = \Bitrix\Main\UserUtils::getDepartmentName($finallyData[$key]["UF_DEPARTMENT"])["NAME"];

                if (empty($data[$field]) || $field == "COMMENTS"){
                    $finallyData[$key][$fieldValue["name"]] = "";
                    continue;
                }

                if (is_array($data[$field])){
                    unset($dataFieldValue);
                    foreach ($data[$field] as $dataField) {
                        $dataFieldValue[] = self::ifFinallyData($userFields, $field, $dataField, $fieldValue);
                    }

                    if ($field == "UF_DEPARTMENT"){
                        $finallyData[$key][$fieldValue["name"]] = $dataFieldValue[count($dataFieldValue) - 1];
                    } else {
                        $finallyData[$key][$fieldValue["name"]] = implode(" | ", $dataFieldValue);
                    }
                } else{
                    $dataField = $data[$field];
                    $finallyData[$key][$fieldValue["name"]] = self::ifFinallyData($userFields, $field, $dataField, $fieldValue);
                }
            }
        }

        $this->data = $finallyData;
    }

    /**
     * Leads to the desired date format
     * @param array|string $userFields = data to be replaced with, $field = record id, $dataField = field name, $fieldValue = data that does not require replacement
     * @return string
     */
    private static function ifFinallyData($userFields = [], $field = "", $dataField = "", $fieldValue = [])
    {
        $response = (empty($userFields[$field][$dataField]) && empty($dataField))
            ? ""
            : ((empty($userFields[$field][$dataField]))
                ? (($fieldValue["type"] == "DATETIME" || $fieldValue["type"] == "DATE")
                    ? ($fieldValue["type"] == "DATETIME")
                        ? date("Y-m-d H:i:s", strtotime($dataField))
                        : date("Y-m-d", strtotime($dataField))
                    : $dataField)
                : (($fieldValue["type"] == "DATETIME" || $fieldValue["type"] == "DATE")
                    ? ($fieldValue["type"] == "DATETIME")
                        ? date("Y-m-d H:i:s", strtotime($userFields[$field][$dataField]))
                        : date("Y-m-d", strtotime($userFields[$field][$dataField]))
                    : $userFields[$field][$dataField]));
        return $response;
    }
}