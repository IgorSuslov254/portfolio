<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Calls_model extends CI_Model{
  function getCalls($date_begin,$date_end)
  {
    $query= $this->db->query("SELECT new_callid,ActivityId,phonenumber,new_contactName,new_callthemeName,new_restaurantName,new_incidentName,new_callrecording,createdon,new_callduration
                              FROM PhoneCall
                              WHERE new_callthemeName IS NOT NULL AND CreatedOn BETWEEN '".$date_begin."' AND '".$date_end."'")->result_array();
    return $query;
  }
  function getCallsOfClient($idClient)
  {
    $query= $this->db->query("SELECT phonecall.new_callid,phonecall.ActivityId,phonecall.phonenumber,phonecall.new_contactName,phonecall.new_callthemeName,phonecall.new_restaurantName,phonecall.new_incidentName,phonecall.new_callrecording,phonecall.createdon,phonecall.new_callduration FROM PhoneCall, Contact WHERE PhoneCall.new_callthemeName IS NOT NULL AND Contact.ContactId = PhoneCall.new_contact AND PhoneCall.new_contact = '".$idClient."' ")->result_array();
    return $query;
  }
}
?>
