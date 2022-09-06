<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class CRM_model extends CI_Model{

  public function addAppeal($data)
  {
	 //date_default_timezone_set('Europe/Kiev');
	 $headers = array(
		'Content-Type: application/json',
		'Accept: application/json'
	 );

	 $data1 = array(
		'EntityName' => 'incident',
		'OptionSets' => array(
		  array(
			 'AttributeName' => 'new_requestchannel',
			 'Value' => $data['modal_add_appeals_form_channel']
		  ),
		),
		'Lookups' => array(
		  array(
			 'AttributeName' => 'customerid',
			 'EntityName' => 'contact',
			 'EntityId' => $data['modal_add_appeals_form_contact_hide']
		  ),
		  array(
			 'AttributeName' => 'new_requesttype',
			 'EntityName' => 'new_theme1',
			 'EntityId' => 	$data['modal_add_appeals_form_new_requesttype']
		  ),
		  array(
			 'AttributeName' => 'new_restaurant',
			 'EntityName' => 'account',
			 'EntityId' => $data['modal_add_appeals_form_new_restaurant']
		  ),
		),
		'CrmDateTimes' => array(
		  array(
			 'AttributeName' => 'new_incidentdate',
			 'Value' => strtotime($data['modal_add_appeals_form_date'])
		  )
		),
		'OtherAttributes' => array(
		  array(
			 'AttributeName' => 'new_order',
			 'Value' => $data['modal_add_appeals_form_chek']?true:false
		  ),
		  array(
			 'AttributeName' => 'description',
			 'Value' => $data['answer_appeals_form_description']
		  )
		)
	 );

	 if($data['modal_add_appeals_form_number']!=NULL)
		{
			$data1['OtherAttributes'][]  =
			array(
				 'AttributeName' => 'new_ordernumber',
				 'Value' => $data['modal_add_appeals_form_number']
			);
		}
	 if($data['modal_add_appeals_form_new_requestpart']!=NULL)
	 {
		$data1['Lookups'][]  =
		array(
		  'AttributeName' => 'new_requestpart',
		  'EntityName' => 'new_theme2',
		  'EntityId' => $data['modal_add_appeals_form_new_requestpart']
		);
	 }

	 if($data['modal_add_appeals_form_new_requesttheme']!=NULL)
	 {
		$data1['Lookups'][]  =
		array(
			 'AttributeName' => 'new_requesttheme',
			 'EntityName' => 'new_theme3',
			 'EntityId' => $data['modal_add_appeals_form_new_requesttheme']
		);
	 }
	 if($data['modal_add_appeals_form_new_food']!=NULL)
	 {
		$data1['Lookups'][]  =
		  array(
			 'AttributeName' => 'new_food',
			 'EntityName' => 'new_food',
			 'EntityId' => $data['modal_add_appeals_form_new_food']
		);
	 }
	 if($data['modal_add_appeals_form_inclusion']!=NULL)
	 {
		$data1['OptionSets'][]  =
		array(
		  'AttributeName' => 'new_inclusiontype',
		  'Value' => $data['modal_add_appeals_form_inclusion']
		);
	 }
	 if($data['modal_add_appeals_form_new_employee']!=NULL)
	 {
		$data1['OptionSets'][]  =
		array(
		  'AttributeName' => 'new_employee',
		  'Value' => $data['modal_add_appeals_form_new_employee']
		);
	 }
	 if($data['answer_appeals_form_new_object']!=NULL)
	 {
		$data1['OptionSets'][]  =
		array(
		  'AttributeName' => 'new_object',
		  'Value' => $data['answer_appeals_form_new_object']
		);
	 }

	 $curl = curl_init();
	 $url = 'http://192.168.31.10:4142/mcd/crm/create';
	 //$url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/create';
	 $data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
	 //print_r($data_json);
	 curl_setopt($curl, CURLOPT_URL, $url);
	 curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	 curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
	 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	 $resp = curl_exec($curl);
	 $http_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
	 curl_close($curl);

	 sleep(1);
	 $resp = json_decode($resp);
	 //print_r($data);
	 if(!empty($data['file']) && $http_code==201)
	 {
		if($this->sendFile($resp->Id,$data['file']))
		{
		  return 1;
		}
		else
		{
		  return 0;
		}
	 }
	 if($http_code==201)
	 {
		return 1;
	 }
	 else
	 {
		return 0;
	 }
  }

  public function sendFile($id,$file)
  {
	 $headers = array(
		'Content-Type: application/json',
		'Accept: application/json'
	 );
	 $data1 = array
	 (
	 "EntityObjectName" => "incident",
	 "EntityObjectId" => $id,
	 "FileName" => $file['filename'],
	 "EncodedData" => $file['file'],
	 "MimeType" => $file['type']
	 );

	 $curl = curl_init();
	 $url = 'http://192.168.31.10:4142/mcd/crm/createannotation';
	 //$url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/createannotation';
	 $data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
	 //print_r($data_json);
	 curl_setopt($curl, CURLOPT_URL, $url);
	 curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	 curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
	 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	 $resp = curl_exec($curl);
	 $http_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
	 curl_close($curl);
	 if($http_code==200)
	 {
		return 1;
	 }
	 else
	 {
		return 0;
	 }
  }


  public function addClient($data)
  {
	 //date_default_timezone_set('Europe/Kiev');
	 $headers = array(
		'Content-Type: application/json',
		'Accept: application/json'
	 );

	 $regionId = $this->db->query("SELECT new_region FROM new_city WHERE new_cityId = '".$data['modal_сustomers_form_city_hide']."'")->result_array()[0]['new_region'];
	 $districtId = $this->db->query("SELECT new_district FROM new_city WHERE new_cityId = '".$data['modal_сustomers_form_city_hide']."'")->result_array()[0]['new_district'];

	 $data1 = array(
		'EntityName' => 'contact',
		'OptionSets' => array(
		  array(
			 'AttributeName' => 'gendercode',
			 'Value' => $data['modal_сustomers_form_email_gender']
		  )
		),
		'Lookups' => array(
		  array(
			 'AttributeName' => 'new_region',
			 'EntityName' => 'new_region',
			 'EntityId' =>$regionId
		  ),
		  array(
			 'AttributeName' => 'new_district',
			 'EntityName' => 'new_district',
			 'EntityId' =>$districtId
		  ),
		  array(
			 'AttributeName' => 'new_city',
			 'EntityName' => 'new_city',
			 'EntityId' => $data['modal_сustomers_form_city_hide']
		  )
		),
		'OtherAttributes' => array(
		  array(
			 'AttributeName' => 'lastname',
			 'Value' => $data['modal_сustomers_form_surname']
		  ),
		  array(
			 'AttributeName' => 'firstname',
			 'Value' => $data['modal_сustomers_form_name']
		  ),
		  array(
			 'AttributeName' => 'middlename',
			 'Value' => $data['modal_сustomers_form_father_name']
		  ),
		  array(
			 'AttributeName' => 'mobilephone',
			 'Value' => $data['modal_сustomers_form_phone']
		  ),
		  array(
			 'AttributeName' => 'emailaddress1',
			 'Value' => $data['modal_сustomers_form_email']
		  )
		)
	 );

	 $curl = curl_init();
	 $url = 'http://192.168.31.10:4142/mcd/crm/create';
	 //$url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/create';
	 $data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
	 //print_r($data_json);
	 curl_setopt($curl, CURLOPT_URL, $url);
	 curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	 curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
	 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	 $resp = curl_exec($curl);
	 //print_r(curl_error($curl));
	 $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	 curl_close($curl);
	 //print_r($resp);
	 if($http_code==201)
	 {
		return 1;
	 }
	 else
	 {
		return 0;
	 }
	 }

  public function addAnswer($data)
  {
	 //date_default_timezone_set('Europe/Kiev');
	 $headers = array(
		'Content-Type: application/json',
		'Accept: application/json'
	 );

	 $data1 = array(
		'EntityName' => 'new_feedback',
    "OptionSets" => array(
      array(
        "AttributeName" => "new_result",
        "Value" => $data['new_result']
      )
    ),
		'Lookups' => array(
			array(
				'AttributeName' => 'new_incident',
				'EntityName' => 'incident',
				'EntityId' => $data["idAppeal"]
			),
			array(
				'AttributeName' => 'ownerid',
				'EntityName' => 'systemuser',
				'EntityId' => $data["id"]
			)
		),
		'OtherAttributes' => array(
			array(
				'AttributeName' => 'new_feedbacktext',
				'Value' =>  $data["text"]
			)
		)
	 );

	 if($data['correcttext']!="")
	 {
		$data1['OtherAttributes'][] = array(
		  'AttributeName' => 'new_feedbacktextcorrect',
		  'Value' => $data['correcttext']
		);
	 }
	 $curl = curl_init();
	 $url = 'http://192.168.31.10:4142/mcd/crm/create';
	 //$url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/create';
	 $data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
	 //print_r($data_json);
	 curl_setopt($curl, CURLOPT_URL, $url);
	 curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	 curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
	 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	 $resp = curl_exec($curl);
	 //print_r(curl_error($curl));
	 $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	 curl_close($curl);
	 //print_r($resp);
	 if($http_code==201)
	 {
		return 1;
	 }
	 else
	 {
		return 0;
	 }
  }


  function changeAnswer($data)
  {
	 $headers = array(
		'Content-Type: application/json',
		'Accept: application/json'
	 );

	 $data1 = array(
	 "EntityName" => "new_feedback",
	 "Id" => $data['id_app'],
	 "OtherAttributes" => array(
		array(
		  "AttributeName" => "new_feedbacktext",
		  "Value" => $data['answer_appeals_form_feedbacktext']
		),
		array(
		  "AttributeName" => "new_feedbacktextcorrect",
		  "Value" => $data['answer_appeals_form_feedbacktextcorrect']
		  )
		)
	 );

	 $curl = curl_init();
	 $url = 'http://192.168.31.10:4142/mcd/crm/update';
	 //$url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/create';
	 $data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
	 curl_setopt($curl, CURLOPT_URL, $url);
	 curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
	 curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
	 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	 $resp = curl_exec($curl);
	 $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	 curl_close($curl);

	 if($http_code==200)
	 {
		return 1;
	 }
	 else
	 {
		return 0;
	 }
  }



  function changeAppeal($id,$data)
  {
    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json'
    );

    $data1 = array(
    "EntityName" => "incident",
    "Id" => $id,
    "OtherAttributes" => array(
      array(
      "AttributeName" => "new_satisfied",
      "Value" => $data['modal_input_feedback']?true:false
      )
     )
    );

    if($data['modal_input_help_PR']!=NULL)
    {
      $data1['OptionSets'][]  =
      array(
        'AttributeName' => 'new_prorjurisconsult',
        'Value' => $data['modal_input_help_PR']
      );
    }

    if($data['modal_input_comments']!=NULL)
    {
      $data1['OtherAttributes'][]  =
      array(
        'AttributeName' => 'new_clientcomment',
        'Value' => $data['modal_input_comments']
      );
    }

    if($data['modal_input_notes']!=NULL)
    {
      $data1['OtherAttributes'][]  =
      array(
        'AttributeName' => 'new_notes',
        'Value' => $data['modal_input_notes']
      );
    }
    $curl = curl_init();
    $url = 'http://192.168.31.10:4142/mcd/crm/update';
    //$url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/update';
    $data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($curl);
    $http_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
    curl_close($curl);


    if(!empty($data['file']) && $http_code==200)
 	  {
   		if($this->sendFile($id,$data['file']))
   		{
   		  return 1;
   		}
   		else
   		{
   		  return 0;
   		}
 	 }

    if($http_code==200)
    {
      return 1;
    }
    else
    {
      return 0;
    }
  }



public function createPayment($data)
{

  $query= $this->db->query("SELECT new_name FROM new_compensation WHERE new_compensation = '".$data['modal_create_payment_form_id_appeal']."'")->result_array();
  if (!empty($query))
  {
    return -1;            //если уже есть компенсация для этого обращения
  }
  $headers = array(
      'Content-Type: application/json',
      'Accept: application/json'
  );
  //   print_r($data);
  //   данные для создания компенсации
  $data1 = array(
    "EntityName" => "new_compensation",
    "OptionSets" => array(
      array(
        "AttributeName" => "new_status",
        "Value" => $data['modal_create_payment_form_status']
      )
    ),
    "Lookups" => array(
      array(
        "AttributeName" => "new_createdby",
        "EntityName" => "systemuser",
        "EntityId" => $data['id']
      ),
      array(
        "AttributeName" => "new_compensation",
        "EntityName" => "incident",
        "EntityId" => $data['modal_create_payment_form_id_appeal']
      ),
      array(
        "AttributeName" => "new_1food",
        "EntityName" => "new_food",
        "EntityId" => $data['modal_create_payment_form_food_value_1']
      )
    ),
    "CrmDateTimes" => array(
      array(
      "AttributeName" => "new_enddate",
      "Value" => strtotime($data['modal_create_payment_form_enddate']), //дата в UNIX формате
      )
    ),
    "OtherAttributes" => array(
      array(
        "AttributeName" => "new_name",
        "Value" => $data['modal_create_payment_form_name_value']
      )
    )
  );


  for ($i=2;$i<=20;$i++)
  {
    $str ='modal_create_payment_form_food_value_'.$i.'';
    if(empty($data[$str]))
      {
        break;
      }
      else
      {
        $data1['Lookups'][] = array(
          "AttributeName" => 'new_'.$i.'food',
          "EntityName" => 'new_food',
          "EntityId" => $data[$str]);
      }
    }

    $curl = curl_init();
    $url = 'http://192.168.31.10:4142/mcd/crm/create';
    //$url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/create';
    $data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($curl);
    $http_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
    curl_close($curl);
    if($http_code==201)
    {
      return 1;
    }
    else
    {
      return 0;
    }
}

  public function changeStatus($id,$status,$timeUnix = -1)
  {
    $headers = array(
        'Content-Type: application/json',
        'Accept: application/json'
    );
    $data1 = array(
    "EntityName" => "new_compensation",
    "Id" => $id['compensation'],
    "OptionSets" => array(
      array(
        "AttributeName" => "new_status",
        "Value" => $status
      )
    )                              //ID компенсации
    );


    if(!empty($id['rest']))
    {
      $data1["Lookups"] = array(
        array(
        'AttributeName' => 'new_usedby',
        'EntityName' => 'account',
        'EntityId' => $id['rest']
      )
    );
    }

    if($timeUnix != -1)
    {
    $data1["CrmDateTimes"] = array(
      array(
      "AttributeName" => "new_enddate",
      "Value" => $timeUnix                      //дата в UNIX формате
      )
    );
   }

    $curl = curl_init();
    $url = 'http://192.168.31.10:4142/mcd/crm/update';
    //$url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/update';
    $data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $resp = curl_exec($curl);
    $http_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
    curl_close($curl);
    if($http_code==200)
    {
      return 1;
    }
    else
    {
      return 0;
    }
  }

  
  function changeWorktime($ownerId, $data)
  {

	$query= $this->db->query("SELECT Account.AccountId FROM Account WHERE Account.OwnerId = '".$ownerId."'")->result_array();
	$accountId = $query[0]['AccountId'];

	if ( $data == 'get_for_support_service' ) return $accountId;

	$headers = array(
        'Content-Type: application/json',
        'Accept: application/json'
    );

    $data1 = array(
		'EntityName' => 'new_changeform',
		'OptionSets' => array(
			array(
				'AttributeName' => 'new_changetype',
				'Value' => '100000000' //тип изменения (время)
			),array(
				'AttributeName' => 'new_schedulechangetype',
				'Value' => $data['new_schedulechangetype'] //насколько изменяетя режим работы (постоянно, временно)
			)
		),'Lookups' => array(
			array(
				'AttributeName' => 'new_restaurant',
				'EntityName' => 'account',
				'EntityId' => $accountId //id ресторана(берется из пользователя, зашедшего в систему)!!!!!!!!!!!!!!!!!!!!!!!!!надо взять из текущего пользователя!!!!!!!!!!!!!!!!!!!!!!!!!
			),
		),
		'OtherAttributes' => array(
			array(
				'AttributeName' => 'new_employeedata',
				'Value' => $data['new_employeedata'] //как себя назвал бы чувак, который оставил заявку
			)
		  )
	);
	if (isset($data['new_resumptionwork'])){
		if($data['new_resumptionwork']!=NULL){
			// array_push($data1, 'CrmDateTimes');
			$data1['CrmDateTimes'][] =
			array(
				'AttributeName' => 'new_resumptionwork',
				'Value' => strtotime($data['new_resumptionwork']) //данные с выбора даты и времени
			);
		}
	}



	if (isset($data['new_schedulechangereason'])){
		if($data['new_schedulechangereason']!=NULL){
			$data1['OptionSets'][]  =
			array(
				'AttributeName' => 'new_schedulechangereason',
				'Value' => $data['new_schedulechangereason'] //причина изменения расписания
			);
		}
	}
	
	if (isset($data['new_new_instoreworktime'])){
	if($data['new_new_instoreworktime']!=NULL){
		$data1['OtherAttributes'][]  =
		array(
			'AttributeName' => 'new_new_instoreworktime',
			'Value' => $data['new_new_instoreworktime']
			//!!!может быть не задана!!!формат записи: "7:00 - 23:00"\/!\/!\/!\/!\/!\/!\/!!!!!!!!
		);
	}}

	if (isset($data['new_driveworktime'])){
	if($data['new_driveworktime']!=NULL){
		$data1['OtherAttributes'][]  =
		array(
			'AttributeName' => 'new_driveworktime',
			'Value' => $data['new_driveworktime']
			//!!!!!!!!!!!!!!!!!!!!!может быть не задана!!!!!!!!!!!!!!!!!!!!!!!!!!
		);
	}}

	if (isset($data['new_expressworktime'])){
	if($data['new_expressworktime']!=NULL){
		$data1['OtherAttributes'][]  =
		array(
			'AttributeName' => 'new_expressworktime',
			'Value' => $data['new_expressworktime']
			//!!!может быть не задана!!!формат записи: "7:00 - 23:00"\/!\/!\/!\/!\/!\/!\/!!!!!!!!
		);
	}}

	if (isset($data['new_glovoworktime'])){
	if($data['new_glovoworktime']!=NULL){
		$data1['OtherAttributes'][]  =
		array(
			'AttributeName' => 'new_glovoworktime',
			'Value' => $data['new_glovoworktime']
			//!!!может быть не задана!!!формат записи: "7:00 - 23:00"\/!\/!\/!\/!\/!\/!\/!!!!!!!!
		);
	}}

	if (isset($data['new_rocketworktime'])){
	if($data['new_rocketworktime']!=NULL){
		$data1['OtherAttributes'][]  =
		array(
			'AttributeName' => 'new_rocketworktime',
			'Value' => $data['new_rocketworktime']
			//!!!может быть не задана!!!формат записи: "7:00 - 23:00"\/!\/!\/!\/!\/!\/!\/!!!!!!!!
		);
	}}

	if (isset($data['new_comment'])){
	if($data['new_comment']!=NULL){
		$data1['OtherAttributes'][]  =
		array(
			'AttributeName' => 'new_comment',
			'Value' => $data['new_comment'] //!!!!!!!!!!!!!!!!!!!может быть не задана!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		);
	}}
	

	$curl = curl_init();
	$url = 'http://192.168.31.10:4142/mcd/crm/create';
	// $url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/create';
	$data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
	//print_r($data_json);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$resp = curl_exec($curl);
	//print_r(curl_error($curl));
	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	// print_r($data_json);
	// print_r($http_code);
	if($http_code==201)
	{
	   return 1;
	}
	else
	{
	   return 0;
	}
  }




  function changeDirector($ownerId, $data)
  {

	$query= $this->db->query("SELECT Account.AccountId FROM Account WHERE Account.OwnerId = '".$ownerId."'")->result_array();
	$accountId = $query[0]['AccountId'];

	$headers = array(
        'Content-Type: application/json',
        'Accept: application/json'
    );
	
	$data1 = array(
		'EntityName' => 'new_changeform',
		'OptionSets' => array(
			array(
				'AttributeName' => 'new_changetype',
				'Value' => '100000001' //тип изменения (директор)
			)
		),'Lookups' => array(
			array(
				'AttributeName' => 'new_restaurant',
				'EntityName' => 'account',
				'EntityId' => $accountId //id ресторана(берется из пользователя, зашедшего в систему)!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			),
		),'OtherAttributes' => array(
			array(
				'AttributeName' => 'new_directorfullname',
				'Value' => $data['new_directorfullname'] //ФИО нового директора
			),array(
				'AttributeName' => 'new_directoremail',
				'Value' => $data['new_directoremail'] //почта нового директора
			),array(
				'AttributeName' => 'new_directorephone',
				'Value' => $data['new_directorephone'] //телефон нового директора
			)
		)
	);

	$curl = curl_init();
	$url = 'http://192.168.31.10:4142/mcd/crm/create';
	// $url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/create';
	$data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
	//print_r($data_json);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	$resp = curl_exec($curl);
	//print_r(curl_error($curl));
	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	// print_r($http_code);
	if($http_code==201)
	{
	   return 1;
	}
	else
	{
	   return 0;
	}
	// print_r($data);
  }



  function changeRestrant($ownerId, $data)
  {
		$query= $this->db->query("SELECT Account.AccountId FROM Account WHERE Account.OwnerId = '".$ownerId."'")->result_array();
		$accountId = $query[0]['AccountId'];

		$headers = array(
      'Content-Type: application/json',
      'Accept: application/json'
    );
	
		$data1 = array(
			'EntityName' => 'new_changeform',
			'OptionSets' => array(
				array(
					'AttributeName' => 'new_changetype',
					'Value' => $data['new_changetype'] //тип изменения (директор)
				)
			),'Lookups' => array(
				array(
					'AttributeName' => 'new_restaurant',
					'EntityName' => 'account',
					'EntityId' => $accountId //id ресторана(берется из пользователя, зашедшего в систему)!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				)
			)
		);

		$curl = curl_init();
		$url = 'http://192.168.31.10:4142/mcd/crm/create';
		// $url = 'https://crmfacade.crm.servicedesk.in.ua/mcd/crm/create';
		$data_json = json_encode ($data1, JSON_UNESCAPED_UNICODE);
		//print_r($data_json);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($curl);
		//print_r(curl_error($curl));
		$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		// print_r($http_code);
		if($http_code==201){
	  	return 1;
	 	} else {
	  	return 0;
		}
		// print_r($data);
  }
}