<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Measuring;
use App\Models\BuildingObject;
use App\Models\MeasuringMaterial;
use App\Models\MeasuringWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeasuringController extends Controller
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(){
//        if($this->request->user()->role == 'user') return response('Нет прав доступа', 419);

        $response = [
            'floors' => Floor::all(),
            'material' => MeasuringMaterial::all(),
            'works' => MeasuringWork::orderBy('name')->get()
        ];

        return response($response, 201);
    }

    public function saveEstimate()
    {
        $params = [
            "required" => null,
        ];
        $this->validatedEstimate($params);

        $buildingObject = BuildingObject::where([
            ["phone", $this->request->input("customerData")['phone']],
            ["address", $this->request->input("customerData")['address']]
        ])->get();

        if (empty($buildingObject[0])){
            $measuring = new Measuring;
            $measuring->data_json = json_encode($this->request->input());
            $measuring->active = 0;
            $measuring->save();

            $buildingObject = new BuildingObject;
            $buildingObject->measuring_id = $measuring['id'];
        } else {
            $measuring = Measuring::find($buildingObject[0]->measuring_id);
            $measuring->data_json = json_encode($this->request->input());
            $measuring->active = 0;
            $measuring->save();

            $buildingObject = BuildingObject::find($buildingObject[0]->id);
        }

        $buildingObject->name = $this->request->input("customerData")['name'];
        $buildingObject->post = $this->request->input("customerData")['post'];
        $buildingObject->address = $this->request->input("customerData")['address'];
        $buildingObject->measurer = $this->request->input("customerData")['measurer'];
        $buildingObject->phone = $this->request->input("customerData")['phone'];
        $buildingObject->type_space_id = $this->request->input("customerData")['typeSpace'];

        $buildingObject->save();

        return response("Замер сохранён!", 201);
    }

    public function calculateEstimate()
    {
        $params = [
            "required" => "required|",
        ];

        if( $this->validatedEstimate($params) === false) return response("Выбирете хотя бы один этаж", 422);

        return response($this->request, 201);
    }

    public function searchEstimate()
    {
        $rules = [
            'data.phone' => 'max:16',
            'data.startDate' => 'date',
            'data.endDate' => 'date',
            'data.checkBox' => 'boolean'
        ];
        $message = [];
        $names = [
            'data.phone' => 'Номер телефона',
            'data.startDate' => 'Дата начала периода',
            'data.endDate' => 'Дата конца периода',
            'data.checkBox' => 'Необработанные замеры'
        ];

        $validated = $this->request->validate($rules, $message, $names);

        if(
            empty($this->request->input('phone')) &&
            empty($this->request->input('startDate')) &&
            empty($this->request->input('endDate')) &&
            empty($this->request->input('checkBox'))
        ) return response("Все поля не могут быть пустыми", 422);

        $where = [];

        if (!empty($this->request->input('phone'))) $where[] = ['phone', 'like', '%'.$this->request->input('phone').'%'];
//        if (!empty($this->request->input('checkBox'))) $where[] = ['active', '=', $this->request->input('checkBox')];

        $buildingObjects = BuildingObject::where($where);
        if (!empty($this->request->input('startDate'))) $buildingObjects = $buildingObjects->whereDate('created_at', '>=', $this->request->input('startDate'));
        if (!empty($this->request->input('endDate'))) $buildingObjects = $buildingObjects->whereDate('created_at', '<=', $this->request->input('endDate'));
        $buildingObjects = $buildingObjects->get();
        foreach ($buildingObjects as $keybuildingObject => $buildingObject){
            $buildingObject->measuring;
            if (!empty($this->request->input('checkBox'))){
                if ($buildingObject->measuring->active == !$this->request->input('checkBox')){
                    $response[] = ["buildingObject" => $buildingObject];
                }
            } else{
                $response[] = ["buildingObject" => $buildingObject];
            }
        }

        /*$buildingObjects = BuildingObject::where('phone', 'like', '%'.$this->request->input('phone').'%')->get();
        foreach ($buildingObjects as $keybuildingObject => $buildingObject){
            $buildingObject->measuring;
            $response[] = ["buildingObject" => $buildingObject];
        }*/

        if (empty($response)) return response("Замер не найден!", 422);

        return response($response, 201);
    }

    private function validatedEstimate($params = array())
    {
        $rules = [];
        $message = [];
        $names = [];

        self::validateCustomer($rules, $names);

        //проверка этажей
        if(!empty($this->request->input('showedFloor')) || empty($params['required'])){
            foreach ($this->request->input('showedFloor') as $keyFloor => $floor){
                foreach ($floor['rooms'] as $keyRoom => $room){
                    // Проверка помещений
                    $rules["showedFloor.".$keyFloor.".rooms.".$keyRoom.".name"] = $params['required']."max:100";
                    $rules["showedFloor.".$keyFloor.".rooms.".$keyRoom.".SQM"] = $params['required']."max:100000|numeric";
                    $rules["showedFloor.".$keyFloor.".rooms.".$keyRoom.".MP"] = $params['required']."max:100000|numeric";
                    $rules["showedFloor.".$keyFloor.".rooms.".$keyRoom.".corners"] = $params['required']."max:100000|numeric";
                    $rules["showedFloor.".$keyFloor.".rooms.".$keyRoom.".profile"] = $params['required']."max:100000|numeric";
                    $rules["showedFloor.".$keyFloor.".rooms.".$keyRoom.".typeMixture"] = $params['required']."max:1|numeric";

                    $names["showedFloor.".$keyFloor.".rooms.".$keyRoom.".name"] = "Этаж ". $keyFloor .", ". $room['name']." \"Название помещения\"";
                    $names["showedFloor.".$keyFloor.".rooms.".$keyRoom.".SQM"] = "Этаж ". $keyFloor .", ". $room['name']." \"кв.м.\"";
                    $names["showedFloor.".$keyFloor.".rooms.".$keyRoom.".MP"] = "Этаж ". $keyFloor .", ". $room['name']." \"м.п.\"";
                    $names["showedFloor.".$keyFloor.".rooms.".$keyRoom.".corners"] = "Этаж ". $keyFloor .", ". $room['name']." \"Уголки\"";
                    $names["showedFloor.".$keyFloor.".rooms.".$keyRoom.".profile"] = "Этаж ". $keyFloor .", ". $room['name']." \"Профиля\"";
                    $names["showedFloor.".$keyFloor.".rooms.".$keyRoom.".typeMixture"] = "Этаж ". $keyFloor .", ". $room['name']." \"Тип смеси\"";

                    // Проверка доп работ в помещениях
                    foreach ($room['addWorks'] as $keyAddwork => $addWork){
                        $params = [
                            "rules" => "showedFloor.".$keyFloor.".rooms.".$keyRoom.".addWorks.".$keyAddwork,
                            "names" => "Этаж ". $keyFloor .", ". $room['name']." , доп. работа: ". $addWork['name'],
                            "data" => $addWork,
                            "required" => $params['required'],
                        ];
                        self::validateAddWork($rules, $names, $params);
                    }
                }

                foreach ($floor['addWorks'] as $keyAddwork => $addWork){
                    $params = [
                        "rules" => "showedFloor.".$keyFloor.".addWorks.".$keyAddwork,
                        "names" => "Этаж ". $keyFloor ." , доп. работа: ". $addWork['name'],
                        "data" => $addWork,
                        "required" => $params['required'],
                    ];
                    self::validateAddWork($rules, $names, $params);
                }
            }

            foreach ($this->request->input('toWorkObject') as $keyAddwork => $addWork){
                $params = [
                    "rules" => "toWorkObject.".$keyAddwork,
                    "names" => "доп. работа на объекте: ". $addWork['name'],
                    "data" => $addWork,
                    "required" => $params['required'],
                ];
                self::validateAddWork($rules, $names, $params);
            }
        } else{
            return false;
        }

        self:self::validateDataObject($rules, $names);

        $validated = $this->request->validate($rules, $message, $names);
    }

    private static function validateAddWork(&$rules, &$names, $params)
    {
        $rules[$params['rules'].".name"] = $params['required']."max:100";
        $names[$params['rules'].".name"] = $params['names'];

        if (!empty($params['data']['param_one'])){
            $rules[$params['rules'].".paramOne"] = $params['required']."max:100000|numeric";
            $names[$params['rules'].".paramOne"] = $params['names']. " , параметр 1";
        }

        if (!empty($params['data']['param_two'])){
            $rules[$params['rules'].".paramTwo"] = $params['required']."max:100000|numeric";
            $names[$params['rules'].".paramTwo"] = $params['names']. " , параметр 2";
        }

        if (!empty($params['data']['param_three'])){
            $rules[$params['rules'].".paramThree"] = $params['required']."max:100000|numeric";
            $names[$params['rules'].".paramThree"] = $params['names']. " , параметр 3";
        }

        if (!empty($params['data']['param_four'])){
            $rules[$params['rules'].".paramFour"] = $params['required']."max:100000|numeric";
            $names[$params['rules'].".paramFour"] = $params['names']. " , параметр 4";
        }
    }

    private static function validateCustomer(&$rules, &$names)
    {
        $rules = [
            'customerData.name' => 'required|max:100',
            'customerData.post' => 'required|max:100',
            'customerData.address' => 'required|max:1000',
            'customerData.measurer' => 'required|max:100',
            'customerData.phone' => 'required|max:16',
            'customerData.typeSpace' => 'required|max:2|numeric',
        ];
        $names = [
            'customerData.name' => 'ФИО клиента',
            'customerData.post' => 'Должность',
            'customerData.address' => 'Адрес',
            'customerData.measurer' => 'Замерщик',
            'customerData.phone' => 'Номер телефона',
            'customerData.typeSpace' => 'Тип жилого помещения',
        ];
    }

    private static function validateDataObject(&$rules, &$names)
    {
        $rules['dataObject.threeHundredEighty'] = 'max:2|numeric';
        $rules['dataObject.layer'] = 'max:2|numeric';
        $rules['dataObject.media'] = 'max:2|numeric';
        $rules['dataObject.water'] = 'max:2|numeric';
        $rules['dataObject.washing'] = 'max:2|numeric';
        $rules['dataObject.electrics'] = 'max:2|numeric';
        $rules['dataObject.skidding'] = 'max:2|numeric';
        $rules['dataObject.segmentation'] = 'max:3|numeric';
        $rules['dataObject.carryingMore'] = 'boolean';
        $rules['dataObject.segmentationMultiChoice.fullGeometry'] = 'boolean';
        $rules['dataObject.segmentationMultiChoice.subcontracting'] = 'boolean';
        $rules['dataObject.segmentationMultiChoice.oldFoundation'] = 'boolean';
        $rules['dataObject.km'] = 'max:100000|numeric';
        $rules['dataObject.cable'] = 'max:100000|numeric';
        $rules['dataObject.tube'] = 'max:100000|numeric';
        $rules['dataObject.concreteContact.km'] = 'max:100000|numeric';
        $rules['dataObject.concreteContact.pm'] = 'max:100000|numeric';
        $rules['dataObject.concreteContact.percent'] = 'max:100000|numeric';
        $rules['dataObject.gasBlock.km'] = 'max:100000|numeric';
        $rules['dataObject.gasBlock.pm'] = 'max:100000|numeric';
        $rules['dataObject.gasBlock.percent'] = 'max:100000|numeric';

        $names['dataObject.threeHundredEighty'] = '380';
        $names['dataObject.layer'] = 'слоя';
        $names['dataObject.media'] = 'медиа';
        $names['dataObject.water'] = 'вода';
        $names['dataObject.washing'] = 'замывка';
        $names['dataObject.electrics'] = 'электрика';
        $names['dataObject.skidding'] = 'занос материала';
        $names['dataObject.segmentation'] = 'Сегментация';
        $names['dataObject.carryingMore'] = 'Пронос > 20м.';
        $names['dataObject.segmentationMultiChoice.fullGeometry'] = 'Полная геометрия';
        $names['dataObject.segmentationMultiChoice.subcontracting'] = 'Субподряд';
        $names['dataObject.segmentationMultiChoice.oldFoundation'] = 'Старый фонд';
        $names['dataObject.km'] = 'расстояние до объекта';
        $names['dataObject.cable'] = 'количество кабеля';
        $names['dataObject.tube'] = 'количество шланга';
        $names['dataObject.concreteContact.km'] = 'количество бетоноконтакта м.кв.';
        $names['dataObject.concreteContact.pm'] = 'количество бетоноконтакта м.п.';
        $names['dataObject.concreteContact.percent'] = 'количество бетоноконтакта проценты';
        $names['dataObject.gasBlock.km'] = 'количество газоблока м.кв.';
        $names['dataObject.gasBlock.pm'] = 'количество газоблока м.п.';
        $names['dataObject.gasBlock.percent'] = 'количество газоблока проценты';
    }
}
