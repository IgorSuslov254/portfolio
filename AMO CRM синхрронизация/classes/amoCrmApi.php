<?php
namespace amo;

require_once("db.php");
require_once("exceptions/amoException.php");
require_once("exceptions/fileException.php");
require_once(__DIR__ . "/../lang/ru/amo.php");

/**
 * amoCRM api class
 * @author Suslov Igor <IUSuslov@1cbit.ru>
 */
class AmoCrmApi
{
    /**
     * amoCRM subdomain
     * @var string
     */
    private static $subdomain;

    /**
     * Secret key
     * @var string
     */
    private static $clientSecret;

    /**
     * Integration ID
     * @var string
     */
    private static $clientId;

    /**
     * Authorization code
     * @var string
     */
    private static $code;

    /**
     * redirect uri
     * @var string
     */
    private static $redirectUri;

    /**
     * access token
     * @var string
     */
    private static $accessToken;

    /**
     * pipeline id
     * @var array
     */
    private static $pipelineId;

    /**
     * user amo
     * @var int
     */
    private static $userAmo;

    /**
     * @throws FileException
     */
    public function __construct()
    {
        try {
            if (!($file = file_get_contents(__DIR__ . "/../config/amo.json"))) throw new FileException($GLOBALS["lang"]["errorFile"], __DIR__ . "/../config/amo.json");
        } catch (FileException $e) {
            http_response_code(404);
            print $e->getMessage();
            die();
        }

        $amoConfig = json_decode($file, true);

        self::$subdomain = $amoConfig["subdomain"];
        self::$clientSecret = $amoConfig["clientSecret"];
        self::$clientId = $amoConfig["clientId"];
        self::$code = $amoConfig["code"];
        self::$redirectUri = /*((!empty($_SERVER['HTTPS'])) ? 'https://' : 'http://')*/ "https://" . $_SERVER['HTTP_HOST'] . $amoConfig["redirectUri"];
        self::$pipelineId = $amoConfig["pipelineId"];
        self::$userAmo = $amoConfig["userAmo"];
    }

    /**
     * create lead
     * @param array $data
     * @return void
     */
    public function index(array $data)
    : string
    {
        $this->auth();

        $params = [
            "type" => "createLide",
            "url" => "https://".self::$subdomain.".amocrm.ru/api/v4/leads/complex",
            "headers" => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . self::$accessToken,
            ],
            "data" => [
                [
                    "name" => (!empty($data["phone"])) ? $data["phone"] : "",
                    "price" => (!empty($data["price"])) ? (int) $data["price"] : 0,
                    "responsible_user_id" => (int) self::$userAmo,
                    "pipeline_id" => (!empty(self::$pipelineId[$data["city"]])) ? (int) self::$pipelineId[$data["city"]] : (int) self::$pipelineId[$GLOBALS["lang"]["defaultCity"]],
                    "_embedded" => [
                        "contacts" => [
                            [
                                "first_name" => (!empty($data["name"])) ? $data["name"] : "",
                                "custom_fields_values" => [
                                    [
                                        "field_code" => "EMAIL",
                                        "values" => [
                                            [
                                                "enum_code" => "WORK",
                                                "value" => (!empty($data["email"])) ? $data["email"] : ""
                                            ]
                                        ]
                                    ],
                                    [
                                        "field_code" => "PHONE",
                                        "values" => [
                                            [
                                                "enum_code" => "WORK",
                                                "value" => (!empty($data["phone"])) ? $data["phone"] : ""
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ],
                    "custom_fields_values" => [
                        [
                            "field_code" => 'UTM_SOURCE',
                            "values" => [
                                [
                                    "value" => (!empty($data["utm_source"])) ? $data["utm_source"] : ""
                                ]
                            ]
                        ],
                        [
                            "field_code" => 'UTM_CONTENT',
                            "values" => [
                                [
                                    "value" => (!empty($data["utm_content"])) ? $data["utm_content"] : ""
                                ]
                            ]
                        ],
                        [
                            "field_code" => 'UTM_MEDIUM',
                            "values" => [
                                [
                                    "value" => (!empty($data["utm_medium"])) ? $data["utm_medium"] : ""
                                ]
                            ]
                        ],
                        [
                            "field_code" => 'UTM_CAMPAIGN',
                            "values" => [
                                [
                                    "value" => (!empty($data["utm_campaign"])) ? $data["utm_campaign"] : ""
                                ]
                            ]
                        ],
                        [
                            "field_code" => 'UTM_TERM',
                            "values" => [
                                [
                                    "value" => (!empty($data["utm_term"])) ? $data["utm_term"] : ""
                                ]
                            ]
                        ],
                        [
                            "field_code" => 'UTM_REFERRER',
                            "values" => [
                                [
                                    "value" => (!empty($data["utm_referrer"])) ? $data["utm_referrer"] : ""
                                ]
                            ]
                        ],
                        [
                            "field_id" => 669201,
                            "values" => [
                                [
                                    "value" => $GLOBALS["lang"]["source"]
                                ]
                            ]
                        ],
                        [
                            "field_id" => 669777,
                            "values" => [
                                [
                                    "value" => (!empty($data["formName"])) ? $data["formName"] : ""
                                ]
                            ]
                        ],
                        [
                            "field_id" => 669779,
                            "values" => [
                                [
                                    "value" => (!empty($data["address"])) ? $data["address"] : ""
                                ]
                            ]
                        ],
                        [
                            "field_id" => 669781,
                            "values" => [
                                [
                                    "value" => (!empty($data["comment"])) ? $data["comment"] : ""
                                ]
                            ]
                        ],
                        [
                            "field_id" => 669783,
                            "values" => [
                                [
                                    "value" => (!empty(self::$pipelineId[$data["city"]])) ? $data["city"] : $GLOBALS["lang"]["defaultCity"]
                                ]
                            ]
                        ],
                        [
                            "field_id" => 669841,
                                "values" => [
                                    [
                                        "value" => (!empty($data["url"])) ? $data["url"] : ""
                                    ]
                                ]
                            ],
                    ],
                ]
            ]
        ];

        $response = $this->amoSrmCurl($params);

        $this->createNotes(json_decode($response, true), $data, $params);

        return $response;
    }

    /**
     * create notes
     * @param array $response
     * @param array $data
     * @param array $params
     * @return void
     * @throws AMOException
     */
    private function createNotes(array $response, array $data, array $params)
    : void
    {
        try {
            if (empty($response[0]["id"])) throw new AMOException($GLOBALS["lang"]["errorAmoNoIdEntity"], [$response, $params]);
        } catch (AMOException $e) {
            http_response_code(400);
            print $e->getMessage();
            die();
        }

        $text = "";
        foreach ($GLOBALS["lang"]["amoText"] as $key => $amoText) {
            if (!empty($data[$amoText])) $text .= $key.": ".$data[$amoText]."\r\n";
        }

        $params = [
            "type" => "createNotes",
            "url" => "https://".self::$subdomain.".amocrm.ru/api/v4/leads/notes",
            "headers" => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . self::$accessToken,
            ],
            "data" => [
                [
                    "entity_id" => $response[0]["id"],
                    "note_type" => "common",
                    "params" => [
                        "text" => $text
                    ]
                ]
            ]
        ];

        $this->amoSrmCurl($params);
    }

    /**
     * auth amoCRM
     * @return void
     * @throws DBException
     */
    private function auth()
    : void
    {
        $db = new \amo\DB();
        $amoToken = $db->query(
            "SELECT ".
                    "* ".
                "FROM ".
                    "`amo_tokens` ".
                "WHERE ".
                    "`id` = 1"
        );

        if (empty($amoToken[0]["endTokenTime"]) || $amoToken[0]["endTokenTime"] - 60 < time()) {
            $params = [
                "type" => "getToken",
                "url" => "https://".self::$subdomain.".amocrm.ru/oauth2/access_token",
                "headers" => ['Content-Type:application/json'],
                "data" => [
                    'client_id' => self::$clientId,
                    'client_secret' => self::$clientSecret,
                    'grant_type' => 'authorization_code',
                    'code' => self::$code,
                    'redirect_uri' => self::$redirectUri,
                ]
            ];

            if (!empty($amoToken[0]["endTokenTime"])){
                $params["data"]["grant_type"] = "refresh_token";
                $params["data"]["refresh_token"] = $amoToken[0]["refresh_token"];
                unset($params["data"]["code"]);
            }

            $amoSrmCurl = $this->amoSrmCurl($params);

            $response = json_decode($amoSrmCurl, true);

            $endTokenTime = $response['expires_in'] + time();

            $db->query(
                "UPDATE ".
                    "amo_tokens ".
                "SET ".
                    "access_token = '" . $response['access_token'] . "', ".
                    "refresh_token = '" . $response['refresh_token'] . "', ".
                    "token_type = '" . $response['token_type'] . "', ".
                    "expires_in = " . $response['expires_in'] . ", ".
                    "endTokenTime = " . $endTokenTime . " ".
                "WHERE ".
                    "id = 1"
            );

            self::$accessToken = $response['access_token'];
        } else{
            self::$accessToken = $amoToken[0]["access_token"];
        }
    }

    /**
     * get fields amoCRM
     * @return void
     */
    public function getLinks()
    : void
    {
        self::printLink('api/v4/leads/custom_fields', 'Список utm меток', self::$subdomain);
        self::printLink('api/v4/users', 'Список пользователей', self::$subdomain);
        self::printLink('api/v4/contacts/custom_fields', 'Список полей контакта', self::$subdomain);

        echo '<br>';
        echo "<a href='https://www.amocrm.ru/developers/content/crm_platform/custom-fields' target='_blank'>" . $GLOBALS["lang"]["amoDoc"] . "</a>";
        echo '<br>';
    }

    /**
     * print link
     * @param $method
     * @param $title
     * @param $subdomain
     * @return void
     */
    private static function printLink($method, $title, $subdomain)
    : void
    {
        echo '<br>';
        echo "<a href='https://$subdomain.amocrm.ru/$method' target='_blank'>$title</a>";
        echo '<br>';
    }

    /**
     * @param array $params
     * @return string
     * @throws AMOException
     */
    private function amoSrmCurl(array $params)
    : string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $params["url"]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params["data"]));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $params["headers"]);
        curl_setopt($curl, CURLOPT_HEADER, false);

        if ($params["type"] == "createLide" || $params["type"] == "createNotes"){
            curl_setopt($curl, CURLOPT_COOKIEFILE, 'AMO_CRM/logs/cookie.txt');
            curl_setopt($curl, CURLOPT_COOKIEJAR, 'AMO_CRM/logs/cookie.txt');
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if ($params["type"] == "getToken"){
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }

        $response = curl_exec($curl);
        $code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

        try {
            if ($code < 200 || $code > 204) throw new AMOException($GLOBALS["lang"]["errorAmo"], [curl_error($curl), $response, $params], $code);
        } catch (AMOException $e) {
            http_response_code($code);
            print $e->getMessage();
            die();
        }

        return $response;
    }
}