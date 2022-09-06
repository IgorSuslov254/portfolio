<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Authentication_model extends CI_Model {
	function get_users($login, $password){
		$query = $this->db->query("SELECT SystemUser.SystemUserId, SystemUser.FullName, RoleBase.Name FROM SystemUser, SystemUserRoles, RoleBase WHERE SystemUser.SystemUserId = SystemUserRoles.SystemUserId AND SystemUserRoles.RoleId = RoleBase.RoleId AND SystemUser.domainname = '".$login."' AND SystemUser.address1_telephone3 = '".$password."'");
		return $query->result_array();
	}
	function recover_password($recover_login){
		$query = $this->db->query("SELECT Address1_Telephone3, InternalEMailAddress FROM SystemUser WHERE domainname = '".$recover_login."'");
		return $query->result_array();
	}
	function system_users(){
		$query = $this->db->query("SELECT * FROM SystemUser");
		return $query->result_array();
	}
}
?>