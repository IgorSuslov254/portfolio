<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends CI_Controller {
	
	public function index(){
		// ini_set('memory_limit','1024M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
		// ini_set('sqlsrv.ClientBufferMaxKBSize','1024288'); // Setting to 512M
		// ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

		$_SESSION['count'] = 0;
		//$this->session->Role_Name = 'Операційний менеджер';
		//echo $this->session->Role_Name;

		if(isset($this->session->User_FullName) && isset($this->session->Role_Name)){
			if(empty($_GET['view_appealse'])){
				if(empty($this->session->view_appealse)){
					$this->session->set_userdata('view_appealse', 'new');
				}
			}
			else{
				$this->session->set_userdata('view_appealse', $_GET['view_appealse']);
			}
			if(!empty($_GET['date_start']) && !empty($_GET['date_end'])){
				$this->session->set_userdata('date_start', $_GET['date_start']);
				$this->session->set_userdata('date_end', $_GET['date_end']);
			}
			$this->creat_table();
		}
		else{
			$this->exit();
		}
	}
	public function creat_table(){
		$data['breadcrumb'] = $this->breadcrumb();
		$data['view_date_picker'] = $this->load->view('view_date_picker', '', TRUE);

		$template = array('table_open' => '<table id="appeals" class="display" style="width:100%">');
		$this->table->set_template($template);
		$this->table->set_heading(HEDING_TABEL);

		/*echo '<script> console.log("'.$this->session->login.'") </script>';
		echo '<script> console.log("'.$this->session->Role_Name.'") </script>';
		echo '<script> console.log("'.$this->session->SystemUserId.'") </script>';*/

		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$app = new Appeals();



		// @todo count
	
		if(!empty($this->session->date_start) && !empty($this->session->date_end)){
			$data['appeals_count'] = $app->getListOfAppeals_count($user, date("Ymd H:i:s", strtotime($this->session->date_start)), date("Ymd H:i:s", strtotime($this->session->date_end)));
			$data['data_start'] = date("d-m-Y", strtotime($this->session->date_start));
			$data['data_end'] = date("d-m-Y", strtotime($this->session->date_end));
		}
		else{
			$data['appeals_count'] = $app->getListOfAppeals_count($user);
			$data['data_start'] = date('01-m-Y');
			$data['data_end'] = date('t-m-Y');
		}

		//var_dump( $data['appeals_count'] );

// 		getCounts_forConsAndOpManager($_GET['date_start'], $_GET['date_end'], []);	



/*
		if(!empty($this->session->date_start) && !empty($this->session->date_end)){
			$data['appeals'] = $app->getListOfAppeals($user, date("Ymd H:i:s", strtotime($this->session->date_start)), date("Ymd H:i:s", strtotime($this->session->date_end)));
			$data['data_start'] = date("d-m-Y", strtotime($this->session->date_start));
			$data['data_end'] = date("d-m-Y", strtotime($this->session->date_end));
		}
		else{
			$data['appeals'] = $app->getListOfAppeals($user);
			$data['data_start'] = date('01-m-Y');
			$data['data_end'] = date('t-m-Y');
		}

		if(!empty($data['appeals'])){
			foreach ($data['appeals'] as $key1 => $value1){
				foreach ($data['appeals'][$key1] as $key2 => $value2)
				{
					$data['appeals']['all'][] = $value2;
				}
			}
		}
*/
		$this->config->load('main_config');

		$data['set_admin_count'] = $this->config->item('bell_count');

		$this->load_view($data);

		// if(isset($data['appeals'])){
		// 	foreach ($data['appeals'] as $key1 => $value1){
		// 		foreach ($data['appeals'][$key1] as $key2 => $value2)
		// 		{
		// 			$data['appeals']['all'][] = $value2;
		// 		}
		// 	}
		// 	if(isset($data['appeals'][$view_appealse])){
		// 		$template = array('table_open' => '<table id="appeals" class="display">');
		// 		$this->table->set_template($template);
		// 		$this->table->set_heading("Номер звернення", "ID_звернення", "Канал надходження", "Дата/час створення", "Клієнт", "Тип звернення", "Розділ звернення", "Тема звернення", "Ресторан", "Страва", "Дата/час інцидента", "Сума чека", "Номер чека / Номер замовлення", "Тип стороннього включення", "ПІБ співробітника, посада","Що було не прибрано/брудне?", "Суть звернення", "Відповідальний", "Статус звернення", "Годин в роботі", "Допомога PR-відділу чи юриста", "Чи задоволений клієнт зворотним зв'язком", "Коментарі клієнта по зворотному зв'язку", "Опрацьовано вчасно", "Коментарі ресторану", "Додатки", "Дата/час створення (Відповіді)", "Текст відповіді", "Текст відповіді з доповненням/корекцією","Код компенсації", "Дата створення", "Строк дії", "Страва", "Ресторан, який створив Компенсацію", "Ресторан, який погасив Компенсацію", "Response ID", "OSAT", "Додатково", "Voucher Code", "POS", "Manual POS category");
		// 		foreach ($data['appeals'][$view_appealse] as $key => $new) {
		// 			$this->table->add_row($new);
		// 		}
		// 		if(isset($method) && $method == 'ajax'){
		// 			echo $data['breadcrumb'].'<h2 class="text-center mt-5">Показано звернення за період з '.$data['data_start'].' до '.$data['data_end'].'</h2>'.$this->table->generate();
		// 		}
		// 	}
		// 	else{
		// 		if(isset($method) && $method == 'ajax'){
		// 			echo $data['breadcrumb'].'<h2 class="text-center mt-5">В період з '.$data['data_start'].' до '.$data['data_end'].' відсутні дані</h2>';
		// 		}
		// 		else{
		// 			$data['appeals_view'] = FALSE;
		// 		}
		// 	}
		// }
		// else{
		// 	if(isset($method) && $method == 'ajax'){
		// 		echo $data['breadcrumb'].'<h2 class="text-center mt-5">В період з '.$data['data_start'].' до '.$data['data_end'].' відсутні дані</h2>';
		// 	}
		// 	else{
		// 		$data['appeals_view'] = FALSE;
		// 	}
		// }
		// if(!isset($method)){
		// 	$this->load_view($data);
		// }
	}
	private function load_view($data){
		$this->load->model('ComboBox_model');
		$data['ComboBox_model'] = $this->ComboBox_model->getInfoForComboboxAddClient();
		$data['getInfoForComboboxAddAppeal'] = $this->ComboBox_model->getInfoForComboboxAddAppeal();
		$data['getTheme2'] = $this->ComboBox_model->getTheme2('3EFE3486-86F2-EA11-81BE-00155D1F050B');
		$data['getTheme3'] = $this->ComboBox_model->getTheme3('3EFE3486-86F2-EA11-81BE-00155D1F050B', '7DB2F6F9-0F03-EB11-81C0-00155D1F050B');

		$this->load->view('head', $data);
		$this->load->view('header');
		$this->load->view('main');
		$this->load->view('modal_appeals_change');
		$this->load->view('modal_answer_appeals');
		$this->load->view('modal_сustomers', $data);
		$this->load->view('modal_add_appeals', $data);
		$this->load->view('modal_create_payment');
		$this->load->view('madal_confirm');
		$this->load->view('modal_main');
		$this->load->view('modal_customer_appeals');
		$this->load->view('footer');
	}
	private function breadcrumb(){
		$breadcrumb = '
			<section id="head_mein">
				<div class="container-fluid">
					<div class="container">
						<div class="row">
							<div class="col-lg-6 text-center text-lg-left">
								<h1>Звернення</h1>
								<h2>Ви можете переглянути звернення за будь-який період</h2>
							</div>
							<div class="col-lg-6 text-center text-sm-left mt-3 mt-lg-0">
								<form id="view_date_picker" action="Portal" method="get">
									<div class="container-fluid">
										<div class="row">
											<div class="col-sm-4">
												<label id="date_start_label" for="date_start" class="float-left"><h3>Від</h3></label>
												<input type="text" id="date_start" class="form-control datepicker" name="date_start" value="'.$this->session->date_start.'">
											</div>
											<div class="col-sm-4 mt-3 mt-sm-0">
												<label id="date_end_label" for="date_end" class="float-left"><h3>До</h3></label>
												<input type="text" id="date_end" class="form-control datepicker" name="date_end" value="'.$this->session->date_end.'">
												<input type="hidden" id="errors_click" value="0">
											</div>
											<div class="col-sm-4 align-self-end">
												<button type="submit" class="btn button_yellow"><h2>Показати</h2></button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</section>
		';
		return $breadcrumb;
	}
	public function change_appeals(){
		$arrayOfForm['modal_input_help_PR']		=  !empty($_POST['modal_input_help_PR'])?$_POST['modal_input_help_PR']:NULL;
		$arrayOfForm['modal_input_feedback']	=  !empty($_POST['modal_input_feedback'])?$_POST['modal_input_feedback']:NULL;
		$arrayOfForm['modal_input_comments']	=  !empty($_POST['modal_input_comments'])?$_POST['modal_input_comments']:NULL;
		$arrayOfForm['modal_input_notes']		=  !empty($_POST['modal_input_notes'])?$_POST['modal_input_notes']:NULL;
		if(!empty($_FILES['file']['tmp_name']))
		{
			$file = file_get_contents($_FILES['file']['tmp_name']);
			$arrayOfForm['file']['file'] = base64_encode($file); // Файл бинарный
			$arrayOfForm['file']['type'] = $_FILES['file']['type'];
			$arrayOfForm['file']['filename'] = $_FILES['file']['name'];
		}

		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$app = new Appeals();
		if($_SESSION['count'] == 0){
			if($app->changeAppeal($user,$_POST['number_appeals'],$arrayOfForm)){
				$_SESSION['count']++;
				echo $_SESSION['count'];
			}
		}
	}
	public function accept_work(){
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$app = new Appeals();
		if($_SESSION['count'] == 0){
			if($app->setInWork($user,$_POST['input_number'])){
				$_SESSION['count']++;
				echo $_SESSION['count'];
			}
		}
	}
	public function take_where_i(){
		echo $this->session->Role_Name;
	}
	public function view_appealse(){
		switch($this->session->view_appealse){
			case 'new':
				$view_appealse = 'Нові';
				break;
			case 'inWork':
				$view_appealse = 'В роботі';
				break;
			case 'expired':
				$view_appealse = 'Прострочені';
				break;
			case 'feedback':
				$view_appealse = 'На зворотній зв‘язок КЦ';
				break;
			case 'closePersonal':
				$view_appealse = 'Закрито співробітником McD';
				break;
			case 'closeOper':
				$view_appealse = 'Закрито';
				break;
			case 'rework':
				$view_appealse = 'На доопрацювання';
				break;
			case 'helpPR':
				$view_appealse = 'Потрібна допомога PR';
				break;
			case 'helpLawyer':
				$view_appealse = 'Потрібна допомога юриста';
				break;
			case 'all':
				$view_appealse = 'Всі';
				break;
		}
		echo $view_appealse;
	}
	public function answer_appeals(){
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$app = new Appeals();
		$answer_appeals = $app->getListOfAnswers($user, $_POST['input_number']);
		if($answer_appeals){
			$view = '
				<div class="modal fade" id="modal_answer_appeals_change" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog modal-notify modal-lg modal-info" role="document">
						<div class="modal-content">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true" class="white-text float-right">&times;</span>
							</button>
							<h2 class="font-weight-bold">Змінити відповідь на звернення</h2>
							<hr>
							<form id="answer_appeals_form_change">
								<div class="container">
									<div class="row">
										<div class="col-sm-6 mt-3">
											<label class="float-left" for="answer_appeals_form_ownerid">Відповідальний</label>
											<input type="text" id="answer_appeals_form_ownerid" class="form-control" name="answer_appeals_form_ownerid" value="'.$answer_appeals[0]['OwnerIdName'].'" disabled>
										</div>
										<div class="col-sm-6 mt-3">
											<label class="float-left" for="answer_appeals_form_createdon">Дата/час створення</label>
											<input type="text" id="answer_appeals_form_createdon" class="form-control" name="answer_appeals_form_createdon" value="'.$answer_appeals[0]['createdon'].'" disabled>
										</div>
										<div class="col-12 mt-3">
											<label class="float-left" for="answer_appeals_form_incident">Звернення</label>
											<textarea id="answer_appeals_form_incident" class="form-control" name="answer_appeals_form_incident">'.$answer_appeals[0]['Description'].'</textarea>
											<input type="hidden" id="id_app" name="id_app" value="'.$answer_appeals[0]['new_feedbackId'].'">
										</div>
										<div class="col-12 mt-3">
											<label class="float-left" for="answer_appeals_form_feedbacktext">Текст відповіді</label>
											<textarea id="answer_appeals_form_feedbacktext" class="md-textarea form-control" name="answer_appeals_form_feedbacktext" required>'.$answer_appeals[0]['new_feedbacktext'].'</textarea>
										</div>
										<div class="col-12 mt-3">
											<label class="float-left" for="answer_appeals_form_feedbacktextcorrect">Текст відповіді з доповненням/корекцією</label>
											<textarea id="answer_appeals_form_feedbacktextcorrect" class="md-textarea form-control" name="answer_appeals_form_feedbacktextcorrect">'.$answer_appeals[0]['new_feedbacktextcorrect'].'</textarea>
										</div>
									</div>
								</div>
							</form>
							<div class="container-fluid mt-3">
								<div class="row">
									<div class="col-sm-6"><button type="submit" form="answer_appeals_form_change" class="btn color_button_black btn-rounded"><H3>Змінити</H3></button></div>
									<div class="col-sm-6"><button type="button" data-dismiss="modal" class="btn btn-rounded"><h3>Ні, дякую</h3></button></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			';
			echo $view;
		}
		else{
			echo 'no';
		}
	}
	public function answer_appeals_form(){
		$data["id"] = $this->session->SystemUserId;
		$data["idAppeal"] = $_POST['id_app'];
		$data["text"] = $_POST['answer_appeals_form_feedbacktext'];
		$data["new_result"] = $_POST['answer_appeals_form_new_result'];
		if(isset($_POST['answer_appeals_form_feedbacktextcorrect'])){
			$data["correcttext"] = $_POST['answer_appeals_form_feedbacktextcorrect'];
		}
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		if($_SESSION['count'] == 0){
			if($user->addAnswer($data)){
				$_SESSION['count']++;
				echo $_SESSION['count'];
			}
		}
	}
	public function answer_appeals_form_change(){
		$data['id_app'] = $_POST['id_app']; //id ответа на обращение, которое ты мне передаёшь
		$data['answer_appeals_form_feedbacktext'] = !empty($_POST['answer_appeals_form_feedbacktext'])?$_POST['answer_appeals_form_feedbacktext']:" "; //текст ответа
		$data['answer_appeals_form_feedbacktextcorrect'] = !empty($_POST['answer_appeals_form_feedbacktextcorrect'])?$_POST['answer_appeals_form_feedbacktextcorrect']:" ";// дополнительный текст ответа
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		if($_SESSION['count'] == 0){
			if($user->changeAnswer($data)){
				$_SESSION['count']++;
				echo $_SESSION['count'];
			}
		}
	}
	public function download_file(){
		$app = new Appeals;
		$files = $app->downloadFiles($_POST['id_app']);
		if($files){
			$view = '';
			foreach ($files as $key => $files_value) {
				$view .= '
					<a href="Portal/dowload/'.$files_value['AnnotationId'].'">'.$files_value['FileName'].'</a></br>
				';
			}
			echo $view;
		}
		else{
			echo 'вкладених файлів немає';
		}
	}
	public function dowload($data){
		$app = new Appeals;
		$files = $app->downloadThisFile($data);
		if($files){
			foreach($files as $key => $files_value){
				if($files_value['DocumentBody']){
					header('Content-Type:'.$files_value['MimeType']);
					header('Content-Disposition: attachment; filename="'.$files_value['FileName'].'"');
					echo base64_decode($files_value['DocumentBody']);
				}
			}
		}
	}
	public function exit(){
		unset($_SESSION['User_FullName']);
		unset($_SESSION['Role_Name']);
		unset($_SESSION['login']);
		unset($_SESSION['view_appealse']);
		unset($_SESSION['SystemUserId']);
		header('Location:'.base_url().'');
	}
	public function server_tabel(){

		// ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
		// ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
		//ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv

		$customer_id = $this->input->get('customer_id');
		$filter_bell = $this->input->get('filter_bell');
		$addParam = [ 
			'customer_id' => $customer_id,
			'filter_bell' => $filter_bell,
			'offset'      => isset($_POST['start']) ?  $_POST['start'] : null,
			'length'      => isset($_POST['length']) ? $_POST['length'] : null,
			'requeststatus' => $this->session->view_appealse
		];
		// add data from config file	                  
	    if ( isset( $addParam['filter_bell'] ) ) { 
	    	$this->config->load('main_config');
	    	$addParam['bell_count'] = $this->config->item('bell_count');
	    	if ( !isset($addParam['bell_count']) ) $addParam['bell_count'] = 1;
	    }
	                  
		//file_put_contents('log.txt', json_encode($addParam) . PHP_EOL, FILE_APPEND);

		if( isset( $customer_id ) ){
			$user = Users::setRole($this->session->login,'Офіс',$this->session->SystemUserId);
			$addParam['requeststatus'] = 'all';
		} else{
			$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		}

		if(isset($_POST['search']['value']) && strlen($_POST['search']['value']) > 2){
			$addParam['search'] = str_replace('*', '', $_POST['search']['value']);
		}

		// Получение всех обращений
			$app = new Appeals();
			if(!empty($this->session->date_start) && !empty($this->session->date_end)){
				$data['appeals'] = $app->getListOfAppeals($user, date("Ymd H:i:s", strtotime($this->session->date_start)), date("Ymd H:i:s", strtotime($this->session->date_end)), $addParam);
		$st = microtime(true);				
				$data['appeals_count'] = $app->getListOfAppeals_count($user, date("Ymd H:i:s", strtotime($this->session->date_start)), date("Ymd H:i:s", strtotime($this->session->date_end)), $addParam);
		$ft = microtime(true);

				$data['data_start'] = date("d-m-Y", strtotime($this->session->date_start));
				$data['data_end'] = date("d-m-Y", strtotime($this->session->date_end));
			}
			else{
				$data['appeals'] = $app->getListOfAppeals($user, -1, -1, $addParam);
				$data['appeals_count'] = $app->getListOfAppeals_count($user, -1, -1, $addParam);
				$data['data_start'] = date('01-m-Y');
				$data['data_end'] = date('t-m-Y');
			}
			
			$recordsTotal = 0;
			if(!empty($data['appeals'][$this->session->view_appealse]))
				$recordsTotal = $data['appeals_count'][$this->session->view_appealse];
			$recordsFiltered = $recordsTotal;

			if ( isset( $addParam['search'] ) ) {
				$addParam['search'] = null;
				$data['appeals_count2'] = $app->getListOfAppeals_count($user, date("Ymd H:i:s", strtotime($this->session->date_start)), date("Ymd H:i:s", strtotime($this->session->date_end)), $addParam);
				 $recordsTotal = $data['appeals_count2'][$this->session->view_appealse];
			}
			
/*
			if(!empty($data['appeals'])){
				foreach ($data['appeals'] as $key1 => $value1){
					foreach ($data['appeals'][$key1] as $key2 => $value2)
					{
						$data['appeals']['all'][] = $value2;
					}
				}
			}
*/
		if( isset( $customer_id ) ) {
			$out_no_server = array(
				'data' => array()
			);
			foreach ($data['appeals']['all'] as $key => $value) {
				$out_no_server['data'][] = array_values( $value );
			}
			//echo '{"data": []}';
			echo json_encode( $out_no_server, JSON_HEX_APOS  );
			return;
		}

		// получение параметров запрса от datatable
			$draw 	= $_POST['draw'];
			$start 	= $_POST['start'];
			$length = $_POST['length'];
			$stop 	= $start + $length;
/*
		if(isset($_POST['search']['value']) && strlen($_POST['search']['value']) > 2){
			$search = '~'.str_replace('*', '', $_POST['search']['value']).'~i';
			foreach($data['appeals'][$this->session->view_appealse] as $key => $appeals){
				if(empty(preg_grep_keys_values($search, $data['appeals'][$this->session->view_appealse][$key]))){
					unset($data['appeals'][$this->session->view_appealse][$key]);
				}
			}
			$recordsFiltered = count($data['appeals'][$this->session->view_appealse]);
		}
		else{
			$recordsFiltered = $recordsTotal;
		}
*/
		//$recordsFiltered = empty($data['appeals'][$this->session->view_appealse]) ? 0 : count($data['appeals'][$this->session->view_appealse]);
		
		// формирование массива данных
/*		
			if(!empty($data['appeals'][$this->session->view_appealse])){
				$i = 0;
				foreach($data['appeals'][$this->session->view_appealse] as $key => $appeals){
					if($i >= $start && $i < $stop){
						$j = 0;
						foreach ($data['appeals'][$this->session->view_appealse][$key] as $key_new => $new){
							$data_tabel[$i-$start][$j] = $data['appeals'][$this->session->view_appealse][$key][$key_new];
							$j++;
						}
					}
					$i++;
				}
			}
*/

			if(!empty($data['appeals'][$this->session->view_appealse])){
				$arr = [];
				foreach($data['appeals'][$this->session->view_appealse] as $key => $appeals) {
					$row = [];
					foreach($appeals as $value) $row[] = empty($value) ? '' : $value;
					$arr[] = $row;
				}
				$data_tabel = array_slice(  $arr, 0, $length);
				//$data_tabel = $arr;
			}

		// Отправка сформированных дынных клиенту
			if(!empty($data_tabel)){
				$dataSet = array(
					'draw' => $draw,
					'recordsTotal' => $recordsTotal,
					'recordsFiltered' => $recordsFiltered,
					'data' => $data_tabel
				);
			}
			else{
				$dataSet = array(
					'draw' => $draw,
					'recordsTotal' => $recordsTotal,
					'recordsFiltered' => $recordsFiltered,
					'data' => ''
				);
			}
			echo json_encode($dataSet);

		
		//file_put_contents('log_time.txt',  'server_tabel count ft-st='.($ft-$st). PHP_EOL, FILE_APPEND);

	}
	public function get_excel(){
		// ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
		// ini_set('sqlsrv.ClientBufferMaxKBSize','524288'); // Setting to 512M
		// ini_set('pdo_sqlsrv.client_buffer_max_kb_size','524288'); // Setting to 512M - for pdo_sqlsrv
	/*
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	*/

		$MAX_RECORDS = 3000;
		$filter_bell = $this->input->get('filter_bell');
		$addParam = [ 
			'filter_bell' => $filter_bell,
			'requeststatus' => $this->session->view_appealse,
			'forExcel' => true
		];
		// add data from config file	                  
	    if ( isset( $addParam['filter_bell'] ) ) { 
	    	$this->config->load('main_config');
	    	$addParam['bell_count'] = $this->config->item('bell_count');
	    	if ( !isset($addParam['bell_count']) ) $addParam['bell_count'] = 1;
	    }


		$head_old = ["Номер звернення", "Канал надходження", "Дата/час створення", "Клієнт", "Тип звернення", "Розділ звернення", "Тема звернення", "Ресторан", "Страва", "Дата/час інцидента", "Сума чека", "Номер чека / Номер замовлення", "Тип стороннього включення", "ПІБ співробітника, посада","Що було не прибрано/брудне?", "Суть звернення", "Відповідальний", "Статус звернення", "Годин в роботі", "Допомога PR-відділу чи юриста", "Чи задоволений клієнт зворотним зв'язком", "Коментарі клієнта по зворотному зв'язку", "Опрацьовано вчасно", "Коментарі ресторану", "Додатки", "Дата/час створення (Відповіді)", "Текст відповіді", "Текст відповіді з доповненням/корекцією","Код компенсації", "Дата створення", "Строк дії", "Страва", "Ресторан, який створив Компенсацію", "Ресторан, який погасив Компенсацію", "Response ID", "OSAT", "Додатково", "Voucher Code", "POS", "Manual POS category"];
		$head = ["Номер звернення" => "TicketNumber", 
		          "Канал надходження" => "new_requestchannel", 
				  "Дата/час створення" => "CreatedOn", 
				  "Клієнт" => "CustomerName",
				  "Клієнт email" => "CustomerEmail", 
				  "Клієнт телефон" => "CustomerPhone",  
				  "Тип звернення" => "new_requesttypeName", 
				  "Розділ звернення" => "new_requestpartName", 
				  "Тема звернення" => "new_requestthemeName", 
				  "Ресторан" => "new_restaurantName", 
				  "Страва" => "new_foodName", 
				  "Дата/час інцидента" => "new_incidentdate",
				  "Сумма чека" => "new_summ",
				  "Номер чека / Номер замовлення" => "new_ordernumber", 
				  "Тип стороннього включення" => "new_inclusiontype",
				  "ПІБ співробітника, посада" => "new_employee",
				  "Що було не прибрано/брудне?" => "new_object",
				  "Суть звернення" => "Description",
				  "Відповідальний" => "OwnerIdName",
				  "Статус звернення" => "new_requeststatus",
				  "Годин в роботі" => "new_worktime",
				  "Допомога PR-відділу чи юриста" => "new_prorjurisconsult", 
				  "Чи задоволений клієнт зворотним зв'язком" => "new_satisfied", 
				  "Коментарі клієнта по зворотному зв'язку" => "new_clientcomment",
				  "Опрацьовано вчасно" => "new_intime",
				  "Коментарі ресторану" => "new_notes",
				  "Додатки" => "",
				  "Дата/час створення (Відповіді)" => "CreateFeedback", 
				  "Текст відповіді" => "new_feedbacktext",
				  "Текст відповіді з доповненням/корекцією" => "new_feedbacktextcorrect",
				  "Код компенсації" => "new_name",
				  "Дата створення" => "dateCompensation",
				  "Строк дії" => "new_enddate",
				  "Страва" => "food",
				  "Ресторан, який створив Компенсацію" => "new_createdbyName", 
				  "Ресторан, який погасив Компенсацію" => "new_usedbyName",
				  "Response ID" => "new_ResponseID", 
				  "OSAT" => "new_OSAT",
				  "Додатково" => "new_additionally",
				  "Voucher Code" => "new_VoucherCode",
				  "POS" => "new_POS",
				  "Manual POS category" => "new_ManualPOScategory"
				];
		require_once __DIR__.'/../../Classes/PHPExcel.php';
		require_once __DIR__.'/../../Classes/PHPExcel/Writer/Excel5.php';
		$document = new \PHPExcel();
		$sheet = $document->setActiveSheetIndex(0);//Выбираем первый лист в документе

		// Получение всех обращений
			$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
			$app = new Appeals();
/*
			if(!empty($this->session->date_start) && !empty($this->session->date_end)){
				$data['appeals'] = $app->getListOfAppeals($user, date("Ymd H:i:s", strtotime($this->session->date_start)), date("Ymd H:i:s", strtotime($this->session->date_end)));
				$data['data_start'] = date("d-m-Y", strtotime($this->session->date_start));
				$data['data_end'] = date("d-m-Y", strtotime($this->session->date_end));
			}
			else{
				$data['appeals'] = $app->getListOfAppeals($user);
				$data['data_start'] = date('01-m-Y');
				$data['data_end'] = date('t-m-Y');
			}
*/
			if(!empty($this->session->date_start) && !empty($this->session->date_end)) {
				$d1 = date("Ymd H:i:s", strtotime($this->session->date_start));
				$d2 = date("Ymd H:i:s", strtotime($this->session->date_end));
			} else {
				$d1 = -1;
				$d2 = -1;
			}

			$appeals_count = $data['appeals_count'] = $app->getListOfAppeals_count($user, $d1, $d2, $addParam);
			// echo json_encode($appeals_count).'<br>';
			$record_count = $appeals_count[ $this->session->view_appealse ];
			$interation_count = intdiv($record_count, $MAX_RECORDS)+($record_count%$MAX_RECORDS>0?1:0);
			// echo 'interation_count='.$interation_count.'<br>';
			$row = 2;
			for($i=0;$i<$interation_count;$i++){
				$addParam['offset'] = $i*$MAX_RECORDS;
				$addParam['length'] = $MAX_RECORDS;
				$appeals = $app->getListOfAppeals($user, $d1, $d2, $addParam);
				// echo 'int '.$i.' count ='.count($data).'<br>';

				// print to excel
				
				foreach($appeals[ $this->session->view_appealse ] as $row_value){
					$col = 0;
					if ($row_value['TicketNumber'] == 'ОБР-24524-D3Z9F7') {
						//var_dump($row_value);
						//break;
					}
					foreach($head as $col_name => $sql_name) {
						$col++;
						if ($sql_name == '') continue;
						$value = isset($row_value[ $sql_name ]) ? $row_value[ $sql_name ] : null;
						if (empty($value)) continue;
						if ($sql_name == 'Description' || $sql_name == 'new_additionally' ||
							$sql_name == 'new_notes' || $sql_name == 'new_feedbacktext' || 
							$sql_name == 'CustomerName' ) 
						{
							$value = mb_eregi_replace("[^a-zа-яёЁЇїІіЄєҐґ0-9\-\.\?\!\)\(\,: ]", '', $value);
						}
						if ( mb_substr( $value, 0, 1)  == '=' ) {
							$value = '_'.$value;
						}
						$sheet->setCellValueByColumnAndRow($col-1, $row, $value);
					} // foreach col

					$row++;
				} // foreach row


			    //var_dump( $appeals[ $this->session->view_appealse ] );
				//return;
			} // for
			// header
			$col = 0;
			foreach($head as $col_name => $sql_name){
				$sheet->setCellValueByColumnAndRow($col++, 1, $col_name);
			}

/*
			if(!empty($data['appeals'])){
				foreach ($data['appeals'] as $key1 => $value1){
					foreach ($data['appeals'][$key1] as $key2 => $value2)
					{
						$data['appeals']['all'][] = $value2;
					}
				}
			}*/

/*
			if(!empty($data['appeals'][$this->session->view_appealse])){
				foreach($data['appeals'][$this->session->view_appealse] as $key => $value){
					unset($data['appeals'][$this->session->view_appealse][$key]['IncidentId']);
					$i = 0;
					foreach($data['appeals'][$this->session->view_appealse][$key] as $key_appeals => $appeals){
						$sheet->setCellValueByColumnAndRow($i, $key+2, mb_eregi_replace("[^a-zа-яёЁЇїІіЄєҐґ0-9 ]", '', $appeals));
						$i++;
					}
				}
			}
*/
			
		header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=Система зворотного зв'язку 'Відгук'.xls");

		$objWriter = new PHPExcel_Writer_Excel5($document);
		$objWriter->save('php://output');
		//$objWriter->save('test.xls');
		exit;
	}
}
?>
