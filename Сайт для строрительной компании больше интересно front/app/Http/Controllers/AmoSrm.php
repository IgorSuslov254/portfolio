<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AmoToken;
use App\Jobs\MessageTelegram;
use Illuminate\Support\Facades\Log;

class AmoSrm extends Controller
{
    private $request;
    private static $subdomain;
    private static $clientSecret;
    private static $clientId;
    private static $code;
    private static $redirectUri;
    private static $accessToken;
    private static $pipelineId;
    private static $userAmo;

    public function __construct(Request $request)
    {
        $this->request = $request;
        self::$subdomain = "kraftthouse";
        self::$clientSecret = "kQLFEYuZoIR2abobzv1E0plVz5uCTMWrN9iPlCUGBptgN72XuhOB9aF4Ipr7Rvik";
        self::$clientId = "bd3112d3-f9c7-4d08-a93c-69950e953687";
        self::$code = "def502002358e4c4f9da9e86428513e6a90b28902c224e08e47443605084a1724c4df9bb8f9fa5569a964b5b50e6716b0e5ceeadb8f15bfb512076591831edab2057ea22ee1159b150163ea9ae0c36f8baa9a86c3ae1dba5256b9d5616a4c4cf596f19e3da41828ce11c1c5f9ba24583674690eff756ad790723b2a7679d037bfece9f72a7ab5f37c7160317db0d005b8558f0315c6f3629767391a9c79b269c0cdb3e7f92d2c806495c50af4a8183281d74ef27d6f0a2ca07a0099e597675340a1eb0033b80db4f5b311be05232411cb50df0d22ab3244d0e195545d1c179ee754a2bfe5f3a218663775308a0b50c97a558697b1f48162c735f90cf95647a7013962a4dd001607ab998e14ea3381a860298cb8db774fa5c3fa14ac3f069d1ee2e0798885a8ab5a78c89fe2bac5fbe54fd9049fcc4f595281b321ef1ddf1ba37572de4f1b2f9aebe5df12598ac2220ca7bef51204fa126c9f85dfd484fdf3f4971a04f734e0a2ad21817fd8bd46e9a448c92190309e0257c150d6402b60723dd5499375f612784ac8d901ccd03fa047d8eadd6b1efba4ce9c5b7409dd179325ea6ef60ff61236549d1639ae1f184bedc665e96d53164cef357f3afcce06de336b3c42c72cb3eb03582e505348b039f6a3cdbfe1d15f8bfbb12e21c5f74a76c9c7a";
        self::$redirectUri = url("/amo-srm");
        self::$pipelineId = "5430124";
        self::$userAmo = "8210197";
    }

    public function index()
    {
        $this->auth();

        $phone = $this->request->input("phone");
        $utmSource = $this->request->input("utmSource") ?? "";
        $utmContent = $this->request->input("utmContent") ?? "";
        $utmMedium = $this->request->input("utmMedium") ?? "";
        $utmCampaign = $this->request->input("utmCampaign") ?? "";
        $utmTerm = $this->request->input("utmTerm") ?? "";

        $params = [
            "type" => "createLide",
            "url" => "https://".self::$subdomain.".amocrm.ru/api/v4/leads/complex",
            "headers" => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . self::$accessToken,
            ],
            "data" => [
                [
                    "name" => $phone,
                    "responsible_user_id" => (int) self::$userAmo,
                    "pipeline_id" => (int) self::$pipelineId,
                    "_embedded" => [
                        "contacts" => [
                            [
                                "custom_fields_values" => [
                                    [
                                        "field_code" => "PHONE",
                                        "values" => [
                                            [
                                                "enum_code" => "WORK",
                                                "value" => $phone
                                            ]
                                        ]
                                    ],
                                ]
                            ]
                        ],
                    ],
                    "custom_fields_values" => [
                        [
                            "field_code" => 'UTM_SOURCE',
                            "values" => [
                                [
                                    "value" => $utmSource
                                ]
                            ]
                        ],
                        [
                            "field_code" => 'UTM_CONTENT',
                            "values" => [
                                [
                                    "value" => $utmContent
                                ]
                            ]
                        ],
                        [
                            "field_code" => 'UTM_MEDIUM',
                            "values" => [
                                [
                                    "value" => $utmMedium
                                ]
                            ]
                        ],
                        [
                            "field_code" => 'UTM_CAMPAIGN',
                            "values" => [
                                [
                                    "value" => $utmCampaign
                                ]
                            ]
                        ],
                        [
                            "field_code" => 'UTM_TERM',
                            "values" => [
                                [
                                    "value" => $utmTerm
                                ]
                            ]
                        ],
                        [
                            "field_id" => 1171839,
                            "values" => [
                                [
                                    "value" => "Сайт"
                                ]
                            ]
                        ],
                    ],
                ]
            ]
        ];
 
        $amoSrmCurl = $this->amoSrmCurl($params);

        $params["sendDocument"] = false;
        $params['message'] = "Синхронизация {$phone} c AmoCrm прошла успешно";

        if (!$amoSrmCurl["status"]){
            $params['message'] = "<b>Ошибка синхронизации c AmoCrm!</b> \r\nПроведите синхронизацию вручную";

            Log::error($amoSrmCurl);
        }

        MessageTelegram::dispatch($params);

        return response()->json($amoSrmCurl);
    }

    private function auth()
    {
        $amoToken = AmoToken::find(1);

        if (empty($amoToken)){
            $amoToken = new AmoToken;
        }

        if (empty($amoToken->endTokenTime) || $amoToken->endTokenTime - 60 < time()) {
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

            if (!empty($amoToken->endTokenTime)){
                $params["data"]["grant_type"] = "refresh_token";
                $params["data"]["refresh_token"] = $amoToken->refresh_token;
                unset($params["data"]["code"]);
            }

            $amoSrmCurl = $this->amoSrmCurl($params);

            if (!$amoSrmCurl["status"]){
                $params["sendDocument"] = false;
                $params['message'] = "<b>Ошибка синхронизации c AmoCrm!</b> \r\nНеудалось получить access_token";
                MessageTelegram::dispatch($params);

                Log::error($amoSrmCurl);

                return false;
            }

            $response = json_decode($amoSrmCurl["out"], true);

            $amoToken->access_token = $response['access_token'];
            $amoToken->refresh_token = $response['refresh_token'];
            $amoToken->token_type = $response['token_type'];
            $amoToken->expires_in = $response['expires_in'];
            $amoToken->endTokenTime = $response['expires_in'] + time();
            $amoToken->save();

            self::$accessToken = $response['access_token'];
        } else{
            self::$accessToken = $amoToken->access_token;
        }
    }

    public function getLinks()
    {
        self::printLink('api/v4/leads/custom_fields', 'Список utm меток', self::$subdomain);
        self::printLink('api/v4/users', 'Список пользователей', self::$subdomain);
        self::printLink('api/v4/contacts/custom_fields', 'Список полей контакта', self::$subdomain);

        echo '<br>';
        echo "<a href='https://www.amocrm.ru/developers/content/crm_platform/custom-fields' target='_blank'>Документация</a>";
        echo '<br>';
    }

    private static function printLink($method, $title, $subdomain)
    {
        echo '<br>';
        echo "<a href='https://$subdomain.amocrm.ru/$method' target='_blank'>$title</a>";
        echo '<br>';
    }

    private function amoSrmCurl($params = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $params["url"]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params["data"]));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $params["headers"]);
        curl_setopt($curl, CURLOPT_HEADER, false);

        if ($params["type"] == "createLide"){
            curl_setopt($curl, CURLOPT_COOKIEFILE, 'amo/cookie.txt');
            curl_setopt($curl, CURLOPT_COOKIEJAR, 'amo/cookie.txt');
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if ($params["type"] == "getToken"){
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }

        $out = curl_exec($curl);
        $code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($code < 200 || $code > 204){
            $response = [
                "status" => false,
                "out" => $out
            ];
        } else{
            $response = [
                "status" => true,
                "out" => $out
            ];
        }

        return $response;
    }
}
