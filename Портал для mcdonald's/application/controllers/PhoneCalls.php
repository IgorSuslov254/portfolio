<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class PhoneCalls extends CI_Controller
{
	public function index(){
		$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
		$calls = new Calls();

		if(!isset($_POST['date_start']) && !isset($_POST['date_end'])){
			$phone_calls = $calls->getCallsList($user);
			$data_start = date('01-m-Y'); 
			$data_end = date('t-m-Y');
		}
		else{
			$phone_calls = $calls->getCallsList($user, date("Ymd H:i:s", strtotime($_POST['date_start'])), date("Ymd H:i:s", strtotime($_POST['date_end'])));
			$data_start = date("d-m-Y", strtotime($_POST['date_start'])); 
			$data_end = date("d-m-Y", strtotime($_POST['date_end']));
		}

		if($phone_calls){
			$template = array('table_open' => '<div class="container mt-5"><div class="row"><table class="display table_phone_calls">', 'table_close' => '</table></div></div>');
			$this->table->set_template($template);
			$this->table->set_heading('Call ID', 'id', 'Номер АОН', 'Клієнт', 'Тематика дзвінка', 'Ресторан', 'Звернення','Посилання на запис', 'Дата/час створення', 'Тривалість (секунд)');
			foreach ($phone_calls as $key => $phone) {
				$this->table->add_row($phone);
			}
			echo $this->breadcrumb().'<h2 class="text-center mt-5">Показано дзвінки за період з '.$data_start.' до '.$data_end.'</h2>'.$this->table->generate();
		}
		else{
			echo $this->breadcrumb().'<h2 class="text-center mt-5">В період з '.$data_start.' до '.$data_end.' відсутні дані</h2>';
		}
	}
	private function breadcrumb(){
		$breadcrumb = '
			<section id="head_mein">
				<div class="container-fluid">
					<div class="container">
						<div class="row">
							<div class="col-lg-6 text-center text-lg-left">
								<h1>Дзвінки</h1>
								<h2>Ви можете переглянути дзвінки за будь-який період</h2>
							</div>
							<div class="col-lg-6 text-center text-sm-left mt-3 mt-lg-0">
								<form id="view_date_picker">
									<div class="container-fluid">
										<div class="row">
											<div class="col-sm-4">
												<label id="date_start_label" for="date_start" class="float-left"><h3>Від</h3></label>
												<input type="text" id="date_start" class="form-control datepicker" name="date_start">
											</div>
											<div class="col-sm-4 mt-3 mt-sm-0">
												<label id="date_end_label" for="date_end" class="float-left"><h3>До</h3></label>
												<input type="text" id="date_end" class="form-control datepicker" name="date_end">
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
}
?>
