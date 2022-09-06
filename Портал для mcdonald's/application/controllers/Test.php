<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Test extends CI_Controller {
	public function index(){
		$this->load->view('head');
		$this->load->view('test_view');
		$this->load->view('footer');
	}
	public function obr(){
		// Получение всех обращений
			$user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
			$app = new Appeals();
			$data['appeals'] = $app->getListOfAppeals($user);

		// функция поиска
			function preg_grep_keys_values($pattern, $input, $flags = 0){
				return array_merge(array_intersect_key($input, array_flip(preg_grep($pattern, array_keys($input), $flags))),preg_grep($pattern, $input, $flags));
			}

		// получение параметров запрса от datatabel
			$recordsTotal = count($data['appeals']['new']);
			$draw 	= $_POST['draw'];
			$start 	= $_POST['start'];
			$length = $_POST['length'];
			$stop 	= $start + $length;

		if(isset($_POST['search']['value']) && strlen($_POST['search']['value']) > 2){
			$search = '~'.$_POST['search']['value'].'~i';
			foreach($data['appeals']['new'] as $key => $appeals){
				if(empty(preg_grep_keys_values($search, $data['appeals']['new'][$key]))){
					unset($data['appeals']['new'][$key]);
				}
			}
			$recordsFiltered = count($data['appeals']['new']);
		}
		else{
			$recordsFiltered = $recordsTotal;
		}

		// формирование массива данных
			$i = 0;
			foreach($data['appeals']['new'] as $key => $appeals){
				if($i >= $start && $i < $stop){
					$j = 0;
					foreach ($data['appeals']['new'][$key] as $key_new => $new){
						if($start == 0){
							$data_tabel[$i][$j] = $data['appeals']['new'][$key][$key_new];
						}
						else{
							$data_tabel[$i-$length][$j] = $data['appeals']['new'][$key][$key_new];
						}
						$j++;
					}
				}
				$i++;
			}

		// Отправка сформированных дынных клиенту
			$dataSet = array(
				'draw' => $draw,
				'recordsTotal' => $recordsTotal,
				'recordsFiltered' => $recordsFiltered,
				'data' => $data_tabel
			);
			echo json_encode($dataSet);
	}
}
?>
