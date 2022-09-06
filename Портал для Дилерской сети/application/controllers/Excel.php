<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Excel extends Admin_Controller{
	public function index(){
		$emails_send = $this->get_email();

		if(isset($emails_send['PrimaryContact'])){
			$this->data['PrimaryContact'] = $emails_send['PrimaryContact'];
		}
		else{
			$this->data['PrimaryContact'] = $_SESSION['email'];
		}

		$this->template->admin_render('excel_view', $this->data);
	}
	public function export_excel(){
		$responsible_for_quality_all = 'alexander.dryuk@mpsa.com';

		if(!empty($_FILES['exel'])){
			$file = $_FILES['exel'];

			//Убираем расширение из названия файла
				$Original_name_exel = $file['name'];
				$change_this = array(".xlsx", ".xls");
				$on_this = array("", "");

			// собираем путь до нового файла
			// в качестве имени оставляем исходное файла имя во время загрузки в браузере
				$srcFileName = str_replace($change_this, $on_this, $Original_name_exel);
				$newFilePath = __DIR__.'/../../'.$srcFileName.'.xlsx';

			if (!move_uploaded_file($file['tmp_name'], $newFilePath)) {
				$data['massege_view'] = 'Помилка при завантаженні файлу, зверніться в службу підтримки';
			} else {
				$data['massege_view'] = 'файл успішно завантажений';
			}

			//Загрузка библиотеки PHPExel получение данных с Exel файла
				require_once __DIR__.'/../../Classes/PHPExcel.php';
				$excel = PHPExcel_IOFactory::load(__DIR__.'/../../'.$srcFileName.'.xlsx');
				Foreach($excel ->getWorksheetIterator() as $worksheet){
				$lists[] = $worksheet->toArray();
				}
				unlink($newFilePath);//удалить файл Exel

			if(count($lists[0]) > 1){
				$errors_file = $this->errors_file($lists);//Проверка файла на шаблон и наличия первого порядкового номера
				if($errors_file[0] == 11){
					//Удаление ненужных столбцов и строк
						$count_line_exel = count($lists[0]);//Количество строк в таблице
						for($i = 0;$i < $count_line_exel;$i++){
							if(!isset($lists[0][$i][1]) && !isset($lists[0][$i+1][1])){
								$flag_deleting_unnecessary = 1;
							}
							if(@$flag_deleting_unnecessary == 1){
								unset($lists[0][$i]);
							}
							for($j = 26; $j < 1000; $j++){
								unset($lists[0][$i][$j]);
							}
						}
						$count_line_exel = count($lists[0]);//Количество строк в таблице без лишних данных

					//Создание массива ошибок для подсвечивания красным текстом
						$errors['id']					= $this->check_id($lists, $count_line_exel, $errors_file);						//Проверка поля ID
						$errors['phone']				= $this->check_phone($lists, $count_line_exel, $errors_file);					//Проверка полей phone
						$errors['email']				= $this->check_email($lists, $count_line_exel, $errors_file);					//Проверка поля email
						$errors['date']					= $this->chek_date($lists, $count_line_exel, $errors_file);						//Проверка поля return_date
						$errors['vin']					= $this->check_vin($lists, $count_line_exel, $errors_file);						//Проверка поля vin
						$errors['customer_type']		= $this->check_customer_type($lists, $count_line_exel, $errors_file);			//Проверка поля customer_type
						$errors['rrdi']					= $this->check_rrdi($lists, $count_line_exel, $errors_file);					//Проверка поля rrdi
						$errors['agree']				= $this->check_agree($lists, $count_line_exel, $errors_file);					//Проверка поля agree
						$errors['kind_work']			= $this->check_kind_work($lists, $count_line_exel, $errors_file);				//Проверка поля kind_work
						$errors['source_client_record']	= $this->check_source_client_record($lists, $count_line_exel, $errors_file);	//Проверка поля client_record
						$errors['gender']				= $this->gender($lists, $count_line_exel, $errors_file);						//Проверка пола
						$errors['check_mileage']		= $this->check_mileage($lists, $count_line_exel, $errors_file);						//Проверка пола


					//Создание масиива дублей для подсвечиваения желтым цветом
						$text_doubles = ' Телефон повторюється в рядках: ';
						$column = 9;//Переменная колонки, по которой проверяются дубли
						$doubles['phone'] = $this->doubles($lists, $column, $text_doubles, $errors_file);
						$text_doubles = ' E-mail повторюється в рядках: ';
						$column = 10;//Переменная колонки, по которой проверяются дубли
						$doubles['email'] = $this->doubles($lists, $column, $text_doubles, $errors_file);
						$text_doubles = ' VIN-код повторюється в рядках: ';
						$column = 15;//Переменная колонки, по которой проверяются дубли
						$doubles_no_highlighted['vin'] = $this->doubles($lists, $column, $text_doubles, $errors_file);

					//Создание нового Exel файла с указанием ошибок, анализа и комментариев и его отправка
						$this->exel_new($lists, $count_line_exel, $srcFileName, $errors, $doubles, $doubles_no_highlighted, $errors_file, $responsible_for_quality_all);
				}
				else{
					$this->send_template($responsible_for_quality_all);
				}
			}
			else{
				$this->send_message_errors($responsible_for_quality_all);
			}
		}
		else{
			$this->data['massege_view'] = 'Помилка! Exel файл не завантажено. Спробуйте ще раз або зверніться в службу підтримки.';
			$this->template->admin_render('total_excel', $this->data);
		}
	}
	private function errors_file($lists){
		$line_list_exel_new = 0;
		$first_if = 0;
		$second_if = 0;
		$third_if = 0;
		foreach($lists[0] as $lists_value){
			$line_list_exel_new++;
			if(($line_list_exel_new == 1 && $first_if == 0) || ($line_list_exel_new == 2 && $first_if == 0)){
				for($i = 0;$i < count($lists_value);$i++){
					$healthy = array(" ", "-", '"', "'", ",", "/", "\n");
					$yummy   = array("", "", "", "", "", "", "");
					$newphrase = str_replace($healthy, $yummy, $lists_value[$i]);
					$header_line[$i] = mb_strtolower($newphrase);
				}
				if($header_line[0] == '№' && $header_line[1] == 'фамилияклиента' && $header_line[2] == 'имяотчество' && $header_line[3] == 'пол' && $header_line[4] == 'улица№домаиквартиры' && $header_line[5] == 'почтовыйкод' && $header_line[6] == 'городпгтсело' && $header_line[7] == 'районобласть' && $header_line[8] == 'фиомастераприемщика' && $header_line[9] == 'номертелефонастационарный(номертелефонауказываетсявмеждународномформате)' && $header_line[10] == 'номертелефонамобильный(номертелефонауказываетсявмеждународномформате)' && $header_line[11] == 'email' && $header_line[12] == 'датаоткрытиязаказнаряда' && $header_line[13] == 'датазакрытиязаказнаряда' && $header_line[14] == 'номерзаказнаряда' && $header_line[15] == 'модельавтомобиля' && $header_line[16] == 'винкод' && $header_line[17] == 'пробегкм' && $header_line[18] == 'номерводительскогоудостоверения(опционально)' && $header_line[19] == 'датапервогозаезданасто(опционально)' && $header_line[20] == 'типклиента(физиескоелицоюридическоелицо)' && $header_line[21] == 'названиесервисногоцентра' && $header_line[22] == 'коддилера' && $header_line[23] == 'согласиеклиентанаиспользованиеегоперсональныхданных(данет)' && $header_line[24] == 'видработ' && $header_line[25] == 'источникзаписиклиента'){
					$flag_first = 1;
					$first_if = 1;
					$number_title = $line_list_exel_new;
				}
				else{
					$flag_first = 0;
				}
			}
			if(($line_list_exel_new == 2 && $second_if == 0) || ($line_list_exel_new == 3 && $second_if == 0) || ($line_list_exel_new == 4 && $second_if == 0)){
				if($lists_value[0] == 1){
					$flag_second = 1;
					$second_if = 1;
					$number_begine = $line_list_exel_new;
				}
				else{
					$flag_second = 0;
				}
			}
			// if(($line_list_exel_new == 1 && $third_if == 0) || ($line_list_exel_new == 2 && $third_if == 0)){
			// 	if($lists_value[0] == 'ID'){
			// 		$third_if = 1;
			// 		$number_title_EN = $line_list_exel_new;
			// 	}
			// }
		}
		$return = array(@$flag_first.@$flag_second,@$number_begine, @$number_title);
		return $return;
	}
	private function check_id($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$serial_number	= 0;
		$array_key		= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					$serial_number++;
					if($serial_number != $lists_value_value[0]){
						$id[$array_key]['x'] = 0;
						$id[$array_key]['y'] = $line_this_time;
						$id[$array_key]['message'] = ' Відсутня або неправильно проставлена нумерація;';
						$array_key++;
					}
				}
			}
		}
		return @$id;
	}
	private function check_phone($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$array_key			= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(!preg_match("/[0-9]{12}/", $lists_value_value[9]) && !preg_match("/[0-9]{12}/", $lists_value_value[10])){
						$phone[$array_key]['x'] = 10;
						$phone[$array_key]['y'] = $line_this_time;
						$phone[$array_key]['message'] = ' Некоректно вказано телефон;';
						$array_key++;
					}
				}
			}
		}
		return @$phone;
	}
	private function check_email($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$array_key			= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(!preg_match("/@/", $lists_value_value[11])){
						$email[$array_key]['x'] = 11;
						$email[$array_key]['y'] = $line_this_time;
						$email[$array_key]['message'] = ' Неправильно або не зазначена електронна пошта;';
						$array_key++;
					}
				}
			}
		}
		return @$email;
	}
	private function chek_date($lists, $count_line_exel, $errors_file){
		$line_this_time = 0;
		$array_key = 0;
		foreach($lists[0] as $lists_value){
			$line_this_time++;
			if($line_this_time > $errors_file[1]-1){
				if(!preg_match("/(0?[1-9]|[12][0-9]|3[01])[\/\-\.](0?[1-9]|1[012])/", $lists_value[13])){
					$date[$array_key]['x'] = 13;
					$date[$array_key]['y'] = $line_this_time;
					$date[$array_key]['message'] = ' Дата видачі авто не відповідає періоду або не містить даних;';
					$array_key++;
				}
				else{
					$dateComps = date_parse($lists_value[13]);
					$year_exel = $dateComps['year'];
					$month_exel = $dateComps['month'];
					$day_exel = $dateComps['day'];
					if(date('d') < 16){
						$month = date('m') - 1;
						$year = date('Y');
						if($month == 0){
							$month = 12;
							$year = date('Y') - 1;
						}
						if($month_exel == $month && $year_exel == $year){}
						else{
							$date[$array_key]['x'] = 13;
							$date[$array_key]['y'] = $line_this_time;
							$date[$array_key]['message'] = ' Дата видачі авто не відповідає періоду або не містить даних;';
							$array_key++;
						}
					}
					else{
						if($month_exel == date('m') && $year_exel == date('Y')){}
						else{
							$date[$array_key]['x'] = 13;
							$date[$array_key]['y'] = $line_this_time;
							$date[$array_key]['message'] = ' Дата видачі авто не відповідає періоду або не містить даних;';
							$array_key++;
						}
					}
				}
			}
		}
		return @$date;
	}
	private function check_vin($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$array_key		= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(!preg_match("/[A-Za-z0-9]{17}/", $lists_value_value[16]) || iconv_strlen($lists_value_value[16]) != 17 || str_split($lists_value_value[16])[0] == '0'){
						$vin[$array_key]['x'] = 16;
						$vin[$array_key]['y'] = $line_this_time;
						$vin[$array_key]['message'] = ' Неправильно або не вказано vin код;';
						$array_key++;
					}
				}
			}
		}
		return @$vin;
	}
	private function check_mileage($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$array_key		= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(empty($lists_value_value[17])){
						$mileage[$array_key]['x'] = 17;
						$mileage[$array_key]['y'] = $line_this_time;
						$mileage[$array_key]['message'] = ' Не вказаний пробіг;';
						$array_key++;
					}
				}
			}
		}
		return @$mileage;
	}
	private function check_customer_type($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$array_key		= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(!preg_match("/^фл$|^юл$/", mb_strtolower($lists_value_value[20]))){
						$customer_type[$array_key]['x'] = 20;
						$customer_type[$array_key]['y'] = $line_this_time;
						$customer_type[$array_key]['message'] = ' Знайдено записи без типу клієнта (значення повинні бути: ФЛ або ЮЛ);';
						$array_key++;
					}
				}
			}
		}
		return @$customer_type;
	}
	private function gender($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$array_key		= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(preg_match("/^фл$/", mb_strtolower($lists_value_value[20]))){
						if(!preg_match("/^m$|^f$/", mb_strtolower($lists_value_value[3]))){
							$gender[$array_key]['x'] = 3;
							$gender[$array_key]['y'] = $line_this_time;
							$gender[$array_key]['message'] = ' Не вказано стать;';
							$array_key++;
						}
					}
				}
			}
		}
		return @$gender;
	}
	private function check_rrdi($lists, $count_line_exel, $errors_file){
		$user_rrdi = $this->rrdi();
		$line_this_time	= 0;
		$array_key		= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(!preg_match("/[A-Za-z0-9]{7}/", $lists_value_value[22]) || $user_rrdi != $lists_value_value[22]){
						$rrdi[$array_key]['x'] = 22;
						$rrdi[$array_key]['y'] = $line_this_time;
						$rrdi[$array_key]['message'] = ' Неправильно або не вказано код дилера;';
						$array_key++;
					}
				}
			}
		}
		return @$rrdi;
	}
	private function check_agree($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$array_key		= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(!preg_match("/(^ДА$|^Да$|^да$|^НЕТ$|^Нет$|^нет$)/", $lists_value_value[23])){
						$agree[$array_key]['x'] = 23;
						$agree[$array_key]['y'] = $line_this_time;
						$agree[$array_key]['message'] = ' Неправильно або не вказано згоду клієнта;';
						$array_key++;
					}
				}
			}
		}
		return @$agree;
	}
	private function check_kind_work($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$array_key		= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(!isset($lists_value_value[24])){
						$check_kind_work[$array_key]['x'] = 24;
						$check_kind_work[$array_key]['y'] = $line_this_time;
						$check_kind_work[$array_key]['message'] = ' Не вказаний вид роботи;';
						$array_key++;
					}
				}
			}
		}
		return @$check_kind_work;
	}
	private function check_source_client_record($lists, $count_line_exel, $errors_file){
		$line_this_time	= 0;
		$array_key		= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(!isset($lists_value_value[25])){
						$check_kind_work[$array_key]['x'] = 25;
						$check_kind_work[$array_key]['y'] = $line_this_time;
						$check_kind_work[$array_key]['message'] = ' Не вказано джерело запису клієнта;';
						$array_key++;
					}
				}
			}
		}
		return @$check_kind_work;
	}
	private function doubles($lists, $column, $text_doubles, $errors_file){
		$line_this_time	= 0;
		$serial_number	= 0;
		$doubles_key	= 0;

		if(isset($phoneno)){
			unset($phoneno);
		}
		if(isset($doubles_phone_result)){
			unset($doubles_phone_result);
		}

		//Формиравание массива телефонных номеров
			foreach($lists[0] as $lists_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					$phoneno[$serial_number] = $lists_value[$column];
					$serial_number++;
				}
			}

		//формирование массива дублей
			for($i = 0; $i < count($phoneno); $i++){
				if(count(array_keys($phoneno, $phoneno[$i])) > 1){
					if($phoneno[$i] != NULL){
						if(isset($phoneno[$i])){
							$doubles[$doubles_key] = array_keys($phoneno, $phoneno[$i]);
							$doubles_key++;
						}
					}
				}
			}

		//Перевод массива дублей в строки для удаления дублей в массиве дублей
			if(isset($doubles)){
				for($i = 0;$i < count($doubles); $i++){
					$value = 0;
					foreach($doubles[$i] as $doubles_value){
						$value .= $doubles_value;
					}
					$doubles_string[$i] = $value;
				}
				//удаления дублей в массиве дублей
					$doubles_string = array_unique($doubles_string);
					$count = 0;
					foreach($doubles_string as $key_doubles_string => $doubles_string_value){
						$doubles_phone[$count] = $doubles[$key_doubles_string];
						$count++;
					}
			}

		//Создание массива дублей с координатами
			if(isset($doubles_phone)){
				$count = 0;
				for($i = 0; $i < count($doubles_phone); $i++){
					for($j = 0;$j < count($doubles_phone[$i]); $j++){
						if($j == 0){
							$message[$i] = $text_doubles;
						}
						else{
							$message[$i] .= ',';
						}
						$message[$i] .= $doubles_phone[$i][$j]+1;
					}
					foreach($doubles_phone[$i] as $doubles_phone_value){
						$doubles_phone_result[$count]['x'] = $column;
						$doubles_phone_result[$count]['y'] = $doubles_phone_value+$errors_file[1];
						$doubles_phone_result[$count]['message'] = $message[$i];
						$count++;
					}
				}
				return $doubles_phone_result;
			}
	}
	private function exel_new($lists, $count_line_exel, $srcFileName, $errors, $doubles, $doubles_no_highlighted, $errors_file, $responsible_for_quality_all){
		require_once __DIR__.'/../../Classes/PHPExcel/Writer/Excel5.php';
		$document = new \PHPExcel();
		$sheet = $document->setActiveSheetIndex(0);//Выбираем первый лист в документе
		$startLine = 1; // Начальная координата y

		//Задание ширины столбцам
			foreach(range('A','X') as $width_columns) {
				$sheet->getColumnDimension($width_columns)->setWidth(20);
			}

		//Создание новой таблицы
			$green_color = 0;
			for($i = 0; $i < $count_line_exel; $i++){
				$j = $i+1;
				$sheet->getRowDimension($j)->setRowHeight(15);//задание высоты
				$currentColumn = 0;
				foreach ($lists[0][$i] as $lists_title){
					$sheet->setCellValueByColumnAndRow($currentColumn, $startLine, $lists_title);
					$sheet->getStyleByColumnAndRow($currentColumn, $startLine)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sheet->getStyleByColumnAndRow($currentColumn, $startLine)->getAlignment()->setWrapText(true);
					// if($i == $errors_file[3]-1){
					// 	$sheet->getStyleByColumnAndRow($currentColumn, $startLine)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('008000');
					// }
					if($i == $errors_file[2]-1){
						$sheet->getStyleByColumnAndRow($currentColumn, $startLine)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ffa500');
					}
					$sheet->getStyleByColumnAndRow($currentColumn, $startLine)->applyFromArray(array(
						'borders' => array(
							'bottom'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
							'right'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
							'top'		=> array('style' => PHPExcel_Style_Border::BORDER_THIN),
							'left'	=> array('style' => PHPExcel_Style_Border::BORDER_THIN)
						)
					));
					// Смещаемся вправо
					$currentColumn++;
				}
				$startLine++;
			}

		//Заливка красным цветом критически неправильных значений
			foreach($errors as $errors_value){
				if(is_array($errors_value)){
					foreach($errors_value as $errors_value_value){
						$sheet->getStyleByColumnAndRow($errors_value_value['x'], $errors_value_value['y'])->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ff0000');
						//формирование массива коминтареев по строчно
							if(!isset($errors_message[$errors_value_value['y']])){
								$errors_message[$errors_value_value['y']] = $errors_value_value['message'];
							}
							else{
								$errors_message[$errors_value_value['y']] .= $errors_value_value['message'];
							}
					}
				}
			}

		//Заливка жёлтым цветом не критических значений
			foreach($doubles as $doubles_value){
				if(is_array($doubles_value)){
					foreach($doubles_value as $doubles_value_value){
						$sheet->getStyleByColumnAndRow($doubles_value_value['x'], $doubles_value_value['y'])->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ffa500');
						//формирование массива коминтареев по строчно
							if(!isset($errors_message[$doubles_value_value['y']])){
								$errors_message[$doubles_value_value['y']] = $doubles_value_value['message'];
							}
							else{
								$errors_message[$doubles_value_value['y']] .= $doubles_value_value['message'];
							}
					}
				}
			}

		//Задание комментариев критических ошибок
			foreach($errors_message as $key_errors_message => $errors_message_value){
				$sheet->setCellValueByColumnAndRow(26, $key_errors_message, $errors_message_value);
				$sheet->getStyleByColumnAndRow(26, $key_errors_message)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			}

		//Создание таблицы анализ данных
			$eqs = $this->data_analysis($count_line_exel, $document, $sheet, $lists, $doubles_no_highlighted, $doubles, $errors_file);

		//сохранение новой таблицы
			$objWriter = \PHPExcel_IOFactory::createWriter($document, 'Excel5');
			$objWriter->save(__DIR__.'/../../'.$srcFileName.'.xls');

		//Отправка сообщение с вложенным файлом
			$this->send_message($srcFileName, $errors, $eqs, $lists, $errors_file, $responsible_for_quality_all);
	}
	private function data_analysis($count_line_exel, $document, $sheet, $lists, $doubles_no_highlighted, $doubles, $errors_file){
		//Анализ данных
			$count_success_vin 		= $this->count_success_vin($lists, $errors_file);//Анализ корректных vin_кодов
			$count_doubles_vin 		= $this->count_doubles_vin($doubles_no_highlighted, $errors_file);//Подсчёт количества дублей vin кодов
			$procent_correct_phone 	= $this->procent_correct_phone($lists, $doubles_no_highlighted, $errors_file);//Подсчет процента коректных телефонных номеров
			$procent_foreign		= $this->procent_foreign($lists, $doubles_no_highlighted, $errors_file);//Подсчет процента иностранных граждан
			$procent_suspicious		= $this->procent_suspicious($lists, $doubles_no_highlighted, $errors_file);//Подсчет процента подозрительных номеров
			$procent_correct_email	= $this->procent_correct_email($lists, $errors_file);//Подсчёт процента корректных элеткронных адресов
			$procent_consenting		= $this->procent_consenting($lists, $doubles_no_highlighted, $errors_file);//Подсчёт процента согласных на контакт
			$procent_legal_entity	= $this->procent_legal_entity($lists, $doubles_no_highlighted, $errors_file);//Подсчёт процента юридических лиц
			$procent_natural_person	= $this->procent_natural_person($lists, $doubles_no_highlighted, $errors_file);//Подсчёт процента физических лиц
			$procent_unique_phone	= $this->procent_unique_phone($lists, $doubles_no_highlighted, $doubles, $errors_file);//Посчёт процентка коректных уникальных номеров
			$procent_eqc			= $this->procent_eqc($lists, $doubles_no_highlighted, $doubles, $errors_file);//Корректность БД для исследования EQC, %
			$procent_net_eqc		= $this->procent_net_eqc($lists, $doubles_no_highlighted, $doubles, $errors_file);//Корректность БД для исследования NET EQC, %

			$data['procent_eqc'] = $procent_eqc;
			$data['procent_net_eqc'] = $procent_net_eqc;

		//Заполнение таблицы анализа
			for($i = 3;$i < 20;$i++){
				$count_line_exel_new = $count_line_exel+$i;
				for($j = 1;$j < 5;$j++){
					if($i == 3 && $j == 1){
						$text = 'Область даних для аналізу';
						$sheet->getStyle("B".$count_line_exel_new)->getFont()->setBold(true);
					}
					elseif($i == 3 && $j == 2){
						$text = 'Назва показника';
						$sheet->getStyle("C".$count_line_exel_new)->getFont()->setBold(true);
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 3 && $j == 3){
						$text = NULL;
					}
					elseif($i == 3 && $j == 4){
						$text = 'Показник';
						$sheet->getStyle("E".$count_line_exel_new)->getFont()->setBold(true);
					}
					elseif($i == 4 && $j == 1){
						$text = 'База за поточний період';
						$sheet->getStyle("B".$count_line_exel_new)->getFont()->setBold(true);
					}
					elseif($i == 4 && $j == 2){
						$text = 'Дата і час завантаження БД ';
						$sheet->getStyle("B".$count_line_exel_new)->getFont()->setBold(true);
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 4 && $j == 3){
						$text = NULL;
					}
					elseif($i == 4 && $j == 4){
						$text = date('d.m.Y H:i:s');
					}
					elseif($i == 5 && $j == 2){
						$text = 'К-ть завантажених рядків';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 5 && $j == 3){
						$text = NULL;
					}
					elseif($i == 5 && $j == 4){
						$text = $count_line_exel-$errors_file[1]+1;
					}
					elseif($i == 6 && $j == 2){
						$text = 'К-ть коректних VIN-кодів';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 6 && $j == 3){
						$text = NULL;
					}
					elseif($i == 6 && $j == 4){
						$text = $count_success_vin;
					}
					elseif($i == 7 && $j == 2){
						$text = 'К-ть дублів серед коректних VIN-кодів';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 7 && $j == 3){
						$text = NULL;
					}
					elseif($i == 7 && $j == 4){
						$text = $count_doubles_vin;
					}
					elseif($i == 8 && $j == 2){
						$text = '% Коректних телефонних номерів';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 8 && $j == 3){
						$text = NULL;
					}
					elseif($i == 8 && $j == 4){
						$text = $procent_correct_phone.'%';
					}
					elseif($i == 9 && $j == 2){
						$text = '% іноземних громадянин';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 9 && $j == 3){
						$text = NULL;
					}
					elseif($i == 9 && $j == 4){
						$text = $procent_foreign.'%';
					}
					elseif($i == 10 && $j == 2){
						$text = '% Підозрілих номерів';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 10 && $j == 3){
						$text = NULL;
					}
					elseif($i == 10 && $j == 4){
						$text = $procent_suspicious.'%';
					}
					elseif($i == 11 && $j == 2){
						$text = '% корректних електронних адрес';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 11 && $j == 3){
						$text = NULL;
					}
					elseif($i == 11 && $j == 4){
						$text = $procent_correct_email.'%';
					}
					elseif($i == 12 && $j == 2){
						$text = '% Згодних на контакт';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 12 && $j == 3){
						$text = NULL;
					}
					elseif($i == 12 && $j == 4){
						$text = $procent_consenting.'%';
					}
					elseif($i == 13 && $j == 2){
						$text = 'Юр. особи';
						$sheet->getStyle("C".$count_line_exel_new)->getFont()->setBold(true);
					}
					elseif($i == 13 && $j == 3){
						$text = '% Юр. осіб';
					}
					elseif($i == 13 && $j == 4){
						$text = $procent_legal_entity.'%';
					}
					elseif($i == 14 && $j == 2){
						$text = 'Фіз. особи';
						$sheet->getStyle("C".$count_line_exel_new)->getFont()->setBold(true);
					}
					elseif($i == 14 && $j == 3){
						$merge_end = $count_line_exel_new+3;
						$text = '% Фіз. особи';
						$sheet->mergeCells("C".$count_line_exel_new.":C".$merge_end);
					}
					elseif($i == 14 && $j == 4){
						$text = $procent_natural_person.'%';
					}
					elseif($i == 15 && $j == 2){
						$text = NULL;
					}
					elseif($i == 15 && $j == 3){
						$text = '% Коректних унікальних телефонних номерів';
					}
					elseif($i == 15 && $j == 4){
						$text = $procent_unique_phone.'%';
					}
					elseif($i == 16 && $j == 2){
						$text = NULL;
					}
					elseif($i == 16 && $j == 3){
						$text = 'Коректність БД для дослідження EQC,%';
					}
					elseif($i == 16 && $j == 4){
						$text = $procent_eqc.'%';
						if($procent_eqc < 80){
							$sheet->getStyleByColumnAndRow($j, $count_line_exel_new)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ff0000');
						}
						else{
							$sheet->getStyleByColumnAndRow($j, $count_line_exel_new)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('4caf50');
						}
					}
					elseif($i == 17 && $j == 2){
						$text = NULL;
					}
					elseif($i == 17 && $j == 3){
						$text = 'Коректність БД для дослідження NET EQC,%';
					}
					elseif($i == 17 && $j == 4){
						$text = $procent_net_eqc.'%';
						if($procent_net_eqc < 36){
							$sheet->getStyleByColumnAndRow($j, $count_line_exel_new)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ff0000');
						}
						elseif($procent_net_eqc < 61){
							$sheet->getStyleByColumnAndRow($j, $count_line_exel_new)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ffeb3b');
						}
						else{
							$sheet->getStyleByColumnAndRow($j, $count_line_exel_new)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('4caf50');
						}
					}
					elseif($i == 18 && $j == 1){
						$text = 'База за 6 місяців';
						$sheet->getStyle("B".$count_line_exel_new)->getFont()->setBold(true);
					}
					elseif($i == 18 && $j == 2){
						$text = 'К-ть дублів (по VIN-коду)';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 18 && $j == 3){
						$text = NULL;
					}
					elseif($i == 18 && $j == 4){
						$text = NULL;
					}
					elseif($i == 19 && $j == 2){
						$text = '% Коректних ел. адрес по БД';
						$sheet->mergeCells("C".$count_line_exel_new.":D".$count_line_exel_new);
					}
					elseif($i == 19 && $j == 3){
						$text = NULL;
					}
					elseif($i == 19 && $j == 4){
						$text = NULL;
					}
					else{
						$text = NULL;
					}

					if($text != NULL){
						$sheet->setCellValueByColumnAndRow($j, $count_line_exel_new, $text);//Заполнение таблицы
						//Перенос текста
							$sheet->getStyle("B".$count_line_exel_new)->getAlignment()->setWrapText(true);
							$sheet->getStyle("C".$count_line_exel_new)->getAlignment()->setWrapText(true);
							$sheet->getStyle("D".$count_line_exel_new)->getAlignment()->setWrapText(true);
							$sheet->getStyle("E".$count_line_exel_new)->getAlignment()->setWrapText(true);
						//Выравнивание текста по горизонтали и вертикали
							$sheet->getStyle("B".$count_line_exel_new)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$sheet->getStyle("C".$count_line_exel_new)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$sheet->getStyle("D".$count_line_exel_new)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$sheet->getStyle("E".$count_line_exel_new)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$sheet->getStyle("B".$count_line_exel_new)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$sheet->getStyle("C".$count_line_exel_new)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$sheet->getStyle("D".$count_line_exel_new)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
							$sheet->getStyle("E".$count_line_exel_new)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					}
				}
			}
		//Задание внешней рамки
			$border_startend = $count_line_exel+3;
			$border = array(
				'borders'=>array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					),
				)
			);
			$sheet->getStyle("B".$border_startend.":E".$count_line_exel_new)->applyFromArray($border);
		//Задание внутренней рамки
			$border = array(
				'borders'=>array(
					'inside' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					),
				)
			);
			
			$sheet->getStyle("B".$border_startend.":E".$count_line_exel_new)->applyFromArray($border);
		//Обядинение ячеек по горизонтали
			$merge_started = $count_line_exel+4;
			$merge_end = $count_line_exel_new-2;
			$merge_end_start = $count_line_exel_new-1;
			$sheet->mergeCells("B".$merge_started.":B".$merge_end);
			$sheet->mergeCells("B".$merge_end_start.":B".$count_line_exel_new);

		return $data;
	}
	private function count_success_vin($lists, $errors_file){
		$line_this_time 	= 1;
		$array_key			= 0;
		$count_success_vin 	= 0;
		foreach($lists as $lists_value){
			foreach($lists_value as $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value_value[16])){
						$count_success_vin++;
					}
				}
			}
		}
		return $count_success_vin;
	}
	private function count_doubles_vin($doubles_no_highlighted, $errors_file){
		$count = 0;
		foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
			//Убираем "VIN-код повторяется в строках:" из строки
				$Original_name_exel = $doubles_no_highlighted_value['message'];
				$change_this = array(" VIN-код повторюється в рядках: ");
				$on_this = array("");
				$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
			$count++;
		}
		//Убираем дубли
			$count_for = count($count_doubles_vin);
			$count_doubles_vin = array_unique($count_doubles_vin);

		$count_doubles_vin_return = 0;
		for($i = 0;$i < $count_for;$i++){
			if(isset($count_doubles_vin[$i])){
				$count = substr_count($count_doubles_vin[$i],",");
				$count_doubles_vin_return += $count;
			}
		}
		return $count_doubles_vin_return;
	}
	private function procent_correct_phone($lists, $doubles_no_highlighted, $errors_file){
		//Создаём массив дублей
			$count = 0;
			foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_no_highlighted_value['message'];
					$change_this = array(" VIN-код повторюється в рядках: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_vin[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_vin);$i++){
					unset($doubles_vin[$i][0]);
				}
				$count = 0;
				foreach($doubles_vin as $doubles_vin_value){
					foreach ($doubles_vin_value as $doubles_vin_value_value){
						$d_vin[$count] = $doubles_vin_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		$line_this_time = 1;
		$count_correct_phone = 0;
		$count_correct = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/^фл$|^юл$/", mb_strtolower($lists_value[20]))){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							$count_correct++;
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								$count_correct_phone++;
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		if($count_correct_phone != 0 && $count_correct != 0){
			$procent_correct_phone = round($count_correct_phone/$count_correct*100, 2);
		} else{
			$procent_correct_phone = 0;
		}

		return $procent_correct_phone;
	}
	private function procent_foreign($lists, $doubles_no_highlighted, $errors_file){
		//Создаём массив дублей
			$count = 0;
			foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_no_highlighted_value['message'];
					$change_this = array(" VIN-код повторюється в рядках: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_vin[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_vin);$i++){
					unset($doubles_vin[$i][0]);
				}
				$count = 0;
				foreach($doubles_vin as $doubles_vin_value){
					foreach ($doubles_vin_value as $doubles_vin_value_value){
						$d_vin[$count] = $doubles_vin_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		$line_this_time = 1;
		$count_correct_phone_foreign = 0;
		$count_correct = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$|^ЮЛ$|^Юл$|^юл$)/", $lists_value[20])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							$count_correct++;
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								//Убираем расширение из названия файла
									if(preg_match("/[0-9]{12}/", $lists_value[9])){
										$Original_name_exel = $lists_value[9];
									}
									if(preg_match("/[0-9]{12}/", $lists_value[10])){
										$Original_name_exel = $lists_value[10];
									}
									$change_this = array("+");
									$on_this = array("");
									$phone_now = str_replace($change_this, $on_this, $Original_name_exel);

								if(!preg_match("/^380/", $phone_now)){
									$count_correct_phone_foreign++;
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		if($count_correct_phone_foreign != 0 && $count_correct != 0){
			$procent_correct_phone_foreign = round($count_correct_phone_foreign/$count_correct*100, 2);
		} else{
			$procent_correct_phone_foreign = 0;
		}


		return $procent_correct_phone_foreign;
	}
	private function procent_suspicious($lists, $doubles_no_highlighted, $errors_file){
		//Создаём массив дублей
			$count = 0;
			foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_no_highlighted_value['message'];
					$change_this = array(" VIN-код повторюється в рядках: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_vin[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_vin);$i++){
					unset($doubles_vin[$i][0]);
				}
				$count = 0;
				foreach($doubles_vin as $doubles_vin_value){
					foreach ($doubles_vin_value as $doubles_vin_value_value){
						$d_vin[$count] = $doubles_vin_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		$line_this_time = 1;
		$count_correct_suspicious_phone = 0;
		$count_correct = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$|^ЮЛ$|^Юл$|^юл$)/", $lists_value[20])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							$count_correct++;
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								if(preg_match("/[0-9]{12}/", $lists_value[10])){
									if(preg_match("~(.)\\1{3}~", $lists_value[10])){
										$count_correct_suspicious_phone++;
									}
								}
								else{
									if(preg_match("~(.)\\1{3}~", $lists_value[9])){
										$count_correct_suspicious_phone++;
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		if($count_correct_suspicious_phone != 0 && $count_correct != 0){
			$procent_suspicious = round($count_correct_suspicious_phone/$count_correct*100, 2);
		} else{
			$procent_suspicious = 0;
		}

		return $procent_suspicious;
	}
	private function procent_correct_email($lists, $errors_file){
		$line_this_time = 1;
		$count_correct_email = 0;
		foreach($lists[0] as $lists_value){
			if(preg_match("/@/", $lists_value[11])){
				$count_correct_email++;
			}
			$line_this_time++;
		}

		$count_my = $line_this_time-$errors_file[1]-1;

		if($count_correct_email != 0 && $count_my != 0) {
			$procent_correct_email = round($count_correct_email/($line_this_time-$errors_file[1]-1)*100,2);
		} else {
			$procent_correct_email = 0;
		}
		
		return $procent_correct_email;
	}
	private function procent_consenting($lists, $doubles_no_highlighted, $errors_file){
		//Создаём массив дублей
			$count = 0;
			foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_no_highlighted_value['message'];
					$change_this = array(" VIN-код повторюється в рядках: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_vin[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_vin);$i++){
					unset($doubles_vin[$i][0]);
				}
				$count = 0;
				foreach($doubles_vin as $doubles_vin_value){
					foreach ($doubles_vin_value as $doubles_vin_value_value){
						$d_vin[$count] = $doubles_vin_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		$line_this_time = 1;
		$count_consenting = 0;
		$count_correct = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$|^ЮЛ$|^Юл$|^юл$)/", $lists_value[20])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								//Убираем + из строки
									if(preg_match("/[0-9]{12}/", $lists_value[9])){
										$Original_name_exel = $lists_value[9];
									}
									if(preg_match("/[0-9]{12}/", $lists_value[10])){
										$Original_name_exel = $lists_value[10];
									}
									$change_this = array("+");
									$on_this = array("");
									$phone_now = str_replace($change_this, $on_this, $Original_name_exel);

								if(preg_match("/^380/", $phone_now)){
									$count_correct++;
									if(preg_match("/(^ДА$|^Да$|^да$)/", $lists_value[23])){
										$count_consenting++;
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		if($count_consenting != 0 && $count_correct != 0){
			$procent_consenting = round($count_consenting/$count_correct*100, 2);
		} else{
			$procent_consenting = 0;
		}

		return $procent_consenting;
	}
	private function procent_legal_entity($lists, $doubles_no_highlighted, $errors_file){
		//Создаём массив дублей
			$count = 0;
			foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_no_highlighted_value['message'];
					$change_this = array(" VIN-код повторюється в рядках: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_vin[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_vin);$i++){
					unset($doubles_vin[$i][0]);
				}
				$count = 0;
				foreach($doubles_vin as $doubles_vin_value){
					foreach ($doubles_vin_value as $doubles_vin_value_value){
						$d_vin[$count] = $doubles_vin_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		$line_this_time = 1;
		$count_legal_entity = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ЮЛ$|^Юл$|^юл$)/", $lists_value[20])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								//Убираем + из строки
									if(preg_match("/[0-9]{12}/", $lists_value[9])){
										$Original_name_exel = $lists_value[9];
									}
									if(preg_match("/[0-9]{12}/", $lists_value[10])){
										$Original_name_exel = $lists_value[10];
									}
									$change_this = array("+");
									$on_this = array("");
									$phone_now = str_replace($change_this, $on_this, $Original_name_exel);

								if(preg_match("/^380/", $phone_now)){
									if(preg_match("/(^ДА$|^Да$|^да$)/", $lists_value[23])){
										$count_legal_entity++;
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}
		$line_this_time = 1;
		$count_correct = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$|^ЮЛ$|^Юл$|^юл$)/", $lists_value[19])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								//Убираем + из строки
									if(preg_match("/[0-9]{12}/", $lists_value[9])){
										$Original_name_exel = $lists_value[9];
									}
									if(preg_match("/[0-9]{12}/", $lists_value[10])){
										$Original_name_exel = $lists_value[10];
									}
									$change_this = array("+");
									$on_this = array("");
									$phone_now = str_replace($change_this, $on_this, $Original_name_exel);

								if(preg_match("/^380/", $phone_now)){
									if(preg_match("/(^ДА$|^Да$|^да$)/", $lists_value[22])){
										$count_correct++;
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		if($count_legal_entity != 0 && $count_correct != 0){
			$procent_legal_entity = round($count_legal_entity/$count_correct*100, 2);
		} else{
			$procent_legal_entity = 0;
		}
		
		return $procent_legal_entity;
	}
	private function procent_natural_person($lists, $doubles_no_highlighted, $errors_file){
		//Создаём массив дублей
			$count = 0;
			foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_no_highlighted_value['message'];
					$change_this = array(" VIN-код повторюється в рядках: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_vin[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_vin);$i++){
					unset($doubles_vin[$i][0]);
				}
				$count = 0;
				foreach($doubles_vin as $doubles_vin_value){
					foreach ($doubles_vin_value as $doubles_vin_value_value){
						$d_vin[$count] = $doubles_vin_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		$line_this_time = 1;
		$count_natural_person = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$)/", $lists_value[20])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								//Убираем + из строки
									if(preg_match("/[0-9]{12}/", $lists_value[9])){
										$Original_name_exel = $lists_value[9];
									}
									if(preg_match("/[0-9]{12}/", $lists_value[10])){
										$Original_name_exel = $lists_value[10];
									}
									$change_this = array("+");
									$on_this = array("");
									$phone_now = str_replace($change_this, $on_this, $Original_name_exel);

								if(preg_match("/^380/", $phone_now)){
									if(preg_match("/(^ДА$|^Да$|^да$)/", $lists_value[23])){
										$count_natural_person++;
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}
		$line_this_time = 1;
		$count_correct = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$|^ЮЛ$|^Юл$|^юл$)/", $lists_value[19])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								//Убираем + из строки
									if(preg_match("/[0-9]{12}/", $lists_value[9])){
										$Original_name_exel = $lists_value[9];
									}
									if(preg_match("/[0-9]{12}/", $lists_value[10])){
										$Original_name_exel = $lists_value[10];
									}
									$change_this = array("+");
									$on_this = array("");
									$phone_now = str_replace($change_this, $on_this, $Original_name_exel);

								if(preg_match("/^380/", $phone_now)){
									if(preg_match("/(^ДА$|^Да$|^да$)/", $lists_value[22])){
										$count_correct++;
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		if($count_natural_person != 0 && $count_correct != 0){
			$procent_natural_person = round($count_natural_person/$count_correct*100, 2);
		} else {
			$procent_natural_person = 0;
		}
		
		return $procent_natural_person;
	}
	private function procent_unique_phone($lists, $doubles_no_highlighted, $doubles, $errors_file){
		//Создаём массив дублей
			$count = 0;
			foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_no_highlighted_value['message'];
					$change_this = array(" VIN-код повторюється в рядках: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_vin[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_vin);$i++){
					unset($doubles_vin[$i][0]);
				}
				$count = 0;
				foreach($doubles_vin as $doubles_vin_value){
					foreach ($doubles_vin_value as $doubles_vin_value_value){
						$d_vin[$count] = $doubles_vin_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		//Создаём массив дублей
			if(isset($doubles['phone'])){
				$count = 0;
				foreach($doubles['phone'] as $doubles_value){
					//Убираем "VIN-код повторяется в строках:" из строки
						$Original_name_exel = $doubles_value['message'];
						$change_this = array(" Телефон повторюється в рядках: ");
						$on_this = array("");
						$count_doubles_phone[$count] = str_replace($change_this, $on_this, $Original_name_exel);
					$count++;
				}
				//Убираем дубли
					$count_for = count($count_doubles_phone);
					$count_doubles_phone = array_unique($count_doubles_phone);
				//Создаем массив дублей без первых элементов
					$count = 0;
					for($i = 0;$i < $count_for;$i++){
						if(isset($count_doubles_phone[$i])){
							$doubles_phone[$count] = explode(",", $count_doubles_phone[$i]);
							$count++;
						}
					}
					for($i = 0;$i < count($doubles_phone);$i++){
						unset($doubles_phone[$i][0]);
					}
					$count = 0;
					foreach($doubles_phone as $doubles_phone_value){
						foreach ($doubles_phone_value as $doubles_phone_value_value){
							$d_phone[$count] = $doubles_phone_value_value+$errors_file[1]-1;
							$count++;
						}
					}
			}
			

		$line_this_time = 1;
		$count_unique_phone = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$)/", $lists_value[20])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								//Убираем + из строки
									if(preg_match("/[0-9]{12}/", $lists_value[9])){
										$Original_name_exel = $lists_value[9];
									}
									if(preg_match("/[0-9]{12}/", $lists_value[10])){
										$Original_name_exel = $lists_value[10];
									}
									$change_this = array("+");
									$on_this = array("");
									$phone_now = str_replace($change_this, $on_this, $Original_name_exel);

								if(preg_match("/^380/", $phone_now)){
									$flag_doubles_phone = 0;
									if(isset($d_phone)){
										foreach($d_phone as $doubles_value){
											if($doubles_value == $line_this_time){
												$flag_doubles_phone = 1;
												break;
											}
										}
									}
									if($flag_doubles_phone == 0){
										if(preg_match("/(^ДА$|^Да$|^да$)/", $lists_value[23])){
											$count_unique_phone++;
										}
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		$line_this_time = 1;
		$count_correct = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$)/", $lists_value[19])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								//Убираем + из строки
									if(preg_match("/[0-9]{12}/", $lists_value[9])){
										$Original_name_exel = $lists_value[9];
									}
									if(preg_match("/[0-9]{12}/", $lists_value[10])){
										$Original_name_exel = $lists_value[10];
									}
									$change_this = array("+");
									$on_this = array("");
									$phone_now = str_replace($change_this, $on_this, $Original_name_exel);

								if(preg_match("/^380/", $phone_now)){
									if(preg_match("/(^ДА$|^Да$|^да$)/", $lists_value[22])){
										$count_correct++;
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		if($count_unique_phone != 0 && $count_correct != 0){
			$procent_unique_phone = round($count_unique_phone/$count_correct*100, 2);
		} else {
			$procent_unique_phone = 0;
		}
		
		return $procent_unique_phone;
	}
	private function procent_eqc($lists, $doubles_no_highlighted, $doubles, $errors_file){
		//Создаём массив дублей
			$count = 0;
			foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_no_highlighted_value['message'];
					$change_this = array(" VIN-код повторюється в рядках: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_vin[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_vin);$i++){
					unset($doubles_vin[$i][0]);
				}
				$count = 0;
				foreach($doubles_vin as $doubles_vin_value){
					foreach ($doubles_vin_value as $doubles_vin_value_value){
						$d_vin[$count] = $doubles_vin_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		//Создаём массив дублей
			if(isset($doubles['phone'])){
				$count = 0;
				foreach($doubles['phone'] as $doubles_value){
					//Убираем "VIN-код повторяется в строках:" из строки
						$Original_name_exel = $doubles_value['message'];
						$change_this = array(" Телефон повторюється в рядках: ");
						$on_this = array("");
						$count_doubles_email[$count] = str_replace($change_this, $on_this, $Original_name_exel);
					$count++;
				}
				//Убираем дубли
					$count_for = count($count_doubles_email);
					$count_doubles_email = array_unique($count_doubles_email);
				//Создаем массив дублей без первых элементов
					$count = 0;
					for($i = 0;$i < $count_for;$i++){
						if(isset($count_doubles_email[$i])){
							$doubles_phone[$count] = explode(",", $count_doubles_email[$i]);
							$count++;
						}
					}
					for($i = 0;$i < count($doubles_phone);$i++){
						unset($doubles_phone[$i][0]);
					}
					$count = 0;
					foreach($doubles_phone as $doubles_phone_value){
						foreach ($doubles_phone_value as $doubles_phone_value_value){
							$d_phone[$count] = $doubles_phone_value_value+$errors_file[1]-1;
							$count++;
						}
					}
			}

		$line_this_time = 1;
		$count_eqc = 0;
		$count_correct = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$)/", $lists_value[20])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							$count_correct++;
							if(preg_match("/[0-9]{12}/", $lists_value[10]) || preg_match("/[0-9]{12}/", $lists_value[9])){
								//Убираем + из строки
									if(preg_match("/[0-9]{12}/", $lists_value[9])){
										$Original_name_exel = $lists_value[9];
									}
									if(preg_match("/[0-9]{12}/", $lists_value[10])){
										$Original_name_exel = $lists_value[10];
									}
									$change_this = array("+");
									$on_this = array("");
									$phone_now = str_replace($change_this, $on_this, $Original_name_exel);

								if(preg_match("/^380/", $phone_now)){
									$flag_doubles_phone = 0;
									if(isset($d_phone)){
										foreach($d_phone as $doubles_value){
											if($doubles_value == $line_this_time){
												$flag_doubles_phone = 1;
												break;
											}
										}
									}
									if($flag_doubles_phone == 0){
										if(preg_match("/(^ДА$|^Да$|^да$)/", $lists_value[23])){
											$count_eqc++;
										}
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		if($count_eqc != 0 && $count_correct != 0){
			$procent_eqc = round($count_eqc/$count_correct*100, 2);
		} else{
			$procent_eqc = 0;
		}
		
		return $procent_eqc;
	}
	private function procent_net_eqc($lists, $doubles_no_highlighted, $doubles, $errors_file){
		//Создаём массив дублей
			$count = 0;
			foreach($doubles_no_highlighted['vin'] as $doubles_no_highlighted_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_no_highlighted_value['message'];
					$change_this = array(" VIN-код повторюється в рядках: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_vin[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_vin);$i++){
					unset($doubles_vin[$i][0]);
				}
				$count = 0;
				foreach($doubles_vin as $doubles_vin_value){
					foreach ($doubles_vin_value as $doubles_vin_value_value){
						$d_vin[$count] = $doubles_vin_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		//Создаём массив дублей
			$count = 0;
			foreach($doubles['email'] as $doubles_value){
				//Убираем "VIN-код повторяется в строках:" из строки
					$Original_name_exel = $doubles_value['message'];
					$change_this = array(" E-mail повторюється в рядках:: ");
					$on_this = array("");
					$count_doubles_vin[$count] = str_replace($change_this, $on_this, $Original_name_exel);
				$count++;
			}
			//Убираем дубли
				$count_for = count($count_doubles_vin);
				$count_doubles_vin = array_unique($count_doubles_vin);
			//Создаем массив дублей без первых элементов
				$count = 0;
				for($i = 0;$i < $count_for;$i++){
					if(isset($count_doubles_vin[$i])){
						$doubles_email[$count] = explode(",", $count_doubles_vin[$i]);
						$count++;
					}
				}
				for($i = 0;$i < count($doubles_email);$i++){
					unset($doubles_email[$i][0]);
				}
				$count = 0;
				foreach($doubles_email as $doubles_email_value){
					foreach ($doubles_email_value as $doubles_email_value_value){
						$d_email[$count] = $doubles_email_value_value+$errors_file[1]-1;
						$count++;
					}
				}

		$line_this_time = 1;
		$count_net_eqc = 0;
		$count_correct = 0;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				if(preg_match("/(^ФЛ$|^Фл$|^фл$)/", $lists_value[20])){
					if(preg_match("/[A-Za-z0-9]{17}/", $lists_value[16])){
						$flag_doubles = 0;
						foreach($d_vin as $doubles_value){
							if($doubles_value == $line_this_time){
								$flag_doubles = 1;
								break;
							}
						}
						if($flag_doubles == 0){
							$count_correct++;
							if(preg_match("/@/", $lists_value[11])){
								$flag_doubles_email = 0;
								foreach($d_email as $doubles_value){
									if($doubles_value == $line_this_time){
										$flag_doubles_email = 1;
										break;
									}
								}
								if($flag_doubles_email == 0){
									if(preg_match("/(^ДА$|^Да$|^да$)/", $lists_value[23])){
										$count_net_eqc++;
									}
								}
							}
						}
					}
				}
			}
			$line_this_time++;
		}

		if($count_net_eqc != 0 && $count_correct != 0){
			$procent_net_eqc = round($count_net_eqc/$count_correct*100, 2);
		} else {
			$procent_net_eqc = 0;
		}
		
		return $procent_net_eqc;
	}
	private function send_message_errors($responsible_for_quality_all){
		if($_SESSION['email'] != NULL && $_SESSION['responsible_for_quality'] == 1){
			switch($_SESSION['organization']){
				case 'peugeot':
					$from = 'peugeot_feedback@800.com.ua';
					break;
				case 'citroen':
					$from = 'citroen_feedback@800.com.ua';
					break;
				case 'opel':
					$from = 'opel_feedback@800.com.ua';
					break;
				case 'ds':
					$from = 'ds_feedback@800.com.ua';
					break;
			}

			$subject = "БД Визиты на СТО не принята";
			$message = 'Добрий день!<br>База не прийнята до аналізу.<br>Помилка: На першому аркуші файлу не знайдені дані.<br> <br> Необхідно виправити помилку і завантажити коректну базу на дилерський портал: <a href="https://pcu.socmedia.com.ua/">https://pcu.socmedia.com.ua/</a><br><br>З повагою, Служба підтримки<br>PCU CRM Portal';

			$emails_send = $this->get_email();

			$this->load->library('email');
			$this->email->from($from);
			$this->email->to($emails_send['PrimaryContact']);
			$this->email->cc($responsible_for_quality_all);
			$this->email->bcc($from);
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();

			$this->data['massege_view'] = $message;
		}
		else{
			if($_SESSION['email'] == NULL && $_SESSION['responsible_for_quality'] == 1){
				$this->data['massege_view'] = 'У вашому обліковому записі не вказано E-mail';
			}
			elseif($_SESSION['email'] != NULL && $_SESSION['responsible_for_quality'] != 1){
				$this->data['massege_view'] = 'Ви не є відповідальним за якість';
			}
			else{
				$this->data['massege_view'] = 'У вашому обліковому записі не вказано E-mail і ви не є відповідальним за якість';
			}
		}
		$this->template->admin_render('total_excel', $this->data);
	}
	private function send_message($srcFileName, $errors, $eqs, $lists, $errors_file, $responsible_for_quality_all){
		if($_SESSION['email'] != NULL && $_SESSION['responsible_for_quality'] == 1){
			if(isset($errors['id']) || isset($errors['rrdi']) || isset($errors['vin']) || isset($errors['date']) || isset($errors['agree']) || isset($errors['kind_work']) || isset($errors['source_client_record']) || isset($errors['gender'])){
				$subject = 'БД{'.$srcFileName.'} не прийнята';
				$message = 'Добрий день!<br><br>База не прийнята до аналізу.<br>Помилка: ';
				if(isset($errors['id'])){
					$message .= ' Знайдені не пронумеровані рядки. ';
				}
				if(isset($errors['gender'])){
					$message .= ' Не вказано стать. ';
				}
				if(isset($errors['rrdi'])){
					$message .= ' Неправильний RRDI. ';
				}
				if(isset($errors['customer_type'])){
					$message .= ' Знайдено записи без типу клієнта (значення повинні бути: ФЛ або ЮЛ). ';
				}
				if(isset($errors['vin'])){
					$message .= ' Неправильний VIN. ';
				}
				if(isset($errors['mileage'])){
					$message .= ' Не вказаний пробіг. ';
				}
				if(isset($errors['date'])){
					$message .= ' Дата видачі авто не відповідає періоду або не містить даних. ';
				}
				if(isset($errors['agree'])){
					$message .= ' Не вказано згоду клієнта на контакт. ';
				}
				if(isset($errors['kind_work'])){
					$message .= ' Знайдено записи без зазначеного виду роботи. ';
				}
				if(isset($errors['source_client_record'])){
					$message .= ' Знайдено записи без зазначеного джерела записи клієнта. ';
				}
				$message .= '<br><br>Необхідно виправити помилку і завантажити коректну базу на дилерський портал: <a href="https://pcu.socmedia.com.ua/">https://pcu.socmedia.com.ua/</a><br><br>З повагою, Служба підтримки<br>PCU CRM Portal';
			}
			else{
				if($eqs['procent_eqc'] >= 80 && $eqs['procent_net_eqc'] >= 61){
					$subject = 'Аналіз бази Візитів на СТО. БАЗА КОРЕКТНА';
					$message = 'Добрий день!<br><br>У доданому файлі представлений аналіз клієнтської бази візитів на СТО. <br> <br> Коректність БД для дослідження EQC - {'.$eqs['procent_eqc'].'}%.<br><br>Коректність БД для дослідження NET EQC - {'.$eqs['procent_net_eqc'].'}%.<br><br>База коректна, дякуємо за співпрацю.<br><br>З повагою, Служба підтримки<br>PCU CRM Portal';
				}
				else{
					$subject = 'Аналіз бази Візитів на СТО. БАЗА НЕКОРЕКТНА';
					$message = 'Добрий день!<br><br>У доданому файлі представлений аналіз клієнтської бази візитів на СТО. <br> <br> Коректність БД для дослідження EQC - {'.$eqs['procent_eqc'].'}%.<br><br>Коректність БД для дослідження NET EQC - {'.$eqs['procent_net_eqc'].'}%.<br><br>Необхідно виправити помилку і завантажити коректну базу на дилерський портал: <a href="https://pcu.socmedia.com.ua/">https://pcu.socmedia.com.ua/</a><br><br>З повагою, Служба підтримки<br>PCU CRM Portal';
				}
				$this->send_crm($lists, $errors_file);
				$this->get_ftp($lists, $errors_file);
			}
			switch($_SESSION['organization']){
				case 'peugeot':
					$from = 'peugeot_feedback@800.com.ua';
					break;
				case 'citroen':
					$from = 'citroen_feedback@800.com.ua';
					break;
				case 'opel':
					$from = 'opel_feedback@800.com.ua';
					break;
				case 'ds':
					$from = 'ds_feedback@800.com.ua';
					break;
			}

			$this->load->library('email');
			$this->email->from($from);
			$this->email->to( $this->get_email() );
			// $this->email->to('igirsuslov@gmail.com');
			$this->email->cc($responsible_for_quality_all);
			$this->email->bcc($from);
			$this->email->attach($srcFileName.".xls");
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();

			//удалить файл Exel
			unlink($srcFileName.'.xls');

			$this->data['massege_view'] = $message;
		}
		else{
			if($_SESSION['email'] == NULL && $_SESSION['responsible_for_quality'] == 1){
				$this->data['massege_view'] = 'У вашому обліковому записі не вказано E-mail';
			}
			elseif($_SESSION['email'] != NULL && $_SESSION['responsible_for_quality'] != 1){
				$this->data['massege_view'] = 'Ви не є відповідальним за якість';
			}
			else{
				$this->data['massege_view'] = 'У вашому обліковому записі не вказано E-mail і ви не є відповідальним за якість';
			}
		}
		$this->template->admin_render('total_excel', $this->data);
	}
	private function send_template($responsible_for_quality_all){
		if($_SESSION['email'] != NULL && $_SESSION['responsible_for_quality'] == 1){
			switch($_SESSION['organization']){
				case 'peugeot':
					$from = 'peugeot_feedback@800.com.ua';
					break;
				case 'citroen':
					$from = 'citroen_feedback@800.com.ua';
					break;
				case 'opel':
					$from = 'opel_feedback@800.com.ua';
					break;
				case 'ds':
					$from = 'ds_feedback@800.com.ua';
					break;
			}

			$emails_send = $this->get_email();

			$subject = 'БД не прийнята';
			$message = 'Добрий день!<br>База не прийнята до аналізу.<br>Помилка: база даних не відповідає шаблону<br><br>Необхідно виправити помилку і завантажити коректну базу на дилерський портал: <a href="https://pcu.socmedia.com.ua/">https://pcu.socmedia.com.ua/</a><br><br>У вкладеному файлі листа представлений шаблон.<br>Скопіюйте свою базу в цей файл і повторіть завантаження нового файлу на портал.<br><br>З повагою, Служба підтримки<br>PCU CRM Portal';

			$this->load->library('email');
			$this->email->from($from);
			$this->email->to($emails_send['PrimaryContact']);
			$this->email->cc($responsible_for_quality_all);
			$this->email->bcc($from);
			$this->email->attach(__DIR__.'/../../template/template.xlsx');
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();

			$this->data['massege_view'] = $message;
		}
		else{
			if($_SESSION['email'] == NULL && $_SESSION['responsible_for_quality'] == 1){
				$this->data['massege_view'] = 'У вашому обліковому записі не вказано E-mail';
			}
			elseif($_SESSION['email'] != NULL && $_SESSION['responsible_for_quality'] != 1){
				$this->data['massege_view'] = 'Ви не є відповідальним за якість';
			}
			else{
				$this->data['massege_view'] = 'У вашому обліковому записі не вказано E-mail і ви не є відповідальним за якість';
			}
		}
		$this->template->admin_render('total_excel', $this->data);
	}
	private function send_crm($lists, $errors_file){
		$this->load->model('Exel_model');
		$get_rrdi = $this->rrdi();

		$this->Exel_model->del_old_list();
		if(date('d') > 15){
			$this->Exel_model->del_old_list_this_month();
		}
		$rrdi = $this->Exel_model->get_rrdi();
		foreach($rrdi as $rrdi_value){
			if($rrdi_value['rrdi'] == $get_rrdi){
				$flag = 1;
				break;
			}
		}
		if(@$flag == 1){//Если перезаписывается бД
			$id_list = $this->Exel_model->get_id_list($get_rrdi);
			foreach($id_list as $id_list_value){
				CRM::del_visit_sto($id_list_value['id_list']);
				$this->Exel_model->del_id_list($id_list_value['id']);
			}
		}
		$line_this_time = 1;
		foreach($lists[0] as $lists_value){
			if($line_this_time > $errors_file[1]-1){
				$count++;
				$got_id_list = CRM::post_visit_sto($lists_value, $line_this_time);
				if(!empty($got_id_list['Result']['Data']['newafsinfoid'])){
					$this->Exel_model->add_id_list($got_id_list['Result']['Data']['newafsinfoid'], $get_rrdi);
				}
			}
			$line_this_time++;
		}
	}
	private function get_email(){
		$this->load->model('Exel_model');
		$web_user_id = $this->Exel_model->get_web_user_id();
		$emailsend = CRM::get_email_send($web_user_id[0]['web_userid']);
		return $emailsend['Result']['Data']['PrimaryContact'];
	}
	public function sending_notifications(){
		$this->load->library('email');
		$this->load->model('Exel_model');

		//Удаление старых записей
			$this->Exel_model->del_old_list();
			if(date('d') > 15){
				$this->Exel_model->del_old_list_this_month();
			}

		//Задание от кого сообщение
			switch($_SESSION['organization']){
				case 'peugeot':
					$from = 'peugeot_feedback@800.com.ua';
					break;
				case 'citroen':
					$from = 'citroen_feedback@800.com.ua';
					break;
				case 'opel':
					$from = 'opel_feedback@800.com.ua';
					break;
				case 'ds':
					$from = 'ds_feedback@800.com.ua';
					break;
			}

		$period = CRM::get_period();//Получения периода для отправки сообщений
		//Задание необходимой мне даты
			if(date('d') < 16){
				$month = date('m') - 1;
				$month = '0'.$month;
			}
			else{
				$month = date('m');
			}
			if($month == 0){
				$month = 12;
				$year = date('Y') - 1;
			}
			$year = date('Y');
			$total_date = $year.$month;

		//Получение почтовых адресов для отправки
			$web_user_id_responsible_for_quality = $this->Exel_model->get_web_user_id_responsible_for_quality();
			$count = 0;
			foreach($web_user_id_responsible_for_quality as $id){
				$send[$count] = $this->Exel_model->get_id_list($id['rrdi']);
				if(!$send[$count]){
					$email[$count] = CRM::get_email_send($id['web_userid']);
				}
				$count++;
			}

		//отправка почты
			foreach($period['Result']['Data'] as $period_value){
				if($period_value['NewPeriod'] == $total_date){
					if($period_value['FirstDate'] == date('d.m.Y') || $period_value['SecondDate'] == date('d.m.Y')){
						$day_massege = $period_value['SecondDate'];
					}
					else{
						$day_massege = $period_value['FourthDate'];
					}
					if($period_value['FirstDate'] == date('d.m.Y') || $period_value['ThirdDate'] == date('d.m.Y')){
						$subject = "Запит бази візитів СТО";
						$message = 'Добрий день!<br><br>Сьогодні протягом дня на дилерський портал https://pcu.socmedia.com.ua необхідно завантажити коректну базу візитів СТО за попередні 2 тижні суворо згідно шаблону і інструкціі.<br><br>Граничні терміни прийняття бази: '.$day_massege.'<br><br>З повагою, Служба підтримки<br>PCU CRM Portal';
						foreach($email as $email_value){
							$this->email->clear();
							$this->email->from($from);
							$this->email->to($email_value['Result']['Data']['PrimaryContact']);
							$this->email->bcc($from);
							$this->email->subject($subject);
							$this->email->message($message);
							$this->email->send();
							sleep(2);
						}
					}

					elseif($period_value['SecondDate'] == date('d.m.Y') || $period_value['FourthDate'] == date('d.m.Y')){
						$subject = "Не надано базу візитів СТО";
						$message = 'Добрий день!<br><br>У перший робочий день місяця не надано базу візитів СТО.<br>На дилерський портал https://pcu.socmedia.com.ua необхідно завантажити коректну базу візитів СТО за попередні 2 тижні згідно шаблону і інструкціі.<br><br>Граничні терміни прийняття бази: '.$day_massege.'<br><br>З повагою, Служба підтримки<br>PCU CRM Portal';
						foreach($email as $email_value){
							$this->email->clear();
							$this->email->from($from);
							$this->email->to($email_value['Result']['Data']['PrimaryContact']);
							$this->email->cc($email_value['Result']['Data']['CEO']);
							$this->email->bcc($from);
							$this->email->subject($subject);
							$this->email->message($message);
							$this->email->send();
							sleep(2);
						}
					}
				}
			}

		$this->email->from('igirsuslov@gmail.com');
		$this->email->to('igirsuslov@gmail.com');
		$this->email->subject('Перевірка');
		$this->email->message('Перевіряю');
		$this->email->send();
	}

	public function get_ftp($lists, $errors_file){
		$this->load->model('Ftp_model');
		$this->Ftp_model->del_value_ftp($lists[0][1]);

		switch ($_SESSION['organization']){
			case 'opel':
				$brand = 'OV';
				break;
			case 'peugeot':
				$brand = 'AP';
				break;
			case 'citroen':
				$brand = 'AC';
				break;
			case 'ds':
				$brand = 'DS';
				break;
		}

		switch ($_SESSION['organization']){
			case 'opel':
				$TradeMark = 'Опель';
				break;
			case 'peugeot':
				$TradeMark = 'Пежо';
				break;
			case 'citroen':
				$TradeMark = 'Ситроен';
				break;
			case 'ds':
				$TradeMark = 'ДС';
				break;
		}

		$line_this_time	= 0;
		foreach ($lists as $key_lists => $lists_value){
			foreach ($lists_value as $lists_value_key => $lists_value_value){
				$line_this_time++;
				if($line_this_time > $errors_file[1]-1){
					$this->Ftp_model->add_value_ftp($lists_value_value, $brand, $TradeMark);
				}
			}
		}
	}

	private function rrdi(){
		$this->load->model('Exel_model');
		$web_userid = $this->Exel_model->get_web_userid();
		$webuser_info = CRM::get_webuser_by_id( $web_userid[0]['web_userid'] );
		$account = CRM::get_dealer_account_id( $webuser_info['Result']['Data']['New_dealer'] );
		$rrdi = $account['Result']['Data']['New_rrdi'];
		return $rrdi;
	}
}