<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\MessageTelegram;
use App\Models\BuildingObject;
use App\Models\PhotoObject;
use App\Models\Question;
use App\Models\SquareBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Review;
use App\Models\Work;
use App\Models\Map;

use CURLFile;
use DateTime;

class Plaster extends Controller
{
    private $request;

     const TELEGRAM_TOKEN = '5003667921:AAGH6ipNSKVsA02dMbAh4XPuOwiC9hUwWQc';
     const TELEGRAM_CHATID = '-1001536908023';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function onlyMap()
    {
        $data['params'] = [
            'DB' => self::getDataPlaster(),
            'onlyMap' => true
        ];

        return view('plaster', $data);
    }

    public function showPlaster()
    {
        $date = new DateTime();

        $data['params'] = [
            'DB' => self::getDataPlaster(),
            'onlyMap' => false,
            'countObject' => number_format(round($date->getTimestamp()/86400) - 14779 + 1, 0, '', ' '),
        ];

        return view('plaster', $data);
    }

    public function calc()
    {
        $validated = $this->request->validate([
            'номер_телефона' => 'required',
            'площадь_по_полу' => 'required|numeric',
        ]);

        if ( ($this->request->input('materialFalse') && $this->request->input('materialTrue')) || (!$this->request->input('materialFalse') && !$this->request->input('materialTrue'))) return response([
            "message" => "Выберете с материалом или без",
        ], 422);

        ($this->request->input('materialFalse')) ? $cof = 2.92 * 247 : $cof = 2.92 * 546;
        ($this->request->input('materialFalse')) ? $title = "без материала" : $title = "с материалом";

        //$response = __('plaster/third_block.cost').' <b>';
        //$response .= (int) $this->request->input('площадь_по_полу') * $cof;
        //$response .= '</b>'.__('plaster/third_block.currency');

        $response = (int) $this->request->input('площадь_по_полу') * $cof;

        $params["sendDocument"] = false;
        $params['message'] = "<i>Расчёт стоимости {$title}</i> \r\n";
        $params['message'] .= "Телефон: <b>".$this->request->input('номер_телефона')."</b> \r\n";
        $params['message'] .= "Площадь: <b>".$this->request->input('площадь_по_полу')." кв.м.</b> \r\n";
        $params['message'] .= $response;

        MessageTelegram::dispatch($params);
        // $this->messageTelegram($params);

        return response()->json($response);
    }

    public function callBack()
    {
        $validated = $this->request->validate([
            'номер_телефона' => 'required',
        ]);

        $parts = parse_url($_SERVER['HTTP_REFERER']);
        if (!empty($parts['query'])) parse_str($parts['query'], $query);

        $response = [
            "status" => "success",
            "phone" => $this->request->input('номер_телефона'),
            "utmSource" => $query["utm_source"] ?? "",
            "utmContent" => $query["utm_content"] ?? "",
            "utmMedium" => $query["utm_medium"] ?? "",
            "utmCampaign" => $query["utm_campaign"] ?? "",
            "utmTerm" => $query["utm_term"] ?? "",
            "roistatVisit" => $_COOKIE['roistat_visit'] ?? "",
            "_token" => $this->request->input("_token")
        ];

        $params["sendDocument"] = false;
        $params['message'] = "<i>Заказ звонка</i> \r\n";
        $params['message'] .= "Телефон: <b>".$this->request->input('номер_телефона')."</b> \r\n";

        MessageTelegram::dispatch($params);
        //$this->messageTelegram($params);

        return response()->json($response);
    }

    public function getSrcYoutube(){
        $response = Review::select('src')->where('type', 'video')->get();

        return response($response, 201);
    }

    public function getBuildingObject()
    {
        $buildingObject = BuildingObject::find($this->request->input('buildingObjectId'));

        $bo = new BuildingObject();

        $response = [
            "right" => $buildingObject,
            "center" => $bo->getphotoObjects([['po.building_object_id', '=', $this->request->input('buildingObjectId')]]),
        ];

        return response($response, 201);
    }

    public function getObjectTypeSquare(){
        return response(self::getOurWorks($this->request->input()), 201);
    }

    public function sendDocumentTelegram()
    {
        $this->request->validate([
            'phone' => 'required|max:16',
            'file' => 'mimes:pdf,docx,xls,xlsx,jpg,jpeg,png,bmp,gif,svg,webp|file'
        ], [

        ], [
            'phone' => 'номер телефона',
            'file' => 'файл'
        ]);

        $params = [
            "sendDocument" => true,
            "message" => "Гарантия лучшей цены \r\nТелефон: ".$this->request->input('phone')." \r\n",
            "name" => $_FILES['file']['tmp_name'],
            "mime" => $_FILES['file']['type'],
            "postname" => $_FILES['file']['name'],
        ];

        // MessageTelegram::dispatch($params);
        $this->messageTelegram($params);

        $parts = parse_url($_SERVER['HTTP_REFERER']);
        if (!empty($parts['query'])) parse_str($parts['query'], $query);

        $response = [
            "status" => "success",
            "phone" => $this->request->input('phone'),
            "utmSource" => $query["utm_source"] ?? "",
            "utmContent" => $query["utm_content"] ?? "",
            "utmMedium" => $query["utm_medium"] ?? "",
            "utmCampaign" => $query["utm_campaign"] ?? "",
            "utmTerm" => $query["utm_term"] ?? "",
            "roistatVisit" => $_COOKIE['roistat_visit'] ?? "",
            "_token" => $this->request->input("_token")
        ];

        return response()->json($response);
    }

    public function getPrivacy()
    {
        return response()->download(public_path("data/privacy/privacy.docx"), 'privacy.docx');
    }

    private function messageTelegram(array $params = array())
    {
        if ($params['sendDocument']){
            $this->SendDocument($params);
        } else {
            fopen("https://api.telegram.org/bot".self::TELEGRAM_TOKEN."/sendMessage?chat_id=".self::TELEGRAM_CHATID."&parse_mode=html&text=".urlencode($params['message']),"r");
        }
     }

    private function SendDocument($params)
    {
        $url = "https://api.telegram.org/bot".self::TELEGRAM_TOKEN."/sendDocument";

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                "chat_id" => self::TELEGRAM_CHATID,
                "document" => new CURLFile($params['name'], $params['mime'], $params['postname']),
                "caption" => $params['message'],
            ]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $out = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    private static function getOurWorks(array $params = array())
    {
        $po = new PhotoObject();

        $response = array(
            'squareBuildings' => SquareBuilding::all(),
            'left' => array(),
            'center' => array(),
            'right' => array(),
        );

        if (!empty($params['left'])) $response['left'] = $po->getPhotoLeft($params['left']);
        if (!empty($params['center'])) $response['center'] = $po->getPhotoLeft($params['center']);
        if ($params['right']) $response['right'] = BuildingObject::find($response['center'][0]['id']);

        return $response;
    }

    private static function getDataPlaster()
    {
        $params = array(
            'left' => [
                ['photo', '=', '1'],
            ],
            'center' => [
                ['po.created_at', '=', NULL],
            ],
            'right' => true,
        );

        return [
            'maps' => Map::all(),
            'reviews' => Review::all(),
            'works' => Work::all(),
            'ourWorks' => self::getOurWorks($params),
            'questions' => Question::all(),
        ];
    }
}
