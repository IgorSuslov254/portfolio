<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Customers extends CI_Controller
{
	public function index(){
		echo $this->breadcrumb().$this->load->view('customers_view', '', TRUE);
	}
	public function search_clients(){
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$client = new Clients();
		$client_date = $client->findClients($user, $_POST['name']);
		if($client_date){
			$template = array('table_open' => '<table class="display table_customers nowrap">');
			$this->table->set_template($template);
			$this->table->set_heading("Прізвище", "id", "Ім'я", "По-батькові", "Мобільний телефон", "E-mail", "Стать", "Область", "Район", "Населений пункт");
			foreach ($client_date as $key => $client) {
				$this->table->add_row($client);
			}
			echo $this->table->generate();
		}
		else{
			echo "no";
		}
	}
	public function more_information_customer(){
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$client = new Clients();
		$more_information_customer = $client->appealsAndCallsOfClients($_POST['id_customer']);
		
		if($more_information_customer['appeals'] || $more_information_customer['calls']){
			$template = array(
				'table_open' => '
					<hr>
					<div class="col-12">
						<h2 class="text-center mt-4">Звернення клієнта</h2>
						<table class="display more_information_customer" style="width:100%">
				',
				'table_close'        => '</table></div>'
			);
			$this->table->set_template($template);
			$this->table->set_heading(HEDING_TABEL);
			if($more_information_customer['appeals']){
				foreach($more_information_customer['appeals'] as $key => $appeals){
					$this->table->add_row($appeals);
				}
			}
			$view_appeals_customer = $this->table->generate();

			$template = array(
				'table_open' => '
					<div class="col-12">
						<h2 class="text-center mt-4">Дзвінки клієнта</h2>
						<table class="display more_information_customer">',
				'table_close'        => '</table></div>'
			);
			$this->table->set_template($template);
			$this->table->set_heading('Call ID', 'id', 'Номер АОН', 'Клієнт', 'Тематика дзвінка', 'Ресторан', 'Звернення','Посилання на запис', 'Дата/час створення', 'Тривалість (секунд)');
			if($more_information_customer['calls']){
				foreach($more_information_customer['calls'] as $key => $calls){
					$this->table->add_row($calls);
				}
			}
			$view_calls_customer = $this->table->generate();
			echo $view_appeals_customer.$view_calls_customer;
		}
		else{
			echo 'no';
		}
	}
	public function search_city(){
		$this->load->model('ComboBox_model');
		$data['city'] = $this->ComboBox_model->findCity($_POST['city']);
		$view = '';
		foreach ($data['city'] as $key => $city) {
			$view .= '<div id="'.$city['new_cityId'].'"><h3>'.$city['new_name'].'</h3></div>';
		}
		echo $view;
	}
	public function search_restaurant(){
		$this->load->model('ComboBox_model');
		$data['restaurant'] = $this->ComboBox_model->findRestaurant($_POST['restaurant']);
		$view = '';
		foreach ($data['restaurant'] as $key => $restaurant) {
			$view .= '<div id="'.$restaurant['AccountId'].'"><h3>'.$restaurant['name'].'</h3></div>';
		}
		echo $view;
	}
	public function createAppeal(){
		$data['modal_add_appeals_form_channel'] =  isset($_POST['modal_add_appeals_form_channel'])?$_POST['modal_add_appeals_form_channel']:NULL;//Канал поступления
		$data['modal_add_appeals_form_contact_hide'] = isset($_POST['modal_add_appeals_form_contact_hide'])?$_POST['modal_add_appeals_form_contact_hide']:NULL; //Клиент
		$data['modal_add_appeals_form_new_requesttype'] = isset($_POST['modal_add_appeals_form_new_requesttype'])?$_POST['modal_add_appeals_form_new_requesttype']:NULL; // Тип обращения
		$data['modal_add_appeals_form_new_requestpart'] = isset($_POST['modal_add_appeals_form_new_requestpart'])?$_POST['modal_add_appeals_form_new_requestpart']:NULL; // Раздел обращения /null
		$data['modal_add_appeals_form_new_requesttheme'] = isset($_POST['modal_add_appeals_form_new_requesttheme'])?$_POST['modal_add_appeals_form_new_requesttheme']:NULL; // тема обращения /null
		$data['modal_add_appeals_form_new_restaurant'] = isset($_POST['modal_add_appeals_form_new_restaurant'])?$_POST['modal_add_appeals_form_new_restaurant']:NULL; // ресторан
		$data['modal_add_appeals_form_date'] = isset($_POST['modal_add_appeals_form_date'])?$_POST['modal_add_appeals_form_date']:NULL; // Дата и время
		$data['modal_add_appeals_form_chek'] = isset($_POST['modal_add_appeals_form_chek'])?$_POST['modal_add_appeals_form_chek']:NULL; // Наличие чека
		$data['modal_add_appeals_form_number'] = isset($_POST['modal_add_appeals_form_number'])?$_POST['modal_add_appeals_form_number']:NULL; // номер чека
		$data['answer_appeals_form_description'] = isset($_POST['answer_appeals_form_description'])?$_POST['answer_appeals_form_description']:NULL; // суть обращения
		$data['modal_add_appeals_form_new_food'] = isset($_POST['modal_add_appeals_form_new_food'])?$_POST['modal_add_appeals_form_new_food']:NULL; // блюдо /null
		$data['modal_add_appeals_form_inclusion'] = isset($_POST['modal_add_appeals_form_inclusion'])?$_POST['modal_add_appeals_form_inclusion']:NULL; // Тип постороннего включения /null
		$data['modal_add_appeals_form_new_employee'] = isset($_POST['modal_add_appeals_form_new_employee'])?$_POST['modal_add_appeals_form_new_employee']:NULL; // ФИО сотрудника, должность /null
		$data['answer_appeals_form_new_object'] = isset($_POST['answer_appeals_form_new_object'])?$_POST['answer_appeals_form_new_object']:NULL; // Что было не убрано / грязное? /null
		if(!empty($_FILES['file']['tmp_name']))
		{
			$file = file_get_contents($_FILES['file']['tmp_name']);
			$data['file']['file'] = base64_encode($file); // Файл бинарный
			$data['file']['type'] = $_FILES['file']['type'];
			$data['file']['filename'] = $_FILES['file']['name'];
		}

		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		if($_SESSION['count'] == 0){
			if($user->addAppeal($data)){
				$_SESSION['count']++;
				echo $_SESSION['count'];
			}
		}
	}
	public function createClient(){
		// Получаешь следующие данные
			$data['modal_сustomers_form_surname'] = $_POST['modal_сustomers_form_suname'];				// Фамилия				// string
			$data['modal_сustomers_form_name'] = $_POST['modal_сustomers_form_name'];					// Имя					// string
			$data['modal_сustomers_form_father_name'] = $_POST['modal_сustomers_form_father_name'];		// Отчество				// string
			$data['modal_сustomers_form_phone'] = $_POST['modal_сustomers_form_phone'];					// Мобильный телефон	// string
			$data['modal_сustomers_form_email'] = $_POST['modal_сustomers_form_email'];					// Email 				// string
			$data['modal_сustomers_form_city_hide'] = $_POST['modal_сustomers_form_city_value'];			// Населённый пункт		// id sity (string)
			$data['modal_сustomers_form_email_gender'] = $_POST['modal_сustomers_form_email_gender'];	// Пол:					// int (Ж-0; М-1 "Если надо -изменим")
			$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
			if($_SESSION['count'] == 0){
				if($user->addClient($data)){
					$_SESSION['count']++;
					echo $_SESSION['count'];
				}
			}
	}
	public function get_Theme2(){
		$this->load->model('ComboBox_model');
		$data['getTheme2'] = $this->ComboBox_model->getTheme2($_POST['them1']);
		$view = '';
		foreach($data['getTheme2'] as $key => $getTheme2){
			$view .= '<option value="'.$getTheme2['new_theme2Id'].'">'.$getTheme2['new_name'].'</option>';
		}
		echo $view;
	}
	public function get_Theme3(){
		$this->load->model('ComboBox_model');
		$data['getTheme3'] = $this->ComboBox_model->getTheme3($_POST['them1'], $_POST['them2']);
		$view = '';
		foreach($data['getTheme3'] as $key => $getTheme3){
			$view .= '<option value="'.$getTheme3['new_theme3Id'].'">'.$getTheme3['new_name'].'</option>';
		}
		echo $view;
	}
	private function breadcrumb(){
		$breadcrumb = '
			<section id="head_mein">
				<div class="container-fluid">
					<div class="container">
						<div class="row">
							<div class="col-12 text-center">
								<h1>Клієнти</h1>
							</div>
						</div>
					</div>
				</div>
			</section>
		';
		return $breadcrumb;
	}
}
?>
