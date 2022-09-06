<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exel_model extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function get_user_rrdi(){
		$query = $this->db->query('SELECT `rrdi` FROM `users` WHERE `id` = '.$_SESSION['user_id'].'');
		return $query->result_array();
	}
	public function del_old_list(){
		$query = $this->db->query('DELETE FROM `list_id` WHERE month != '.date('m').'');
	}
	public function del_old_list_this_month(){
		$query = $this->db->query('DELETE FROM `list_id` WHERE day < 15');
	}
	public function get_rrdi(){
		$query = $this->db->query('SELECT `rrdi` FROM `list_id`');
		return $query->result_array();
	}
	public function get_id_list($rrdi){
		$query = $this->db->query('SELECT `id`,`id_list` FROM `list_id` WHERE `rrdi` = "'.$rrdi.'"');
		return $query->result_array();
	}
	public function add_id_list($id_list, $rrdi){
		$query = $this->db->query("INSERT INTO `list_id` (`rrdi`, `month`, `day`, `id_list`) VALUES ('".$rrdi."',".date('m').",".date('d').",'".$id_list."')");
	}
	public function del_id_list($id){
		$query = $this->db->query('DELETE FROM `list_id` WHERE `id` = '.$id.'');
	}
	public function get_web_user_id(){
		$query = $this->db->query('SELECT `web_userid` FROM `users` WHERE `id` = '.$_SESSION['user_id'].'');
		return $query->result_array();
	}
	public function get_web_user_id_responsible_for_quality(){
		$query = $this->db->query('SELECT `web_userid`, `rrdi` FROM `users` WHERE `responsible_for_quality` = 1 AND `rrdi` != ""');
		return $query->result_array();
	}
// @author aws
// 2021-05-13	
	public function get_user_info(){
		$query = $this->db->query('SELECT * FROM `users` WHERE `id` = '.$_SESSION['user_id'].'');
		return $query->result_array();
	}

	public function get_users_info(){
		$query = $this->db->query('SELECT * FROM `users`');
		return $query->result_array();
	}

	// temp
	public function get_user_all(){
		$query = $this->db->query('SHOW COLUMNS FROM `users`');
		return $query->result_array();
	}


	public function get_web_userid(){
		$query = $this->db->query('SELECT `web_userid` FROM `users` WHERE `id` = '.$_SESSION['user_id'].'');
		return $query->result_array();
	}

}
?>