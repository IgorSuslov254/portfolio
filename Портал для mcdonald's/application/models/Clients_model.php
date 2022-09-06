<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Clients_model extends CI_Model{
  function findClients($emailANDPhone)
  {
      $emailANDPhone = $emailANDPhone.'%';
      $query= $this->db->query("SELECT LastName,ContactId, FirstName, middlename, MobilePhone, EMailAddress1, GenderCode, new_regionName, new_districtName, new_cityName FROM Contact WHERE MobilePhone LIKE '".$emailANDPhone."' OR EMailAddress1 LIKE '".$emailANDPhone."'")->result_array();
      $this->decodeGenderCode($query);
      return $query;
  }


  function decodeGenderCode(&$client)
  {
    foreach ($client as $key => $value)
    {
      switch ($client[$key]['GenderCode'])
      {
        case 1:
          $client[$key]['GenderCode'] = 'Жінка';
          break;
        case 2:
          $client[$key]['GenderCode'] = 'Чоловік';
          break;
      }
    }
  }

}

?>
