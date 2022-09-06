<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_model extends CI_Model
{
  public function getPaymentList($date_begin,$date_end,$id = -1)
  {

    $strQuery = "SELECT new_compensation.new_name,new_compensation.new_compensationid,
                              CONCAT (Incident.CustomerIdName,' <a href=\"mailto:',Contact.EMailAddress1,'\">',Contact.EMailAddress1,'</a> ',' <a href=\"tel:',Contact.MobilePhone,'\">',
                              Contact.MobilePhone,'</a>') AS CustomerIdName,
                              new_compensation.CreatedOn,new_compensation.new_enddate,
                              CONCAT(new_compensation.new_1foodName,',',new_compensation.new_2foodName,',',new_compensation.new_3foodName,',',new_compensation.new_4foodName,',',new_compensation.new_5foodName,',',new_compensation.new_6foodName,',',new_compensation.new_7foodName,',',new_compensation.new_8foodName,',',		 new_compensation.new_9foodName,',',new_compensation.new_10foodName,',',new_compensation.new_11foodName,',',new_compensation.new_12foodName,',',new_compensation.new_13foodName,',',new_compensation.new_14foodName,',',new_compensation.new_15foodName,',',new_compensation.new_16foodName,',',new_compensation.new_17foodName,',',new_compensation.new_18foodName,',',new_compensation.new_19foodName,',',new_compensation.new_20foodName) AS food,
                              new_compensation.new_createdbyName,new_compensation.new_status,new_compensation.new_usedbyName,Incident.Description
															FROM new_compensation
															JOIN Incident ON new_compensation.new_compensation = Incident.IncidentId
															LEFT OUTER JOIN Contact ON Incident.CustomerId = Contact.ContactId
                              WHERE new_compensation.CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."' ";
    if ($id != -1)
    {
      $strQuery .= " AND  new_createdby = '".$id."' ";      //если передается id ресторана
    }
    $strQuery .= "ORDER BY CreatedOn DESC";
    $query= $this->db->query($strQuery)->result_array();
   foreach ( $query as $key => $value)
   {
     $query[$key]['food'] = trim ( $query[$key]['food'] ,$characters = " ," );    //удаление лишних запятых
   }

    return $query;
  }

  function getPaymentForCheck()           //определяем компенсации (HOLD & Active), время которых вышло
    {
      $result = $this->db->query("SELECT new_compensationId,new_enddate,new_status FROM new_compensation WHERE (new_status = '100000000' OR new_status = '100000001') AND new_enddate <= GETDATE()")->result_array();
      return $result;
    }
}
?>
