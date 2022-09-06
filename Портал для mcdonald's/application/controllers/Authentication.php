<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Authentication extends CI_Controller {
	public function index(){
		// if(@$_SERVER['SSL_TLS_SNI'] == 'mcd-portal.crm.servicedesk.in.ua' && $_SERVER['REQUEST_URI'] == '/CRMProject/'){
			$data['over'] = 'over';
			$this->load->view('head', $data);
			$this->load->view('authentication');
			$this->load->view('modal');
			$this->load->view('footer');
		// }
		// else{
		// 	header('Location: '.base_url().'');
		// }
	}
	public function input(){
		$login 		= $_POST['login'];
		$password 	= $_POST['password'];

		$this->load->model('Authentication_model');
		$data['table'] = $this->Authentication_model->get_users($login, $password);

		if($data['table']){
			if($data['table'][0]['Name'] == 'Офіс' || $data['table'][0]['Name'] == 'Операційний менеджер' || $data['table'][0]['Name'] == 'Консультант' || $data['table'][0]['Name'] == 'Ресторан' || $data['table'][0]['Name'] == 'Модератор'){
				foreach ($data['table'] as $key => $table) {
					$this->session->set_userdata('User_FullName', $table['FullName']);
					$this->session->set_userdata('SystemUserId', $table['SystemUserId']);
					$this->session->set_userdata('Role_Name', $table['Name']);
					$this->session->set_userdata('login', $_POST['login']);
				}
				echo 'yes';
			}
			else{
				echo "Даний користувач не має доступу до порталу";
			}
		}
		else{
			echo 'Невірно введено логін або пароль!';
		}
	}
	public function modal_recover_password(){
		$recover_login = $_POST['recover_login'];

		$this->load->model('Authentication_model');
		$data['recover_password'] = $this->Authentication_model->recover_password($recover_login);
		if($data['recover_password']){
			foreach ($data['recover_password'] as $key => $recover_password) {
				$this->email->from('');
				$this->email->to($recover_password['InternalEMailAddress']);
				$this->email->subject('Відновлення пароля');
				$this->email->message('Ваш пароль:'.$recover_password['Address1_Telephone3']);
				$this->email->send();
			}
			echo 'yes';
		}
		else{
			echo 'Вказаний користувач не зареєстрований в системі';
		}
	}
}
?>
