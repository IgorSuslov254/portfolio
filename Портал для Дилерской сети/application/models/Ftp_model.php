<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ftp_model extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function add_value_ftp($value, $brand, $TradeMark){
		if(mb_strtolower($value[20]) == 'фл'){
			$value[20] = 'Private';
		}
		else{
			$value[20] = 'Company';
		}

		if(mb_strtolower($value[23]) == 'да'){
			$value[23] = 'TRUE';
		}
		elseif(mb_strtolower($value[23]) == 'нет'){
			$value[23] = 'FALSE';
		}

		$date = new \DateTime($value[12]);
		$new_date = $date->format('Y-m-d');

		for($i = 0; $i < 27; $i++){
			if(!isset($value[$i])){
				$value[$i] = "NULL";
			}
			else{
				$value[$i] = str_replace('"', '', $value[$i]);
				$value[$i] = str_replace("'", "", $value[$i]);
				$value[$i] = trim($value[$i]);
				if($i == 4){
					$value[$i] = str_replace(';', '', $value[$i]);
				}
				if($i != 5 || $i != 10){
					$value[$i] = '"'.$value[$i].'"';
				}
			}
		}

		$query = $this->db->query('INSERT INTO `ftp` (`file_type`,`brand`,`appli_src`,`country_code`,`site_code`,`cust_type`,`cust_last_name`,`cust_first_name`,`cust_sex`,`cust_email`,`custaddress`,`cust_zip_code`,`cust_city`,`cust_mobile_number`,`agreement`,`trade_mark`,`model`,`VIN`,`InvoiceDate`) VALUES ("APV","'.$brand.'","INSTITUT","UA",'.$value[22].','.$value[20].','.$value[1].','.$value[2].','.$value[3].','.$value[11].','.$value[4].','.$value[5].','.$value[6].','.$value[10].','.$value[23].',"'.$TradeMark.'",'.$value[15].','.$value[16].',"'.$new_date.'")');
	}
	public function del_value_ftp($value){
		$query = $this->db->query('DELETE FROM `ftp` WHERE `ftp`.`site_code` = "'.$value[22].'"');
	}
	public function get_ftp(){
		$query = $this->db->query('SELECT * FROM `ftp`');
		return $query->result_array();
	}
}
?>