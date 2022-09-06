<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Appeal_model extends CI_Model {
    
    const REQUESTSTATUS_PARAM =  [
      'new' => 100000000,
      'inWork' => 100000001,
      'expired' => 100000002,
      'rework' => 100000006,
      'closeOper' => array( 100000004 , 100000005 ),
      'helpPR' => 100000007,
      'helpLawyer' => 100000008,
      'all' => null
    ];

  const SEARCH_FIELDS = [
    'Incident.TicketNumber', 
    'Incident.CreatedOn', 
    'CustomerIdName', 
    'Incident.new_requesttypeName', 
    'Incident.new_requestpartName', 
    'Incident.new_requestthemeName', 
    'Incident.new_restaurantName', 
    'Incident.new_foodName',
    'Incident.new_summ',
    'Incident.new_ordernumber',
    'Incident.Description',
    'Incident.OwnerIdName'

  ];

  //общая для всех пользователей часть запроса

  //private $strQueryTable_count = "SELECT Incident.IncidentId, Incident.new_requeststatus";

  //IIF(LEN(Contact.EMailAddress1)>20, REPLACE(Contact.EMailAddress1,'@',' @'), Contact.EMailAddress1),
  const strQueryTable = "SELECT Incident.TicketNumber,Incident.IncidentId, Incident.new_requestchannel,Incident.CreatedOn,
                            CONCAT (Incident.CustomerIdName,' <a href=\"mailto:',Contact.EMailAddress1,'\">',
                              Contact.EMailAddress1,'</a>',' <a href=\"tel:', Contact.MobilePhone, '\">',Contact.MobilePhone,'</a>')
                            AS CustomerIdName,
                            Incident.new_requesttypeName,Incident.new_requestpartName,Incident.new_requestthemeName,Incident.new_restaurantName,Incident.new_foodName,Incident.new_incidentdate,Incident.new_summ,Incident.new_ordernumber,Incident.new_inclusiontype,Incident.new_employee,Incident.new_object,Incident.Description,Incident.OwnerIdName,Incident.new_requeststatus,Incident.new_worktime,Incident.new_prorjurisconsult,Incident.new_satisfied,Incident.new_clientcomment,Incident.new_intime,Incident.new_notes,
                            (SELECT COUNT(FileName) FROM Annotation WHERE Annotation.ObjectId = Incident.IncidentId) AS FileName,
                            new_feedback.CreatedOn AS CreateFeedback,new_feedback.new_feedbacktext,new_feedback.new_feedbacktextcorrect,
                            new_compensation.new_name, new_compensation.CreatedOn AS dateCompensation, new_compensation.new_enddate,
                            CONCAT(new_compensation.new_1foodName,',',new_compensation.new_2foodName,',',new_compensation.new_3foodName,',',new_compensation.new_4foodName,',',new_compensation.new_5foodName,',',new_compensation.new_6foodName,',',new_compensation.new_7foodName,',',new_compensation.new_8foodName,',',		 new_compensation.new_9foodName,',',new_compensation.new_10foodName,',',new_compensation.new_11foodName,',',new_compensation.new_12foodName,',',new_compensation.new_13foodName,',',new_compensation.new_14foodName,',',new_compensation.new_15foodName,',',new_compensation.new_16foodName,',',new_compensation.new_17foodName,',',new_compensation.new_18foodName,',',new_compensation.new_19foodName,',',new_compensation.new_20foodName) AS food,
                            new_compensation.new_createdbyName, new_compensation.new_usedbyName,
                            Incident.new_ResponseID,Incident.new_OSAT,Incident.new_additionally,Incident.new_VoucherCode,Incident.new_POS,Incident.new_ManualPOScategory, ci.count, Incident.CustomerId";      //добавлено 8 марта

  const strQueryTable_excel = "SELECT Incident.TicketNumber,Incident.IncidentId, Incident.new_requestchannel,Incident.CreatedOn,
                               Incident.CustomerIdName as CustomerName, 
                               Contact.EMailAddress1 as CustomerEmail, 
                               Contact.MobilePhone as CustomerPhone,
                            Incident.new_requesttypeName,Incident.new_requestpartName,Incident.new_requestthemeName,Incident.new_restaurantName,Incident.new_foodName,Incident.new_incidentdate,Incident.new_summ,Incident.new_ordernumber,Incident.new_inclusiontype,Incident.new_employee,Incident.new_object,Incident.Description,Incident.OwnerIdName,Incident.new_requeststatus,Incident.new_worktime,Incident.new_prorjurisconsult,Incident.new_satisfied,Incident.new_clientcomment,Incident.new_intime,Incident.new_notes,
                            (SELECT COUNT(FileName) FROM Annotation WHERE Annotation.ObjectId = Incident.IncidentId) AS FileName,
                            new_feedback.CreatedOn AS CreateFeedback,new_feedback.new_feedbacktext,new_feedback.new_feedbacktextcorrect,
                            new_compensation.new_name, new_compensation.CreatedOn AS dateCompensation, new_compensation.new_enddate,
                            CONCAT(new_compensation.new_1foodName,',',new_compensation.new_2foodName,',',new_compensation.new_3foodName,',',new_compensation.new_4foodName,',',new_compensation.new_5foodName,',',new_compensation.new_6foodName,',',new_compensation.new_7foodName,',',new_compensation.new_8foodName,',',    new_compensation.new_9foodName,',',new_compensation.new_10foodName,',',new_compensation.new_11foodName,',',new_compensation.new_12foodName,',',new_compensation.new_13foodName,',',new_compensation.new_14foodName,',',new_compensation.new_15foodName,',',new_compensation.new_16foodName,',',new_compensation.new_17foodName,',',new_compensation.new_18foodName,',',new_compensation.new_19foodName,',',new_compensation.new_20foodName) AS food,
                            new_compensation.new_createdbyName, new_compensation.new_usedbyName,
                            Incident.new_ResponseID,Incident.new_OSAT,Incident.new_additionally,Incident.new_VoucherCode,Incident.new_POS,Incident.new_ManualPOScategory, ci.count, Incident.CustomerId";      //добавлено 8 марта

  
  // *********************************Common
  // *******************************************************************

  private static function getClietCountAppeal($date_begin = null, $date_end = null) {

    $sql = "LEFT OUTER JOIN
      (SELECT COUNT(*) as count, CustomerId FROM Incident ".
      ( $date_begin == NULL ? "" : " WHERE CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'").
      " GROUP BY CustomerId) as ci 
      ON Incident.CustomerId = ci.CustomerId
    ";

    return $sql;

  } // getClietCountAppeal

  private function get_search_param( $addParam ) {
    if ( !isset( $addParam ) || !isset( $addParam['search'] ) || $addParam['search'] == ''  ) return "";
    $search_arr = explode('+', $addParam['search']);
    $sql = '';
    foreach($search_arr as $key => $search) {
      $search = trim($search);
      $sql .= "DECLARE @searchWord".$key." VARCHAR(".(mb_strlen($search)+2).") ".PHP_EOL.
              "SET @searchWord".$key." = '%".$search."%' ".PHP_EOL;
    }
    return $sql; 
  } // get_search_param

  private function get_search_condition( $addParam ) {
    if ( !isset( $addParam ) || !isset( $addParam['search'] ) || $addParam['search'] == ''  ) return "";
    $search_arr = explode('+', $addParam['search']);
    $cond = '';
    foreach($search_arr as $key => $value) {
      $cond .= ' AND (';
      foreach(self::SEARCH_FIELDS as $field) {
        // $cond .= 'CONTAINS('.$field.', @searchWord) OR ';
        $cond .= $field.' LIKE @searchWord'.$key.' OR ';
      } //foreach
      $cond = mb_substr($cond, 0, -4);
      $cond .= ')'.PHP_EOL;
    }
    return $cond;
  } // get_search_condition

  private function get_requeststatus_condition( $addParam ) {
    if ( !isset( $addParam ) || !isset( $addParam['requeststatus'] )  ) return "";
    
    $cond = self::REQUESTSTATUS_PARAM[ $addParam['requeststatus'] ];
    if ( !isset( $cond ) ) return '';

    if ( is_array( $cond ) ) {
      return " AND Incident.new_requeststatus IN ('".implode("','", $cond)."')".PHP_EOL;
    }  else {
      return ' AND Incident.new_requeststatus = '.$cond.PHP_EOL;
    }    
  } // get_requeststatus_condition

  private function strQueryTable( $addParam = [] ) {
    if ( isset( $addParam['forExcel'] ) && $addParam['forExcel'] === true ) {
      return self::strQueryTable_excel.PHP_EOL;
    } else {
      return self::strQueryTable.PHP_EOL;
    }
  } // strQueryTable

  // **********************************Office
  // *******************************************************************

  public function getCountsForOffice($login,$date_begin,$date_end, $addParam = []) {
    $arr = [];
    $addParam_requeststatus = isset($addParam['requeststatus']) ? $addParam['requeststatus'] : null;
    $cc=0;
    foreach(self::REQUESTSTATUS_PARAM as $key => $param) {
      if (isset($addParam_requeststatus) && $addParam_requeststatus != $key) continue;
      $addParam['requeststatus'] = $key;
      $sql = $this->getCountsForOffice_sql($login, $date_begin, $date_end, $addParam);
      $cc++;
$st = microtime(true);      
      $res = $this->db->query( $sql )->result_array();
$ft = microtime(true);
      $arr[ $key ] = count($res) == 0 ? 0 : $res[0]['count'];
    } // foreach
    //file_put_contents('log_time.txt',  'getCountsForOffice('.$cc.') ft-st='.($ft-$st). PHP_EOL, FILE_APPEND);
    return  $arr;
  } //getCountsForOffice

  private function getCountsForOffice_sql($login,$date_begin,$date_end, $addParam = [])
  {
    $sql=
      $this->get_search_param($addParam).
      " SELECT count(*) as count ".
      " FROM Incident ".
      self::getClietCountAppeal($date_begin,$date_end).PHP_EOL.
      " WHERE Incident.CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'".PHP_EOL.
      $this->get_requeststatus_condition( $addParam ).
      $this->get_search_condition( $addParam ). 
      (isset($addParam['customer_id'])  ? " AND Incident.CustomerId = '".$addParam['customer_id']."'"  : "").
      (isset($addParam['filter_bell'])  ? " AND ci.count > ".$addParam['bell_count']  : "");

      //file_put_contents('log.txt', json_encode($addParam) . PHP_EOL, FILE_APPEND);
      //file_put_contents('log.txt', $sql . PHP_EOL, FILE_APPEND);
      return $sql;
  } // getAppealsForOffice_sql

  private function getAppealsForOffice_sql($login,$date_begin,$date_end, $addParam = [])
  {
    $sql =
      $this->get_search_param($addParam).
      $this->strQueryTable($addParam).
      " FROM Incident ".
      " LEFT OUTER JOIN Contact ON Incident.CustomerId = Contact.ContactId
      LEFT OUTER JOIN new_feedback ON Incident.IncidentId = new_feedback.new_incident  AND new_feedback.new_feedbackId IN (SELECT new_feedbackId FROM (SELECT new_incident,max(CreatedOn) mydate,max(new_feedbackId) new_feedbackId FROM new_feedback GROUP BY new_incident) x)
      LEFT OUTER JOIN new_compensation ON Incident.IncidentId = new_compensation.new_compensation ".PHP_EOL.
      self::getClietCountAppeal($date_begin,$date_end).PHP_EOL.
      " WHERE Incident.CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'".PHP_EOL.
      $this->get_requeststatus_condition( $addParam ).
      $this->get_search_condition( $addParam ).
      (isset($addParam['customer_id'])  ? " AND Incident.CustomerId = '".$addParam['customer_id']."'"  : "").
      (isset($addParam['filter_bell'])  ? " AND ci.count > ".$addParam['bell_count']  : "").
      ( (isset($addParam['noOrderBy']) && $addParam['noOrderBy'] === true) ? "" : " ORDER BY Incident.CreatedOn DESC, Incident.IncidentId").
      (isset($addParam['offset']) ? " OFFSET ".$addParam['offset']." ROWS FETCH NEXT ".$addParam['length']." ROWS ONLY" : "");

      //file_put_contents('log.txt', json_encode($addParam) . PHP_EOL, FILE_APPEND);
      //file_put_contents('log.txt', $sql . PHP_EOL, FILE_APPEND);
      return $sql;
  } // getAppealsForOffice_sql

  public function getAppealsForOffice($login,$date_begin,$date_end, $addParam = [])
  {
    $st = microtime(true);
      $result = $this->db->query( 
          $this->getAppealsForOffice_sql($login,$date_begin,$date_end, $addParam)
      )->result();
    $ft = microtime(true);
    //file_put_contents('log_time.txt',  'getAppealsForOffice ft-st='.($ft-$st). PHP_EOL, FILE_APPEND);
    return $result;
  }

  // ********************Moderator
  // *******************************************************************
  public function getAppealsForModerator($id,$date_begin,$date_end)
  {
      //$query= $this->db->query("SELECT TicketNumber,IncidentId, new_requestchannel,CustomerIdName,new_requesttypeName,new_requestpartName,new_requestthemeName,new_restaurantName,new_foodName,new_incidentdate,new_order,new_ordernumber,new_inclusiontype,new_employee,new_object,Description,OwnerIdName, new_requeststatus,new_worktime,new_prorjurisconsult,new_satisfied,new_clientcomment,CreatedOn,new_intime,new_notes FROM Incident WHERE CreatedBy ='".$id."' AND Incident.CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'")->result();
      //return $query;
  }
  public function getCounts_forModerator($date_begin,$date_end) {
    // nothing to do for compatibilities with this shit code
  }


  // ***********************Consultant and OpManager
  // *******************************************************************
  public function getCountsForConsAndOpManager($login, $date_begin, $date_end, $addParam = []) {
    $sql = '';
    $arr = [];
    $addParam_requeststatus = isset($addParam['requeststatus']) ? $addParam['requeststatus'] : null;
    foreach(self::REQUESTSTATUS_PARAM as $key => $param) {
      if (isset($addParam_requeststatus) && $addParam_requeststatus != $key) continue;
      $addParam['requeststatus'] = $key;
      $res = $this->db->query( $this->getCountsForConsAndOpManager_sql($login, $date_begin, $date_end, $addParam) )->result_array();
      $arr[ $key ] = $res[0]['count'];
    } // foreach
    //$sql = mb_substr($sql, 0, -1);
    return  $arr;
  } // getCounts_forConsAndOpManager

  private function getCountsForConsAndOpManager_sql($login,$date_begin,$date_end, $addParam = []) {
    
    $owningBusinessUnit_list = $this->get_OwningBusinessUnit_list(null, $login);
    if (count($owningBusinessUnit_list) == 0) {
      $owningBusinessUnit_str = "";
    } else {
      $owningBusinessUnit_str = " OR Incident.OwningBusinessUnit IN ('".implode("','",$owningBusinessUnit_list)."')"; 
    }

    return 
      $this->get_search_param($addParam).
      "SELECT count(*) as count".
      " FROM Incident
      
      LEFT OUTER JOIN BusinessUnit ON Incident.OwningBusinessUnit = BusinessUnit.BusinessUnitId
      LEFT OUTER JOIN (SELECT TOP 1 * FROM SystemUser WHERE DomainName = '".$login."') as SystemUser ON SystemUser.BusinessUnitId = BusinessUnit.BusinessUnitId

      ".self::getClietCountAppeal($date_begin,$date_end)."
      WHERE (SystemUser.DomainName = '".$login."'".$owningBusinessUnit_str.")".
      " AND Incident.CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'".
      (isset($addParam['filter_bell']) ? " AND ci.count > ".$addParam['bell_count']  : "").
      $this->get_requeststatus_condition( $addParam ).
      $this->get_search_condition( $addParam );
  }

  private function getAppealsForConsAndOpManager_sql($login,$date_begin,$date_end, $addParam = []) {
    
    $owningBusinessUnit_list = $this->get_OwningBusinessUnit_list(null, $login);
    //$owningBusinessUnit_str = " IN ('".implode("','",$owningBusinessUnit_list)."')"; 
    if (count($owningBusinessUnit_list) == 0) {
      $owningBusinessUnit_str = "";
    } else {
      $owningBusinessUnit_str = " OR Incident.OwningBusinessUnit IN ('".implode("','",$owningBusinessUnit_list)."')"; 
    }

    return 
      $this->get_search_param($addParam).
      $this->strQueryTable($addParam).
      " FROM Incident
      LEFT OUTER JOIN Contact ON Incident.CustomerId = Contact.ContactId
      LEFT OUTER JOIN new_feedback ON Incident.IncidentId = new_feedback.new_incident  AND new_feedback.new_feedbackId IN (SELECT new_feedbackId FROM (SELECT new_incident,max(CreatedOn) mydate,max(new_feedbackId) new_feedbackId FROM new_feedback GROUP BY new_incident) x)
      LEFT OUTER JOIN new_compensation ON Incident.IncidentId = new_compensation.new_compensation
      
      LEFT OUTER JOIN BusinessUnit ON Incident.OwningBusinessUnit = BusinessUnit.BusinessUnitId
      LEFT OUTER JOIN (SELECT TOP 1 * FROM SystemUser WHERE DomainName = '".$login."') as SystemUser ON SystemUser.BusinessUnitId = BusinessUnit.BusinessUnitId

      ".self::getClietCountAppeal($date_begin,$date_end)."
      WHERE (SystemUser.DomainName = '".$login."'".$owningBusinessUnit_str.")".
      " AND Incident.CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'".
      (isset($addParam['filter_bell']) ? " AND ci.count > ".$addParam['bell_count']  : "").
      $this->get_requeststatus_condition( $addParam ).
      $this->get_search_condition( $addParam ).
      ( (isset($addParam['noOrderBy']) && $addParam['noOrderBy'] === true) ? "" : " ORDER BY Incident.CreatedOn DESC, Incident.IncidentId").
      (isset($addParam['offset']) ? " OFFSET ".$addParam['offset']." ROWS FETCH NEXT ".$addParam['length']." ROWS ONLY" : "");
  }

  public function getAppealsForConsAndOpManager($login,$date_begin,$date_end, $addParam = [])  
  //ищет обращения для подразделения пользователь и всех 
  {                                                                   
    return $this->db->query( $this->getAppealsForConsAndOpManager_sql($login,$date_begin,$date_end, $addParam) )->result();
  } // getAppealsForConsAndOpManager


  // ***********************Restaurant
  // *******************************************************************
  public function getCountsForRestaurant($id, $date_begin, $date_end, $addParam = []){
$st = microtime(true);
    $sql = $this->get_search_param($addParam).' SELECT ';
    $arr = [];
    $addParam['offset'] = null;
    $addParam['noOrderBy'] = true;
    //file_put_contents('log.txt', json_encode($addParam).PHP_EOL, FILE_APPEND | LOCK_EX);
    $cc=0;
    $addParam_requeststatus = isset($addParam['requeststatus']) ? $addParam['requeststatus'] : null;
    foreach(self::REQUESTSTATUS_PARAM as $key => $param) {
      if (isset($addParam_requeststatus) && $addParam_requeststatus != $key) continue;
      $addParam['requeststatus'] = $key;
      $sql .= 
      "(".
          $this->getCountsForRestaurant_sql($id, $date_begin, $date_end, $addParam).
        ") AS ".($key == 'all' ? 'all22' : $key).', ';
      //$res = $this->db->query( $sql )->result_array();
      //$arr[ $key ] = $res[0]['count'];
      //break;
      $cc++;
    } // foreach
    $sql = mb_substr($sql, 0, -2);
    $res = $this->db->query( $sql )->result_array();
    $arr = $res[0];
    if (isset($arr['all22'])) $arr['all'] = $arr['all22'];
    //file_put_contents('log.txt',  json_encode($arr). PHP_EOL, FILE_APPEND);
$ft = microtime(true);
    //file_put_contents('log_time.txt',  'getCountsForRestaurant('.$cc.') ft-st='.($ft-$st). PHP_EOL, FILE_APPEND);

    return  $arr;
  } // getCountsForRestaurant
  
  private function getCountsForRestaurant_sql($id, $date_begin, $date_end, $addParam = [])
  {
    return
      "SELECT count(*) as count".
      " FROM Incident ".
      " LEFT OUTER JOIN Contact ON Incident.CustomerId = Contact.ContactId
      JOIN Account ON Incident.new_restaurant = Account.AccountId AND Account.OwnerId ='".$id."'".
      self::getClietCountAppeal($date_begin,$date_end).
      PHP_EOL." WHERE Incident.CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'".
      $this->get_requeststatus_condition( $addParam ).
      $this->get_search_condition( $addParam ).
      (isset($addParam['filter_bell'])  ? " AND ci.count > ".$addParam['bell_count']  : "");

  } // getAppealsForRestaurant_sql

  private function getAppealsForRestaurant_sql($id, $date_begin, $date_end, $addParam = [])
  {
    return
      $this->get_search_param($addParam).
      $this->strQueryTable($addParam).
      " FROM Incident ".
      " LEFT OUTER JOIN Contact ON Incident.CustomerId = Contact.ContactId
      LEFT OUTER JOIN new_feedback ON Incident.IncidentId = new_feedback.new_incident  AND new_feedback.new_feedbackId IN (SELECT new_feedbackId FROM (SELECT new_incident,max(CreatedOn) mydate,max(new_feedbackId) new_feedbackId FROM new_feedback GROUP BY new_incident) x)
      JOIN Account ON Incident.new_restaurant = Account.AccountId AND Account.OwnerId ='".$id."'".
      " LEFT OUTER JOIN new_compensation ON Incident.IncidentId = new_compensation.new_compensation ".
      self::getClietCountAppeal($date_begin,$date_end).
      PHP_EOL." WHERE Incident.CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'".
      $this->get_requeststatus_condition( $addParam ).
      $this->get_search_condition( $addParam ).
      (isset($addParam['filter_bell'])  ? " AND ci.count > ".$addParam['bell_count'].PHP_EOL : "").
      " ORDER BY Incident.CreatedOn DESC, Incident.IncidentId".PHP_EOL.
      (isset($addParam['offset']) ? " OFFSET ".$addParam['offset']." ROWS FETCH NEXT ".$addParam['length']." ROWS ONLY" : "");
  } // getAppealsForRestaurant_sql

  public function getAppealsForRestaurant($id, $date_begin, $date_end, $addParam = [])
  {
    //file_put_contents('log.txt',  json_encode($addParam). PHP_EOL, FILE_APPEND);
    //file_put_contents('log.txt', $this->getAppealsForRestaurant_sql($id, $date_begin, $date_end, $addParam) . PHP_EOL, FILE_APPEND);
    $st = microtime(true);
    $ret = $this->db->query($this->getAppealsForRestaurant_sql($id, $date_begin, $date_end, $addParam))->result();
    $ft = microtime(true);

    //file_put_contents('log.txt',  'getAppealsForRestaurant ft-st='.($ft-$st). PHP_EOL, FILE_APPEND);
    return $ret;
  }

  // ***********************Other shit
  // *******************************************************************
  private function get_OwningBusinessUnit_list($buisnessUnitId = NULL, $login = NULL) {
    if ($buisnessUnitId  == null) {
      $childBuisnessUnit =  $this->db->query("SELECT BusinessUnitId FROM SystemUser WHERE DomainName= '".$login."' ")->result();
      $buisnessUnitId = $childBuisnessUnit[0]->BusinessUnitId;
    }
    $var = $this->db->query("SELECT BusinessUnitId FROM BusinessUnit WHERE BusinessUnit.ParentBusinessUnitId = '".$buisnessUnitId."' ");
    $var = $var->result();
    $result = [];
    foreach ($var as $value) {
      $result[] = $value->BusinessUnitId;
      $result = array_merge($result, $this->get_OwningBusinessUnit_list($value->BusinessUnitId));
    }
    return $result;
   } // get_OwningBusinessUnit_list

  function getAppealsAnswers($incidentNumber)
  {
      $query= $this->db->query("SELECT Incident.Description, new_feedback.new_feedbackId,new_feedback.OwnerIdName,new_feedback.createdon,new_feedback.new_feedbacktext,new_feedback.new_feedbacktextcorrect FROM Incident, new_feedback WHERE Incident.TicketNumber = '".$incidentNumber."' AND Incident.IncidentId = new_feedback.new_incident")->result_array();
      return $query;
  }

  function setInWork($idAppeal)
  {
    $headers = array(
      'Content-Type: application/json',
      'Accept: application/json'
    );
    $data1 = array(
    "EntityName" => "incident",
    "Id" => $idAppeal,
    "OptionSets" => array(
      array(
        "AttributeName" => "new_requeststatus",
        "Value" => "100000001"
      )
    )
  );


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



  function getAppealsOfClient($idClient){
    $query= $this->db->query($this->strQueryTable()." FROM Incident
                              LEFT OUTER JOIN Contact ON Incident.ContactId = Contact.ContactId AND Incident.ContactId= '".$idClient."'
                              LEFT OUTER JOIN new_feedback ON Incident.IncidentId = new_feedback.new_incident  AND new_feedback.new_feedbackId IN (SELECT new_feedbackId FROM (SELECT new_incident,max(CreatedOn) mydate,max(new_feedbackId) new_feedbackId FROM new_feedback GROUP BY new_incident) x)
                              LEFT OUTER JOIN new_compensation ON Incident.IncidentId = new_compensation.new_compensation
                              ".self::getClietCountAppeal()."
                              WHERE Incident.ContactId= '".$idClient."'
                              ORDER BY Incident.CreatedOn DESC")->result();


    return $query;
  }

  function getListOfFiles($idAppeal)
  {
    $query= $this->db->query("SELECT  FileName,AnnotationId FROM Annotation WHERE ObjectId = '".$idAppeal."'")->result_array();
    return $query;
  }

  function downloadFile($idFile)
  {
    $query= $this->db->query("SELECT  DocumentBody,FileName,MimeType FROM Annotation WHERE AnnotationId = '".$idFile."'")->result_array();
    return $query;
  }


  function report($date_begin, $date_end)
  {
    $query= $this->db->query("SELECT Incident.IncidentId, Incident.CreatedOn, Incident.TicketNumber, Incident.new_requeststatus, Incident.new_restaurantName, Account.new_cityName, Account.new_opsmanagerName, Account.new_consultantName, Incident.new_requesttypeName, Incident.new_requestpartName, Incident.new_requesttheme, Incident.new_requestthemeName, Incident.Description, new_compensation.new_name, Incident.new_requestchannel
    FROM Incident
    LEFT JOIN Account ON Incident.new_restaurant = Account.AccountId
    LEFT JOIN new_compensation ON Incident.IncidentId = new_compensation.new_compensation
    WHERE Incident.CreatedOn BETWEEN  CONVERT(Datetime, '".$date_begin."')  AND CONVERT(Datetime,'".$date_end."')")->result();
    
	//WHERE Incident.CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'")->result();

	// WHERE Incident.CreatedOn BETWEEN ".$date_begin." AND ".$date_end."")->result();

    return $query;

  }


}


?>
