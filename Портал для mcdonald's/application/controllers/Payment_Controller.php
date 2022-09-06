<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_Controller extends CI_Controller
{
	public function index(){
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$pay = new Payment;

		if(!isset($_POST['date_start']) && !isset($_POST['date_end'])){
			$payment = $pay->getPaymentList($user);

			$data_start = date("01-m-Y", strtotime("-1 month"));
			$data_end = date('t-m-Y');
		}
		else{
			$payment = $pay->getPaymentList($user, date("Ymd H:i:s", strtotime($_POST['date_start'])), date("Ymd H:i:s", strtotime($_POST['date_end'])));

			$data_start = date("d-m-Y", strtotime($_POST['date_start']));
			$data_end = date("d-m-Y", strtotime($_POST['date_end']));
		}

		$key_status = array('Active','Hold','Used');
		foreach ($key_status as $key => $value_key_status) {
			if(!empty($payment[$value_key_status])){
				$_SESSION['payment_'.strtolower($value_key_status)] = count($payment[$value_key_status]);
			}
			else{
				$_SESSION['payment_'.strtolower($value_key_status)] = 0;
			}
		}

		if(isset($_POST['status'])){
			$status = $_POST['status'];
		}
		else{
			$status = 'Active';
		}
		$data['status'] = $status;

		$template = array('table_open' => '<div class="container mt-5"><div class="row"><table id="payment_table_view" class="display table_payment">', 'table_close' => '</table></div></div>');
		$this->table->set_template($template);
		$this->table->set_heading('Код компенсації', 'id', 'Ім`я клієнта', 'Дата створення', 'Строк дії', 'Страва', 'Ресторан, який створив Компенсацію','Ресторан, який погасив Компенсацію', 'Суть звернення');
		if(!empty($payment[$status])){
			foreach ($payment[$status] as $key => $payment){
				$this->table->add_row($payment);
			}
		}
		echo $this->breadcrumb($data).'<h2 class="text-center mt-5">Показано компенсацii за період з '.$data_start.' до '.$data_end.'</h2>'.$this->table->generate();
	}
	public function search_food()
	{
		$payment = new Payment;
		$result = $payment->getFood($_POST['food']);

		$view = '';
		foreach ($result as $key => $result_value) {
			$view .= '<div id="'.$result_value['new_foodId'].'"><h3>'.$result_value['new_name'].'</h3></div>';
		}

		echo $view;
	}
	public function create_payment()
	{
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$payment = new Payment();
		if($_SESSION['count'] == 0){
			$sucsess = $payment->createPayment($user,$_POST);
			if($sucsess == 1){
				$_SESSION['count']++;
				echo $_SESSION['count'];
			}
			else{
				echo ' Відносно даного звернення компенсація вже створена';
			}
		}
	}
	public function change_status_hold(){
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$payment = new Payment();

		$id['compensation'] = $_POST['id'];
		$id['rest'] = $_SESSION['SystemUserId'];

		echo $payment->toHold($user,$id);
	}
	public function change_status_active(){
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$payment = new Payment();

		$id['compensation'] = $_POST['id'];

		echo $payment->toActive($user,$id);
	}
	public function planCheck()
	{
		$this->CI =& get_instance();
		$this->CI->load->model('CRM_model');
		$this->CI->load->model('Payment_model');
		$res = $this->CI->Payment_model->getPaymentForCheck();

		foreach ($res as $key => $value)
		{
			$id['compensation'] = $res[$key]['new_compensationId'];

			if($res[$key]['new_status'] == "100000000")							//если активная
			{
				$this->CI->CRM_model->changeStatus($id,'100000003');			//меняем статаус на Deactive
			}
			elseif ($res[$key]['new_status'] == "100000001")					//если в ожидании
			{
				$this->CI->CRM_model->changeStatus($id,'100000002');			//меняем статаус на Used
			}
			sleep(1);
		}
	}
	private function breadcrumb($data = NULL){
		$breadcrumb = '
			<section id="head_mein">
				<div class="container-fluid">
					<div class="container">
						<div class="row">
							<div class="col-lg-6 text-center text-lg-left">
								<h1>Компенсацii</h1>
								<h2>Ви можете переглянути компенсацii за будь-який період</h2>
							</div>
							<div class="col-lg-6 text-center text-sm-left mt-3 mt-lg-0">
								<form id="view_date_picker">
									<div class="container-fluid">
										<div class="row">
											<div class="col-sm-4">
												<label id="date_start_label" for="date_start" class="float-left"><h3>Від</h3></label>
												<input type="text" id="date_start" class="form-control datepicker" name="date_start">
												<input type="hidden" id="role_payment_table" value="'.$_SESSION['Role_Name'].'">
											</div>
											<div class="col-sm-4 mt-3 mt-sm-0">
												<label id="date_end_label" for="date_end" class="float-left"><h3>До</h3></label>
												<input type="text" id="date_end" class="form-control datepicker" name="date_end">
												<input type="hidden" id="status_payment_table" value="'.$data['status'].'">
											</div>
											<div class="col-sm-4 my-3 m-sm-auto">
												<button type="submit" class="btn button_yellow btn-rounded"><h2>Показати</h2></button>
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

	public function payment_ative(){
		echo $_SESSION['payment_active'];
	}
	public function payment_hold(){
		echo $_SESSION['payment_hold'];
	}
	public function payment_used(){
		echo $_SESSION['payment_used'];
	}
}
?>
