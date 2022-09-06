<?php
// error_reporting(E_ALL);
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', '600');

// require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalDataBar;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalColorScale;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalDataBarExtension;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormatValueObject;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\ConditionalFormattingRuleExtension;

class Excel_report extends CI_Controller {

	const TITLES = [
		'Дата/час створення',              'Номер звернення', 'Статус звернення', 'Ресторан',         'Місто', 
		'Операційний менеджер',            'Консультант',     'Тип звернення',    'Розділ звернення', 'Тема звернення',
		'Суть звернення (текстовий опис)', 'Компенсація',     'Канал надходження'];
	
	const DB_TITLES = [ 'CreatedOn' => 0,           // Дата/час створення
						'TicketNumber' => 1,        // Номер звернення
						'new_requeststatus' => 2,   // Статус звернення
						'new_restaurantName' => 3,  // Ресторан
						'new_cityName' => 4,        // Місто
						'new_opsmanagerName' => 5,  // Операційний менеджер
						'new_consultantName' => 6,  // Консультант
						'new_requesttypeName' => 7, // Тип звернення
						'new_requestpartName' => 8, // Розділ звернення
						'new_requestthemeName' => 9,    // Тема звернення
						'Description' => 10,        // 'Суть звернення (текстовий опис)
						'new_name' => 11,           // Компенсація
						'new_requestchannel' => 12  // Канал надходження
						];

	public function __construct() {
		parent::__construct();
		$this->load->library('Utils');
		$this->config->load('excel_report');
	} // __construct
	
	private static function convert_config( $str, $date_from, $date_to) {
		$str = str_replace('@date_from', $date_from, $str);
		$str = str_replace('@date_to', $date_to, $str);
		return $str;
	}

	public function index() {
		
		// flagtoSend
		// ***************************************
		$today = new DateTime();
		$flagToSend = false;
		if(  $today->format('H:i') == '10:00' )	$flagToSend = true;
	    //$flagToSend = true;
		echo '$flagToSend='.(int)$flagToSend.'<br>';
		
    	// calc all needed date
    	// ****************************
		$today = new DateTime('now', new DateTimeZone("Europe/Kiev"));

		// @debug
		//$today->modify('-11 day');

		$k = (int)$today->format('Z')/60/60;

		$yesterday = clone $today;
		$yesterday_end = clone $today;
		$prev_week_start = clone $today;
		$prev_week_end = clone $today;
		$prev_month_start = clone $today;
		$prev_month_end = clone $today;

		// modify all date

		// daily
		// *****************************************************
		// $yesterday->modify('yesterday');
		$yesterday->modify('yesterday');
		$yesterday_str = $yesterday->format('d-m-Y');
		$yesterday->modify('-'.$k.' hour');
		
		// $yesterday_end->modify('yesterday');
		$yesterday_end->modify('yesterday 23:59:59');
		$yesterday_end->modify('-'.$k.' hour');
		$yesterday_end_str = $yesterday_end->format('d-m-Y');
		
		// weekly
		// ******************************
		$prev_week_start->modify('Monday previous week');
		// $prev_week_start->modify('first day of this month');
		$prev_week_start_str = $prev_week_start->format('d-m-Y');
		$prev_week_start->modify('-'.$k.' hour');
		
		$prev_week_end->modify('Sunday previous week 23:59:59');
		$prev_week_end_str = $prev_week_end->format('d-m-Y');
		$prev_week_end->modify('-'.$k.' hour');

		// monthly
		// *****************************
		$prev_month_start->modify('first day of previous month');
		$prev_month_start_str = $prev_month_start->format('d-m-Y');
		$prev_month_start->modify('-'.$k.' hour');

		$prev_month_end->modify('last day of previous month');
		$prev_month_end_str = $prev_month_end->format('d-m-Y');
		$prev_month_end->modify('-'.$k.' hour');
		
		$daily_fileName = 'report/'.self::convert_config( $this->config->item('er_daily_fileName'), $yesterday_str, $yesterday_end_str);
		$weekly_fileName = 'report/'.self::convert_config( $this->config->item('er_weekly_fileName'), $prev_week_start_str, $prev_week_end_str);
		$monthly_fileName = 'report/'.self::convert_config( $this->config->item('er_monthly_fileName'), $prev_month_start_str, $prev_month_end_str);

		echo '$k='.$k.'<br>';
		echo '$today='.$today->format('Y-m-d H:i:s').'<br>';
		echo '$yestarday='.$yesterday->format('Y-m-d H:i:s').'<br>';
		echo '$yesterday_end='.$yesterday_end->format('Y-m-d H:i:s').'<br>';
		echo '$prev_week_start='.$prev_week_start->format('Y-m-d H:i:s').'<br>';
		echo '$prev_week_end='.$prev_week_end->format('Y-m-d H:i:s').'<br>';
		echo '$daily_fileName='.$daily_fileName.'<br>';
		echo '$weekly_fileName='.$weekly_fileName.'<br>';
		echo '$monthly_fileName='.$monthly_fileName.'<br>';

		// $query= $this->db->query("select * from INFORMATION_SCHEMA.columns where TABLE_NAME='Incident'")->result();
		// echo Utils::prettyPrint( $query, '$query' );
		
		// dayli
		// ************************************************************
		$this->send_email_excel($flagToSend,
								$this->get_appeals($yesterday->format('Y-m-d H:i:s'),
		                                           $yesterday_end->format('Y-m-d H:i:s')
												   ),
								$daily_fileName,
								'daily',
		                        $yesterday_str, 
		                        $yesterday_end_str
				       		    );

		// weekly
		// ******************************************					   
		if ($today->format('N') == 1) {
			$this->send_email_excel($flagToSend,
									$this->get_appeals($prev_week_start->format('Y-m-d H:i:s'),
			                                           $prev_week_end->format('Y-m-d H:i:s')
													   ),
									$weekly_fileName,
									'weekly',
			                        $prev_month_start_str, 
			                        $prev_week_end_str, 
									);
		} // if

		// monthly
		// *******************************************
		if ( (int)$today->format('d') == 1 ) {

			$this->send_email_excel($flagToSend,
									$this->get_appeals($prev_month_start->format('Y-m-d H:i:s'),
			                                           $prev_month_end->format('Y-m-d H:i:s')
													   ),
									$monthly_fileName,
									'monthly',
			                        $prev_month_start_str, 
			                        $prev_month_end_str, 
									);
		} // if

	} // index
	
	private function get_appeals($date_begin = null, $date_end = null) {
		
		$app = new Appeals;
		$data = $app -> listForReport($date_begin, $date_end);
		// echo Utils::prettyPrint( $data, '$data' );
		
		$appeals = [];
		$appeals[] = self::TITLES;
		foreach($data as $item) {
			
			$arr = [];
			for ($i=0; $i<count(self::DB_TITLES); $i++) $arr[] = '';
			foreach (self::DB_TITLES as $key => $value) {
				$arr[ $value ] = $item[ $key ];
			} // foreach
			$appeals[] = $arr;
			
		} // foreach
		
		// echo Utils::prettyPrint( $appeals, 'appeals ('.count($appeals).')' );
		return $appeals;

	} // get_appeals
	
    public function send_email_excel($flagToSend, $appeals, $fileName, $type, $date_begin, $date_end) {
		
		$log_file = 'report/send_mail.log';
		$log_msg = '';
		
		echo '<h3>send_email_excel ('.$type.')</h3>';
		$log_msg .= 'send_email_excel('.$type.') started at '.date('Y-m-d H:i:s').PHP_EOL;
	
		// build report
		// *****************************************
		
		$this->build_excel_report( array('data' => $appeals, 'filename' => $fileName) );
		$log_msg .= 'Файл для отправки: '.$fileName.PHP_EOL;
		// load config
		// *****************************************

		$users =  $this->config->item('er_email_list');
		if (empty($users) || !is_array($users)) throw new Error('Error in config. Param "er_email_list" not found or is wrong');
		$users[] = 'igirsuslov@gmail.com';
		
		$bcc_list =  $this->config->item('er_email_bcc_list');
		if (empty($bcc_list ) || !is_array($bcc_list) ) throw new Error('Error in config. Param "er_email_list" not found or is wrong.');
		$bcc = implode(';', $bcc_list);
	
		$from = $this->config->item('er_email_from');
		if (empty($from )) throw new Error('Error in config. Param "er_email_from" not found');
		
		$param = 'er_'.$type.'_email_subject';
		$subject = $this->config->item($param);
		if (empty($from )) throw new Error('Error in config. Param "'.$param.'" not found');
		$subject = self::convert_config($subject, $date_begin, $date_end);

		$param = 'er_'.$type.'_email_body';
		$body = $this->config->item($param);
		if (empty($from )) throw new Error('Error in config. Param "'.$param.'" not found');
		$body = self::convert_config($body, $date_begin, $date_end);
		// echo $body.'<br';

		$param = 'er_mail_config';
		$email_config = $this->config->item($param);
		if (empty( $email_config )) throw new Error('Error in config. Param "'.$param.'" not found');
		
		$delete_file = $this->config->item('er_delete_file');
		// nothing check
		
    	// send email
    	// ******************************
    	$this->load->library('email');
		// $this->email->initialize($email_config);
		
		// echo Utils::prettyPrint($email_config, '$email_config').'<br>';

    	//$users = ['igirsuslov@gmail.com', 'adam@esprit.name'];
		//$users = ['yug@800.com.ua'];
		//$bcc = '';
		//$flagToSend = true;
		$log_msg .= 'Email list('.count($users).')=['.implode(', ', $users).']'.PHP_EOL;
		$log_msg .= 'bcc='.$bcc.PHP_EOL;
		$log_msg .= 'Отправка разрешена: '.(int)$flagToSend.PHP_EOL;
		echo Utils::prettyPrint($users, 'email list ('.count($users).')').'<br>';
		echo 'bcc='.$bcc.'<br>';
		
		$count_sended = 0;
    	foreach ($users as $user) {
		        $this->email->clear(TRUE);

		        $this->email->from($from );
		        $this->email->to( $user );
		        $this->email->bcc($bcc);
		        $this->email->subject($subject);
		        $this->email->message($body);
				
				$this->email->attach($fileName);

		    	// echo $user_send.'<br><br>';
				if ($flagToSend === true) {
			    	if( $this->email->send() ){
			    		echo '<div>email send '.$user.'</div`>';
						$count_sended++;						
			    	} else {
			    		echo '<div style="color:red;">error send '.$user.'</div>';
						// echo phpinfo();
						show_error($this->email->print_debugger());
						
			    	}
			        // sleep(1);
				}
				
			usleep(500000);

		} // foreach

    	// send mail		
    	
		if ($delete_file === true) unlink($fileName);
		
		if ($flagToSend == false) {
			log_message('info', 'Excel_report: No Email\'s was sent');
			echo "<h4>No Email's was sent</h4>";
		}
		
		$log_msg .= 'Успешно отправлено '.$count_sended.' сообщений.'.PHP_EOL;
		$log_msg .= '------------------------------------------------------------------------'.PHP_EOL;
		file_put_contents($log_file, $log_msg, FILE_APPEND);
    	return; 	
    } // send_email_excel
	
	
	// ****************************************************************************
	// EXCEL BUILDING
	// ****************************************************************************
	
	private static function build_dataBar( $ref ) {
		
		$conditional = new Conditional();
		$conditional->setConditionType(Conditional::CONDITION_DATABAR);
		$conditional->setDataBar(new ConditionalDataBar());
		$conditional->getDataBar()
					->setMinimumConditionalFormatValueObject(new ConditionalFormatValueObject('min'))
					->setMaximumConditionalFormatValueObject(new ConditionalFormatValueObject('max'))
					->setColor('FF638EC6');
		$ext = $conditional
			->getDataBar()
			->setConditionalFormattingRuleExt(new ConditionalFormattingRuleExtension())
			->getConditionalFormattingRuleExt();

		$ext->setCfRule('dataBar');
		$ext->setSqref( $ref ); // target CellCoordinates
		$ext->setDataBarExt(new ConditionalDataBarExtension());
		$ext->getDataBarExt()
			->setMinimumConditionalFormatValueObject(new ConditionalFormatValueObject('autoMin'))
			->setMaximumConditionalFormatValueObject(new ConditionalFormatValueObject('autoMax'))
			->setMinLength(0)
			->setMaxLength(100)
			->setGradient(false)
			->setBorder(false)
			//->setDirection('rightToLeft')
			->setDirection('leftToRight')
			->setNegativeBarBorderColorSameAsPositive(false)
			->setBorderColor('FFFF0000')
			->setNegativeFillColor('FFFF0000')
			->setNegativeBorderColor('FFFF0000')
			->setAxisColor('FF000000');
		
		return [ $conditional ];
	} // build_dataBar
	
	private static function build_colorScale( $ref ) {
		$conditional = new Conditional();
		$conditional->setConditionType(Conditional::CONDITION_COLORSCALE);
		$conditional->setColorScale(new ConditionalColorScale());

		$conditional->getColorScale()
					->setMinimumConditionalFormatValueObject(new ConditionalFormatValueObject('min'))
					->setMaximumConditionalFormatValueObject(new ConditionalFormatValueObject('max'))
					->setColor('fef0f0')
					// FFFCFCFF
					->setColor2('FFF8696B');
		return [ $conditional ];					
	}
	
	private static function calc_total($TOTAL_NAME, &$arr) {
 		if (!is_array( $arr) ) echo Utils::prettyPrint( $arr, 'arr' ) ;
 		$total = 0;
 		foreach ($arr as $value) {
 			if (is_array($value)) {
 				foreach($value as $value1) $total += $value1;
 			} else {
 				$total += $value;
 			}
 		}
 		$arr[ $TOTAL_NAME ] = $total;
 	} // calc_total
	
	private static function print_total($sheet, $h_fileds, $row, $total) {

		foreach ($total as $key => $value) {
			if (is_array($value)) {
				foreach($value as $key1 => $value1) {
					$col = $h_fileds[ $key1 ];
					$sheet->setCellValueByColumnAndRow($col, $row, $value1);
				}
			} else {
				$col = $h_fileds[ $key ];
				$sheet->setCellValueByColumnAndRow($col, $row, $value);
			}
		}

	} // print_total

	private function build_excel_report( $options = array() ) {

 		$data = $options['data'];
 		if ( empty($data) ) {
 			echo "Error MCD Excel Report. Data is empty<br>";
 			return false;
 		}

        //****************************************************************
 		// data prepare
		
		$REQUEST_TYPE_ALLOW = ["Скарги - Ресторан", "Скарги - МакДрайв", "Скарги - Доставка Glovo", "Скарги - Доставка Raketa"];
		$REQUEST_TYPE_NAME = "Тип звернення";
		
		$SKIP_NAME = "Пропущено";
		
 		$TOTAL_NAME = 'ЗАГАЛОМ';
 		$V_GROUP = ['Операційний менеджер', 'Консультант', 'Ресторан'];
 		$H_GROUP = 'Розділ звернення';
 		
 		$H_GROUP_SPECIAL_VALUE = 'Якість їжі/напоїв';
 		$H_GROUP_SPECIAL_FIELD = 'Тема звернення';
 		
 		$T_DATA = '';
 		$start_col_h_fields = count($V_GROUP)+1;

 		$prepare_data = array();

 		foreach ($data[0] as $col_ind => $col_data) {
 			$fields[ $col_data ] = $col_ind;
 		}

 	 	//echo Utils::prettyPrint($fields, '$fields');
		// echo '<hr>';

		// filling group data
		// **********************************************************
		$group_data = array();
		$h_fileds = array();
		$h_fileds_special = array();
		$data_sheet_data = array();
 		foreach ($data as $row_ind => $row_data) {
 			if ( $row_ind == 0 ) {
				$data_sheet_data[] = $row_data;
				continue;
			}
			
			// skip all empty records
			// skip all records where
			$flag_continue = false;
			foreach($V_GROUP as $v_item) {
				if ( empty( $row_data[ $fields[$v_item] ] )) $flag_continue = true;
			}
			if ( empty( $row_data[ $fields[$H_GROUP] ] )) {
				$flag_continue = true;
			} else {
				if ( stripos($row_data[ $fields[$H_GROUP] ], 'не підтверджено') !== false ) $flag_continue = true;
			}
			
			// request type check
			// *************************************
			if ( !in_array( $row_data[ $fields[$REQUEST_TYPE_NAME] ],  $REQUEST_TYPE_ALLOW ) ) $flag_continue = true;
			
			if ($flag_continue) { 
			  // $data[$row_ind][] = '1';
			  continue;
			}
			// array_key_last($data[$row_ind])
			$data_sheet_data[] = $row_data;
			// $data[$row_ind][] = '0';
			
 			$h_value = $row_data[ $fields[$H_GROUP] ];
 			$h_fileds [ $h_value ] = 1;
 			// echo $h_value.'<br>';  
 			$item = &$group_data;
 			foreach($V_GROUP as $v_item) {
 				$v_value = $row_data[ $fields[$v_item] ];
 				if ( !isset($item[$v_value]) ) $item[$v_value] = array();
 				$item = &$item[$v_value];
 			} // V_GROUP

// add group by special field
// @version 2022-01-12

 			if ($H_GROUP_SPECIAL_VALUE == $h_value) {
 				if ( !isset( $item[ $h_value ] ) ) $item[ $h_value ] = [];
 				
 				$h_value_add = $row_data[ $fields[$H_GROUP_SPECIAL_FIELD] ];
 				if (empty($h_value_add)) $h_value_add = 'не вказано';
 				if ( !isset( $item[ $h_value ][ $h_value_add ] ) ) $item[ $h_value ][ $h_value_add ]= 0;
 				$item[ $h_value ][ $h_value_add ]++;

 				$h_fileds_special[ $h_value_add ] = 1;

 			} else {
 				
 				if ( !isset( $item[ $h_value ] ) ) $item[ $h_value ] = 0;
 				$item[ $h_value ]++;

 			}

 		} // foreach
		
		$end_col_h_fields = $start_col_h_fields+count($h_fileds)+
				(count($h_fileds_special) == 0 ? 0 : count($h_fileds_special)-1);

		// echo Utils::prettyPrint($group_data, '$group_data');
		// echo Utils::prettyPrint($h_fileds_special, '$h_fileds_special');
 		// sorting group data
 		// **********************************************************
 		ksort($group_data);
		
		//echo Utils::prettyPrint($group_data, '$group_data');
 		
 		ksort($h_fileds);

		// add special fields
		$temp = [];
		$h_group_special_col = 1; // column number for special
		$h_group_special_count = count($h_fileds_special);
		$col=0;
		foreach ($h_fileds as $key => $value) {
			$col++;
			if ($key == $H_GROUP_SPECIAL_VALUE) {
				// echo $col.'<br>'.$start_col_h_fields.'<br>';
				$h_group_special_col = $col+$start_col_h_fields-1;
				foreach($h_fileds_special as $key1 => $value1) $temp[ $key1 ] = $h_fileds_special[ $key1 ];
			} else {
				$temp[$key]=$h_fileds[$key];
			}
		} // foreach
		$h_fileds = $temp;

 		$h_fileds[$TOTAL_NAME] = 1;
 		$col = $start_col_h_fields;
 		// create index of column for h_fields
 		foreach ($h_fileds as $key => $value) {
			$h_fileds[ $key ] = $col;
 			$col++; 	
 		} // foreach
		
		// echo Utils::prettyPrint($h_fileds, '$h_fileds');

 		foreach($group_data as &$lvl_1) {
 			ksort($lvl_1);
 			foreach ($lvl_1 as &$lvl_2) {

 				// sorting by number of the sale point
 				uksort($lvl_2, function($a, $b) {
 					if ($a == $b) return 0;
 					$key_a = get_strings_between($a, '№', ',');
 					$key_b = get_strings_between($b, '№', ',');
 					if ( count($key_a) != 1 || count($key_b) != 1) return $a > $b;
 					if ( (int)$key_a[0] > (int)$key_b[0] ) return 1;
 				});

 			} // foreach
 		}; // foreach
		
		// echo Utils::prettyPrint($group_data, '$group_data');
		
 		// calculate totals
 		// *********************************************************
 		$total_0 = array();

 		foreach($group_data as &$lvl_1) {
 			
 			$total_1 = array();
 			foreach ($lvl_1 as &$lvl_2) {
 				
 				$total_2 = array();
 				foreach ($lvl_2 as $key_3 => $lvl_3) {

 					foreach ($lvl_3 as $key_4 => $lvl_4) {

 						if ( is_array($lvl_4) ) {
 							foreach ($lvl_4 as $key_5 => $lvl_5) {
	 							if ( !isset($total_0[ $key_5 ]) ) $total_0[ $key_5 ] = 0;
	 							if ( !isset($total_1[ $key_5 ]) ) $total_1[ $key_5 ] = 0;
	 							if ( !isset($total_2[ $key_5 ]) ) $total_2[ $key_5 ] = 0;
	 							$total_0[ $key_5 ] += $lvl_5;
	 							$total_1[ $key_5 ] += $lvl_5;
	 							$total_2[ $key_5 ] += $lvl_5;
 							}
 						} else {

 							if ( !isset($total_0[ $key_4 ]) ) $total_0[ $key_4 ] = 0;
 							if ( !isset($total_1[ $key_4 ]) ) $total_1[ $key_4 ] = 0;
 							if ( !isset($total_2[ $key_4 ]) ) $total_2[ $key_4 ] = 0;
 							$total_0[ $key_4 ] += $lvl_4;
 							$total_1[ $key_4 ] += $lvl_4;
 							$total_2[ $key_4 ] += $lvl_4;
 						}
 					} // // foreach lvl 4
 					
 				} // foreach lvl 3
 				self::calc_total($TOTAL_NAME, $total_2);
 				$lvl_2[ 'total' ] = $total_2;

 			} // foreach lvl 2
 			self::calc_total($TOTAL_NAME, $total_1);
 			$lvl_1[ 'total' ] = $total_1;

 		} // foreach lvl 1
		unset($lvl_1);
		unset($lvl_2);
 		self::calc_total($TOTAL_NAME, $total_0);
		$group_data[ 'total' ] = $total_0;

 	// 	echo Utils::prettyPrint($h_fileds, '$h_fileds');
		// echo '<hr>';

 	 	//echo Utils::prettyPrint($group_data, '$group_data1');
		// echo '<hr>';

 		// end of data prepare


		//echo Utils::prettyPrint(array_merge(array_slice($data,0,10), [['...']]), 'read data');
		//echo '<hr>';

		$spreadsheet = new Spreadsheet();

		// Set document properties
		$spreadsheet->getProperties()->setCreator('MCD EXCEL REPORT')
		    ->setLastModifiedBy('MCD EXCEL REPORT')
		    ->setTitle('Office 2007 XLSX MCD EXCEL REPORT')
		    ->setSubject('Office 2007 XLSX MCD EXCEL REPORT')
		    ->setDescription('MCD REPORT, generated using MCD EXCEL REPORT.')
		    ->setKeywords('office 2007 openxml php')
		    ->setCategory('MCD EXCEL REPORT');

		$sheet = $spreadsheet->getActiveSheet();    
		$sheet->setTitle('Зведені дані');


		$data_sheet = $spreadsheet->addSheet(new Worksheet($spreadsheet));
		$data_sheet->setTitle('Список звернень');   


		// [START] write SOURCE data
		// **********************************************************
		
		// $data_sheet->fromArray($data);
		$data_sheet->fromArray($data_sheet_data);
		$source_total_col = isset( $data[0] ) ? count($data[0]) : 1;

		// header format
		$styleArray_header = [
		    'font' => [
		        'bold' => true,
		    ],
		    'alignment' => [
		        'horizontal' => PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		        'vertical' => PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
		        'wrapText' => true
		    ],
		    'borders' => [
		        'top' => [
		            'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		    'fill' => [
		        'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
		        'rotation' => 90,
		        'startColor' => [
		            'rgb' => 'fce5cd',
		        ],
		        'endColor' => [
		            'rgb' => 'ffc000',
		        ],
		    ],
		];
		$styleArray_header_special = [
		    'font' => [
		        'bold' => true,
		    ],
		    'alignment' => [
		        'horizontal' => PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		        'vertical' => PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
		        'wrapText' => true
		    ],
		    'borders' => [
		        'top' => [
		            'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		    'fill' => [
		        'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
		        'rotation' => 90,
		        'startColor' => [
		            'rgb' => 'fff700',
		        ],
		        'endColor' => [
		            'rgb' => 'ffff00',
		        ],
		    ],
		];

		$data_sheet->getStyleByColumnAndRow(1, 1, $source_total_col, 1)->applyFromArray($styleArray_header);
		for($i=1; $i<$source_total_col; $i++) {
			if ($data[0][$i-1] == 'Суть звернення (текстовий опис)') {
				$data_sheet->getColumnDimension( getColumnChr($i) )->setWidth(120);
			} else {
				$data_sheet->getColumnDimension( getColumnChr($i) )->setAutoSize(true);
			}
		}
		
		$data_sheet->freezePane('A2');

		// [END] write SOURCE data
		// **********************************************************



		// [START] write GROUP data
		// **********************************************************

		// write $h_fileds as header from column 3
		$h_fileds_width = 10;

		$col = $start_col_h_fields;
		$row = 1;
		
		//temp
		//echo Utils::prettyPrint($group_data, '$group_data1__');
		// echo '$h_group_special_count='.$h_group_special_count.'<br>';
		$sheet->setCellValueByColumnAndRow($h_group_special_col, $row, $H_GROUP_SPECIAL_VALUE);
		$sheet->mergeCells(getColumnChr($h_group_special_col).$row.':'.
			               getColumnChr($h_group_special_col+$h_group_special_count-1).$row);
		$sheet->mergeCells(getColumnChr(1).$row.':'.getColumnChr($start_col_h_fields-1).($row+1));	
		
		unset( $key ); unset( $item );
		foreach ($h_fileds as $key => $item) {
			//$key = preg_replace("/\(.*\)/", "", $key);
			if ( $col >=  $h_group_special_col && $col < $h_group_special_col+$h_group_special_count ) {
				$sheet->setCellValueByColumnAndRow($col, $row+1, $key);
			} else {
				$sheet->setCellValueByColumnAndRow($col, $row, $key);
				$sheet->mergeCells(getColumnChr($col).$row.':'.getColumnChr($col).($row+1));
			}
			//echo  getColumnChr($col).'<br>';
			$sheet->getColumnDimension( getColumnChr($col) )->setWidth($h_fileds_width);
			$col++;
		} // foreach
		$row++;
		//temp
		// echo Utils::prettyPrint($group_data, '$group_data2');

		// total column
		$total_col = $col-1;
		//$sheet->setCellValueByColumnAndRow($col, $row, 'ЗАГАЛОМ');
		$sheet->getColumnDimension( getColumnChr($col) )->setWidth($h_fileds_width);

		$sheet->getStyleByColumnAndRow(1,1,$total_col ,2)->applyFromArray($styleArray_header);
		$sheet->getStyleByColumnAndRow($h_group_special_col,1,
			                           $h_group_special_col+$h_group_special_count-1 ,2)
					->applyFromArray($styleArray_header_special);

		$sheet->getStyleByColumnAndRow($total_col, 1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0070c0');
		
		// body format
		$styleArray_0 = [
		    'font' => [
		        'bold' => true,
		    ],
		    'fill' => [
		        'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
		        'startColor' => [
		            'rgb' => 'ffcc66',
		        ],
		    ],
		];	
		$styleArray_1 = [
		    'font' => [
		        'bold' => true,
		    ],
		    'fill' => [
		        'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
		        'startColor' => [
		            'rgb' => 'ffff99',
		        ],
		    ],
		];	
		$styleArray_lastColumn = [
		    'fill' => [
		        'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
		        'startColor' => [
		            'rgb' => 'b8cce4',
		        ],
		    ],
		];	
		
		//echo Utils::prettyPrint($styleArray_1, 'styleArray_1');  
		
		// writing data
		// ***************************************************************

		
		$row++;
		//echo Utils::prettyPrint($group_data, '$group_data2');
		$lvl = 1;
		foreach ($group_data as $key_0 => $item_0) {
			if ($key_0 == 'total') continue;
			
			$col = 1;
 			$sheet->setCellValueByColumnAndRow($col, $row, $key_0);
			// $sheet->getRowDimension($row)->setOutlineLevel($lvl)->setVisible(true)->setCollapsed(false);
			
 			$sheet->getStyleByColumnAndRow(1,$row,$total_col ,$row)->applyFromArray($styleArray_0);
 			self::print_total($sheet, $h_fileds, $row, $item_0['total']);

 			$col++;
 			$row++;
			foreach ($item_0 as $key_1 => $item_1) {
				if ($key_1 == 'total') continue;
				
				$col=2;
				$sheet->setCellValueByColumnAndRow($col, $row, $key_1);
				$sheet->getRowDimension($row)->setOutlineLevel($lvl)->setVisible(true)->setCollapsed(false);
				$sheet->getStyleByColumnAndRow(1,$row,$total_col ,$row)->applyFromArray($styleArray_1);
				self::print_total($sheet, $h_fileds, $row, $item_1['total']);

				$row++;
				$cc_startRow = $row;
				$col=3;
				foreach ($item_1 as $key_2 => $item_2) {
					if ($key_2 == 'total') continue;

					$sheet->setCellValueByColumnAndRow($col, $row, $key_2);
					$sheet->getRowDimension($row)->setOutlineLevel($lvl)->setVisible(true)->setCollapsed(false);
					// if ( !is_array( $item_2 ) ) echo Utils::prettyPrint($item_1, '$item_1');
					self::calc_total($TOTAL_NAME, $item_2); 
					self::print_total($sheet, $h_fileds, $row, $item_2);
					$row++;

				} // foreach lvl2
				
				// last column format
				// ****************************************************
				$ref = getColumnChr($end_col_h_fields).$cc_startRow.':'.getColumnChr($end_col_h_fields).($row-1);
				$sheet->getStyle($ref)->applyFromArray($styleArray_lastColumn);
				
				$conditionalStyles = self::build_dataBar( $ref );
				$sheet->getStyle( $ref )->setConditionalStyles( $conditionalStyles );
				
				// colorScale format
				// ****************************************************
				$ref =  getColumnChr($start_col_h_fields).$cc_startRow.':'.getColumnChr($end_col_h_fields-1).($row-1);
				// 'D4:T10';
				$conditionalStyles = self::build_colorScale( $ref );
				$sheet->getStyle( $ref )->setConditionalStyles( $conditionalStyles );
			
			} // foreach lvl1
			
			// $lvl++;
		} // foreach lvl0
		
		// $ref = 'U4:U10';
		// $conditionalStyles = $this->build_dataBar( $ref );
		// $sheet->getStyle( $ref )->setConditionalStyles( $conditionalStyles );

		// $ref = 'D4:T10';
		// $conditionalStyles2 = $this->build_colorScale( $ref );
		// $sheet->getStyle( $ref )->setConditionalStyles( $conditionalStyles2 );
		
		// writing last row total
		// ********************************************************
		$sheet->setCellValueByColumnAndRow(1, $row, $TOTAL_NAME);
		$styleArray = [
		    'font' => [
		        'bold' => true,
		    ],
		    'fill' => [
		        'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
		        'startColor' => [
		            'rgb' => 'ffc000',
		        ],
		    ],
		];	
		$sheet->getStyleByColumnAndRow(1,$row,$total_col ,$row)->applyFromArray($styleArray);
		$sheet->getStyleByColumnAndRow($total_col, $row)->getFill()->setFillType(PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0070c0');
		self::print_total($sheet, $h_fileds, $row, $group_data['total']);


		// formatting column width
		// ********************************************************

		// column width
		$group_with = 4;
		$sheet->getColumnDimension('A')->setWidth($group_with);
		$sheet->getColumnDimension('B')->setWidth($group_with);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->freezePane('A3');
		// no sammary below
		$sheet->setShowSummaryBelow(false);

		$spreadsheet->setActiveSheetIndex(0);

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		
		if ( !empty($options['filename']) ) {

			$writer->save( $options['filename'] );
			echo '<b>"'.$options['filename'].'" was created and saved.'.'</b><br>';

		} else {

			// Redirect output to a client’s web browser (Xlsx)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="mcd_report.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0
			
			$writer->save( 'php://output' );
			//exit;
		}


	} // build_excel_report	

} // Excel_report

function getColumnChr( $col ) {
	$firstChr = '';
	if ( $col > 26 ) {
		$firstChr = 'A';
		$col -= 26;
	}
	$char = $firstChr.chr(65+$col-1);
	return $char;
} // getColumnChr

function get_strings_between(string $string, string $start, string $end): array {
    $r = [];
    $startLen = strlen($start);
    $endLen = strlen($end);
    while (!empty($string)) {
        $startPosition = strpos($string, $start);
        if ($startPosition === false) {
            return $r;
        }
        $contentStart = $startPosition + $startLen;
        $contentEnd = strpos($string, $end, $contentStart);
        $len = $contentEnd - $contentStart;
        $r[] = substr($string, $contentStart, $len);
        $endPosition = $contentEnd + $endLen;
        $string = substr($string, $endPosition);
    }
    return $r;
} // get_strings_between

/*
		$login = 'CCalesya.mudzhyri';
		$role = 'Офіс';
		$id = 'C3770725-5406-EB11-81C0-00155D1F050B';

		$_POST['date_start'] = '07.06.2021';
		$_POST['date_end'] = '08.06.2021';

		$user = Users::setRole($login, $role, $id);
		$app = new Appeals();
*/
		// $data['appeals'] = $app->getListOfAppeals($user, date("Ymd H:i:s", strtotime($_POST['date_start'])), date("Ymd H:i:s", strtotime($_POST['date_end'])));
		//$data['appeals'] = $app->getListOfAppeals($user);
		// echo Utils::prettyPrint( $data['appeals'], 'appeals' );
		// echo "hello world!".'<br>';
		// echo  '__DIR__'.__DIR__.'<br>';
/*
		$filename_src = __DIR__ . '\template\source_mcd.xlsx';
		$filename_src_json = __DIR__ . '\template\source_mcd.json';

		if ( !file_exists($filename_src)) {
			echo 'Error: not file exist:  '.$filename_src;
			return;
		}
		
		if ( file_exists($filename_src_json) && false) {
			
			$data_str = file_get_contents($filename_src_json);
			$data = json_decode( $data_str, true );
			
		} else {
			$spreadsheet_src = IOFactory::load($filename_src);
			
			$cells = $spreadsheet_src->getSheetByName('Список звернень')->getCellCollection();
			 
			$data = array();
			for ($row = 1; $row <= $cells->getHighestRow(); $row++) {
				//echo 'row: '.$row.'<br>';
				$data_row = array();
				for ($col = 'A'; $col <= 'L'; $col++) {
					// Так можно получить значение конкретной ячейки
					$val = $cells->get($col.$row)->getValue();

					//echo $col.': '.$val.'<br>';
					$data_row[] = $val;
				}
				$data[] = $data_row;
			} 

			file_put_contents( __DIR__ . '\template\source_mcd.json', json_encode($data));
		}
*/
		//echo Utils::prettyPrint( $data, '$data' );



?>
