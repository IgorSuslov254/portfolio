<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class ComboBox_model extends CI_Model{

  public function getInfoForComboboxAddAppeal()
  {
      $resArray['new_requesttype']   = $this->db->query("SELECT new_name,new_theme1Id FROM new_theme1 ORDER BY new_name")->result_array();
      $resArray['new_restaurant']    = $this->db->query("SELECT name, AccountId from AccountBase ORDER BY name")->result_array();
      $resArray['new_food']          = $this->db->query("SELECT new_name, new_foodId FROM new_foodExtensionBase ORDER BY new_name")->result_array();
      return $resArray;
  }

  public function getTheme2($theme1)
  {
    return $this->db->query("SELECT new_name,new_theme2Id FROM new_theme2 WHERE new_1theme ='".$theme1."' ORDER BY new_name")->result_array();
  }

  public function getTheme3($theme1,$theme2)
  {
    return $this->db->query("SELECT new_name,new_theme3Id FROM new_theme3 WHERE new_2theme ='".$theme2."' AND new_1theme = '".$theme1."'  ORDER BY new_name")->result_array();
  }

  public function getInfoForComboboxAddClient()
  {
    $resArray['new_region']     = $this->db->query("SELECT new_name, new_regionId FROM new_regionExtensionBase ORDER BY new_name")->result_array();
    $resArray['new_district']   = $this->db->query("SELECT new_name,new_districtId FROM new_districtExtensionBase ORDER BY new_name")->result_array();
    return $resArray;
  }

  public function findCity($text)
  {
    $text = $text.'%';
    return $this->db->query("SELECT new_cityExtensionBase.new_cityId, CONCAT(new_cityExtensionBase.new_name,' ', new_districtExtensionBase.new_name,' район ', new_regionExtensionBase.new_name,' область') AS new_name
                              FROM new_cityExtensionBase, new_districtExtensionBase,new_regionExtensionBase
                              WHERE new_cityExtensionBase.new_region = new_regionExtensionBase.new_regionId
                              AND new_cityExtensionBase.new_district = new_districtExtensionBase.new_districtId
                              AND new_cityExtensionBase.new_name LIKE '".$text."'")->result_array();
  }

  public function findRestaurant($text)
  {
    $text = '%'.$text.'%';
    // return $this->db->query("SELECT name, AccountId from AccountBase ORDER BY name WHERE name LIKE '".$text."'")->result_array();
    return $this->db->query("SELECT name, AccountId from AccountBase WHERE name LIKE '".$text."'")->result_array();
  }

  public function getFoodList($text)   //получение еды и ее id по первым буквам для выпадающего списка
    {
      $text = '%'.$text.'%';
      return $this->db->query("SELECT new_name, new_foodId
                               FROM new_foodExtensionBase
                               WHERE new_name
                               LIKE '".$text."'
                               ORDER BY new_name")->result_array();
    }
}
?>
