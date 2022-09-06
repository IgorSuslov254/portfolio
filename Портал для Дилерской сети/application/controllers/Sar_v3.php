<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define("SAR_URL", "sar_v3");

	/** * 
	 * @author aws
	 * @version 3 2021-08-24
	 */


	use PhpOffice\PhpSpreadsheet\Chart\Chart;
	use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
	use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
	use PhpOffice\PhpSpreadsheet\Chart\Legend;
	use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
	use PhpOffice\PhpSpreadsheet\Chart\Title;
	use PhpOffice\PhpSpreadsheet\Chart\Layout;

	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
	use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
	use PhpOffice\PhpSpreadsheet\Style\Font;

class Sar_v3 extends Admin_Controller{
	
	//include 'Sar_models_report.php';

	private $cars_data = array();
	private $cars_data_byName = array();
	private $dealer = null;
	private $account_id;

	const TEST_DEALER = "a6ba10c3-bb1c-ea11-81b3-00155d1f050b";
	const VIEW_TMPL = 'sar/sar_view_v3';

	const ORGANIZATIONS = ['citroen', 'peugeot', 'opel', 'ds'];
	const SAR = array(

		array('value' => 'SAR_showroom_traffic',
			  'name' => 'Showroom traffic (відвідуваність автосалону) - к-ть покупців, які зайшли в салон та погодились на консультацію. Внесіть числові значення',
	          'name_excel' => "Showroom traffic (посещаемость автосалона) – кол-во зашедших в салон покупателей, которые согласились на консультацию')",
	          'updatable' => true),

		array('value' => 'SAR_leads',
			 /* 'name' => "Lead (потенційний покупець) - погодився залишити контактну інформацію (анкета ТД, звіт по роботі з клієнтом, консультація по телефону і т.д.). Автоматично рахується к-ть створених дилером лідів на порталі, а також отриманих онлайн лідів. Можна редагувати значення (тільки збільшення к-ті)",*/

              'name' => "Lead (потенційний покупець) - погодився залишити контактну інформацію (анкета ТД, звіт по роботі з клієнтом, консультація по телефону і т.д.). Внесіть числові значення",

	          'name_excel' => "Lead (потенциальный покупатель) – покупатель, который согласился оставить контактную информацию (анкета ТД, отчет по работе с клиентом, консультация по телефону и т.д.)",
	          'updatable' => true),

		array('value' => 'SAR_testdrive',
			  'name' => "Test Drive (тест драйв) - к-ть лідів, в яких відмічено “Пройдено ТД” = “Так” по даті проходження тест-драйву",
	          'name_excel' => "Test Drive (тест драйв) – кол-во проведенных тест драйвов (TD)",
	          'updatable' => false),

		array('value' => 'SAR_offer',
			  'name'=> "Offer (укладений контракт) - к-ть створених контрактів в розділі “Контракти” по даті контракту",
	          'name_excel' => "Offer (заключенный контракт) – кол-во заключенных контрактов (СС)",
	          'updatable' => false),

		array('value' => 'SAR_sale',
			  'name' => "Sale (продаж) - к-ть проданих автомобілів, які проведені в системі ABCnet",
	          'name_excel' => "Sale (продажа) – кол-во проданных автомобилей, которые проведены в системе ABCnet",
	          'updatable' => false)

		);

	const SAR_MODELS_REPORT = array(
		array( 'value' => 'SHR', 'name' => 'SHR Traffic'),
		array( 'value' => 'CC', 'name' => 'CC' ),
		array( 'value' => 'CR', 'name' => 'Closing Ratio')
	);

	const SAR_API_PARAM = array(
		'SAR_showroom_traffic' => array(
									'updatable' => true,
	      	  					    'EntityName' => 'new_showroom_traffic_details',
	      	  					    'Lookups_EntityName' => 'new_showroom_traffic',
	      	  					    'data_loader' => 'showroom_traffic'
	      							),
		'SAR_leads' => array(
							'updatable' => true,
	      	    			'EntityName' => 'new_lead_sar_detail',
	      	    			'Lookups_EntityName' => 'new_lead_sar',
	      	    			'data_loader' => 'leads'
	      				),
		'SAR_testdrive' => array( 'updatable' => false ),
		'SAR_offer' => array( 'updatable' => false ),
		'SAR_sale' => array( 'updatable' => false )

	);


	public function __construct() {

	    parent::__construct();
        
        // before dancing needs to get car reference
		// ***************************************
		$this->load_cars_data();

		/*
        $cars = CRM::get_cars();
		foreach ( $cars['Result']['Data'] as $car ) {
			$this->cars_data[ $car['Su_carcomplId'] ] = $car;
			$this->cars_data_byName[ $car['Su_name'] ] = $car;
		} // foreach
*/

		// setting dealer
		$this->load->model('Exel_model');
		if (isset($_SESSION['user_id'])) $user_info = $this->Exel_model->get_user_info();

		//echo Utils::prettyPrint($_SESSION, '$_SESSION');

		$this->user_info = $user_info;
		if ( isset($user_info) ) {
			
			$this->account_id = $user_info[0]['company'];
			if (isset($_SESSION['access_employees']) && $_SESSION['access_employees'] !== true) {
				//$this->dealer = $user_info[0]['web_userid'];
				$this->dealer = $user_info[0]['company'];
			} // if

		} // if

		// session  
		$this->load->library('session');
		// Utils 
		$this->load->library('Utils');
		// config
		$this->config->load('sar_config');
    } // __construct
	
	/*************************************************************
	  GETTING DATA FROM API
	***********************************************************/
	private function error( $options = array() ) {
	
		$this->data['error'] = true;
		if ( isset($options['error_code']) ) $this->data['error_code'] = $options['error_code'];
		if ( isset($options['error_description']) ) $this->data['error_description'] = $options['error_description'];

		$this->template->admin_render(self::VIEW_TMPL, $this->data);
	} // error

	public function index() {

		// $this->loadCars();
		$t0 = microtime(true);
		// convert form data to internal format
		$period_start = $this->input->post('period_start');
		$selected_dealer = $this->input->post('selected_dealer');
		echo "<script>console.log(`selected_dealer from post= `,`". json_encode($selected_dealer) ."`);</script>";

		if( !isset( $period_start ) ) {
			if ( isset($this->session->SAR_period_start) ) {
				$period_start = $this->session->SAR_period_start;
			} else {
				$period_start = date('Y-m-d');
			}
		}
		$this->session->SAR_period_start = $period_start;

		if( !isset( $selected_dealer ) ) {
			if ( isset($this->session->selected_dealer) ) {
				$selected_dealer = $this->session->selected_dealer;
			} else {
				$selected_dealer = "";
			}
		}
		$this->session->selected_dealer = $selected_dealer;

		if ($selected_dealer != "" && is_null($this->dealer) ) $this->dealer = $selected_dealer;

		// return back period_start
		$this->data['SAR_period_start'] = $period_start;
		$this->data['SAR_selected_dealer'] = $selected_dealer;
		$this->data['SAR_index'] = self::SAR;

		$period_start_arr = explode('-', $period_start);
		$options = array( 'month' => $period_start_arr[1], 'year' => $period_start_arr[0], 'day' => $period_start_arr[2]);
		if ( !is_null($this->dealer) ) $options['dealer'] = $this->dealer;
	    //echo "<script>console.log(`". json_encode( $options, true) ."`);</script>";
		
		if ( (isset($_SESSION['access_employees']) && $_SESSION['access_employees'] === true)) {
			$this->data['SAR_dealers'] = $this->load_dealers();
		}	    

		// load data to $this->data
		$t1 = microtime(true);
		$this->load_data( $options );
		$t2 = microtime(true);
	    //$this->load_models_report_data( $options );
		$t3 = microtime(true);
		
		echo "<script>console.log(`t1-t0= `,`". ($t1-$t0) ."`);</script>";
		echo "<script>console.log(`t2-t1= `,`". ($t2-$t1) ."`);</script>";
		echo "<script>console.log(`t3-t2= `,`". ($t3-$t2) ."`);</script>";


		foreach($this->data as $index => $item) {
			$tmpl1 = 'SAR';
			$tmpl2 = 'SAR_MR';
			//$tmpl1 = 'SAR_offer';
			// if (substr($index, 0, strlen($tmpl1)) == $tmpl1) echo "<script>console.log(`".$index."= `,`". json_encode($item) ."`);</script>";
			if (substr($index, 0, strlen($tmpl2)) == $tmpl2) echo "<script>console.log(`".$index."= `,`". json_encode($item) ."`);</script>";
		} // foreach
		
		$this->template->admin_render(self::VIEW_TMPL, $this->data);
	} // index
	
	// for ajax call
	// **************************************************
	public function get_models_report_data() {
	
		$period_start = $this->input->post('period_start');
		$selected_dealer = $this->input->post('selected_dealer');
		//echo "<script>console.log(`selected_dealer from post= `,`". json_encode($selected_dealer) ."`);</script>";

		if( !isset( $period_start ) ) {
			if ( isset($this->session->SAR_period_start) ) {
				$period_start = $this->session->SAR_period_start;
			} else {
				$period_start = date('Y-m-d');
			}
		}

		if( !isset( $selected_dealer ) ) {
			if ( isset($this->session->selected_dealer) ) {
				$selected_dealer = $this->session->selected_dealer;
			} else {
				$selected_dealer = "";
			}
		}
		
		$this->session->selected_dealer = $selected_dealer;
		if ($selected_dealer != "" && is_null($this->dealer) ) $this->dealer = $selected_dealer;

		$period_start_arr = explode('-', $period_start);
		$options = array( 'month' => $period_start_arr[1], 'year' => $period_start_arr[0], 'day' => $period_start_arr[2]);
		if ( !is_null($this->dealer) ) $options['dealer'] = $this->dealer;
	    //echo "<script>console.log(`". json_encode( $options, true) ."`);</script>";
		
		// load data to $this->data
		$t1 = microtime(true);
		$this->load_data( $options );
		$this->load_models_report_data( $options );
		$t2 = microtime(true);
	    
		echo json_encode(array( 'SAR_MR_total' => $this->data['SAR_MR_total'],
								'SAR_MR_index' => $this->data['SAR_MR_index'],
								'opt' => $options),	JSON_UNESCAPED_UNICODE);

	} // get_models_report_data

	private function applyFilter($filter, $data) {
		$ret = array();
		foreach ($data as $key => $value) {
			$flagAdd = true;
			if ( isset($filter['fromCarsList']) && $filter['fromCarsList'] == true ) {
				if ( isset($value[0]) && $value[0]['fromCarsList'] != true ) $flagAdd = false;
			}

			if ( isset($filter['day']) ) {
				for ($i=(int)$filter['day']+1; $i<32; $i++) {
					$value[$i]['value'] = '';
				}	
			} // if

			if ($flagAdd) $ret[] = $value;
		} // foreach

		return $ret;
	} // applyFilter


	private function calc_diff_forItem( $type, &$item, $total = null, $noShare = false) {
		
		if ( isset( $item[$type]) ) {

			$current = (int)$item[$type]['current'];
			$prev = (int)$item[$type]['prev'];
			$prev_prev = (int)$item[$type]['prev_prev'];

			
			if ( is_nan($current) ) $current = 0;
			if ( is_nan($prev) ) $prev = 0;
			if ( is_nan($prev_prev) ) $prev_prev = 0;

			$diff = $current - $prev;
			if ( $prev == 0 ) {
				$diff_percent = '';
			} else {
				$diff_percent = round($current/$prev, 4);
			}
		
		} else {
			$diff = 0;
			$diff_percent = '';
		}	


		if ( is_null(  $total ) ) {
			$share = 1;
		} else {
			if ( $total == 0 ) {
				$share = 0;
			} else {
				$share = round($diff/$total, 4);
			}
		}

		// add to out array
		$item[$type]['diff'] = round($diff, 4);
		$item[$type]['diff_percent'] = $diff_percent;
		if ($noShare !== true) $item[$type]['share'] = $share;

		return $diff;

	} // calc_diff_forItem


	private function combine( $type, $field, $data, &$mr ) {
		
		foreach ($data as $item) {
			if ( isset( $item[0]) ) {

				$car_id = $item[0]['car_id'];

				if ( isset( $mr[ $car_id ] ) ) {
					if ( !isset($mr[ $car_id ][ $type ]) ) $mr[ $car_id ][ $type ] = array();
					if ( isset($item[32]['value']) ) {
						//$this->data['SAR_MR_LOG'] .=  $car_id.' '.$type.' '.$field.'\n';
						$mr[ $car_id ][ $type ][ $field ] = $item[32]['value'];
					}
				}

			} // if $item[0]

		} // foreach

	} // combine

	private function combine_total( $type, $field, $data, &$mr_total ) {
		
		$value = (int)$data[32];
		if ( is_nan($value) ) $value = 0;

		if ( !isset($mr_total[ $type ]) ) $mr_total[ $type ] = array();
		$mr_total[ $type ][ $field ] = $value;

	} // combine_total


	private function load_models_report_data( $options = array() ) {

		//$this->data['SAR_MR_LOG'] = '';
		//$MR_INDEX_FORMAT = 'd_F';
		$MR_INDEX_FORMAT = 'F_d';

		$day = $options['day'];
		if ( !isset($day) ) {
			$day = Date('d');
			$options['day'] = $day;
		}

		$isLastDayOfMonth = cal_days_in_month(CAL_GREGORIAN, $options['month'], $options['year']) == $day;

		$filter = array('day' => $day, 'fromCarsList' => true);
		$this->data['SAR_MR_index'] = array();

		// current month
		// ***********************************************************
		if ( isset($this->data['SAR_showroom_traffic']) && isset($this->data['SAR_showroom_traffic_total']) ) {
			$shr_current = $this->data['SAR_showroom_traffic'];
			$shr_current_total = $this->data['SAR_showroom_traffic_total'];
			//$this->data['SAR_MR_loadcurrent_shr'] = false;
			// echo 'use calced<br>';
		} else {
			$shr_current = $this->showroom_traffic( $options );
			$shr_current = $this->applyFilter( $filter,  $shr_current);
			$shr_current_total = $this->getTotal( $shr_current );
			//$this->data['SAR_MR_loadcurrent_shr'] = true;
		}
		
		// if ( isset($this->data['SAR_sale']) && isset($this->data['SAR_sale_total']) ) {
		// 	$cc_current = $this->data['SAR_sale'];
		// 	$cc_current_total = $this->data['SAR_sale_total'];
		// } else {
		// 	$cc_current = $this->sale( $options );
		// 	$cc_current_total = $this->getTotal( $cc_current );
		// }

		if ( isset($this->data['SAR_offer']) && isset($this->data['SAR_offer_total']) ) {
			$cc_current = $this->data['SAR_offer'];
			$cc_current_total = $this->data['SAR_offer_total'];
		} else {
			$cc_current = $this->offer( $options );
			$cc_current = $this->applyFilter( $filter,  $cc_current);
			$cc_current_total = $this->getTotal( $cc_current );
		}

		if ( isset($this->data['SAR_offer']) && isset($this->data['SAR_offer_total']) ) {
			$offer_current = $this->data['SAR_offer'];
			$offer_current_total = $this->data['SAR_offer_total'];
		} else {
			$offer_current = $cc_current;
			$offer_current_total = $cc_current_total;
		}

		$this->data['SAR_MR_index']['current'] = date($MR_INDEX_FORMAT, strtotime( $options['year'].'-'.$options['month'].'-'.$day) );
		
		// previous month
		// ***********************************************************
		if ($options['month'] == 1) {
			$options['month'] = 12;
			$options['year'] = (int)$options['year'] - 1;
		} else {
			$options['month'] = (int)$options['month'] - 1;
		}
		$options['day'] = $day;
		if ($isLastDayOfMonth) $options['day'] = cal_days_in_month(CAL_GREGORIAN, $options['month'], $options['year']);
		$this->data['SAR_MR_options_prev'] = $options;
		$filter['day'] = $options['day'];

		$shr_prev = $this->showroom_traffic( $options );
		$shr_prev = $this->applyFilter( $filter,  $shr_prev);
		$shr_prev_total = $this->getTotal( $shr_prev );

		//$cc_prev = $this->sale( $options );
		$cc_prev = $this->offer( $options );
		$cc_prev = $this->applyFilter( $filter, $cc_prev);
		$cc_prev_total = $this->getTotal( $cc_prev );

		$offer_prev = $this->offer( $options );
		$offer_prev = $this->applyFilter( $filter,  $offer_prev);
		$offer_prev_total = $this->getTotal( $offer_prev );

		$print_day = min($options['day'], cal_days_in_month(CAL_GREGORIAN, $options['month'], $options['year']));
		$this->data['SAR_MR_index']['prev'] = date($MR_INDEX_FORMAT, strtotime( $options['year'].'-'.$options['month'].'-'.$print_day));

		// previous of previous month
		// ***********************************************************
		if ($options['month'] == 1) {
			$options['month'] = 12;
			$options['year'] = (int)$options['year'] - 1;
		} else {
			$options['month'] = (int)$options['month'] - 1;
		}
		$options['day'] = $day;
		if ($isLastDayOfMonth) $options['day'] = cal_days_in_month(CAL_GREGORIAN, $options['month'], $options['year']);
		$this->data['SAR_MR_options_prev_prev'] = $options;
		$filter['day'] = $options['day'];
		
		$shr_prev_prev = $this->showroom_traffic( $options );
		$shr_prev_prev = $this->applyFilter( $filter, $shr_prev_prev);
		$shr_prev_prev_total = $this->getTotal( $shr_prev_prev );
		
		//$cc_prev_prev = $this->sale( $options );
		$cc_prev_prev = $this->offer( $options );
		$cc_prev_prev = $this->applyFilter( $filter, $cc_prev_prev);
		$cc_prev_prev_total = $this->getTotal( $cc_prev_prev );	

		$offer_prev_prev = $this->offer( $options );
		$offer_prev_prev = $this->applyFilter( $filter, $offer_prev_prev);
		$offer_prev_prev_total = $this->getTotal( $offer_prev_prev );		

		$print_day = min($options['day'], cal_days_in_month(CAL_GREGORIAN, $options['month'], $options['year']));
		$this->data['SAR_MR_index']['prev_prev'] = date($MR_INDEX_FORMAT, strtotime( $options['year'].'-'.$options['month'].'-'.$print_day) );

		// result data

		//foreach () {

		//} // foreach

		//$this->data['SAR_MR_shr'] = $this->combine();

		$out_arr = $this->createOutArray();
		//echo Utils::prettyPrint($out_arr, 'out_arr');
		$mr = array(); 
		$mr_total = array();
		foreach($out_arr as $ind => $item) {
			$car_id = $item[0]['car_id'];
			$mr[ $car_id ] = $item[0];
		} // foreach

		// SHR 
		// *****************************************************************
		$this->combine_total('SHR', 'current', $shr_current_total, $mr_total);
		$this->combine_total('SHR', 'prev', $shr_prev_total, $mr_total);
		$this->combine_total('SHR', 'prev_prev', $shr_prev_prev_total, $mr_total);
		$total_share = $this->calc_diff_forItem('SHR', $mr_total);


		$this->combine('SHR', 'current', $shr_current, $mr);
		$this->combine('SHR', 'prev', $shr_prev, $mr);
		$this->combine('SHR', 'prev_prev', $shr_prev_prev, $mr);
		foreach($mr as &$item) $this->calc_diff_forItem( 'SHR', $item, $total_share );

		// CC
		// ******************************************************************
		$this->combine_total('CC', 'current', $cc_current_total, $mr_total);
		$this->combine_total('CC', 'prev', $cc_prev_total, $mr_total);
		$this->combine_total('CC', 'prev_prev', $cc_prev_prev_total, $mr_total);
		$total_share = $this->calc_diff_forItem('CC', $mr_total);


		$this->combine('CC', 'current', $cc_current, $mr);
		$this->combine('CC', 'prev', $cc_prev, $mr);
		$this->combine('CC', 'prev_prev', $cc_prev_prev, $mr);
		foreach($mr as &$item) $this->calc_diff_forItem( 'CC', $item, $total_share);

		// OFFER
		// ******************************************************************
		$this->combine_total('OFFER', 'current', $offer_current_total, $mr_total);
		$this->combine_total('OFFER', 'prev', $offer_prev_total, $mr_total);
		$this->combine_total('OFFER', 'prev_prev', $offer_prev_prev_total, $mr_total);
		$total_share = $this->calc_diff_forItem('OFFER', $mr_total);


		$this->combine('OFFER', 'current', $offer_current, $mr);
		$this->combine('OFFER', 'prev', $offer_prev, $mr);
		$this->combine('OFFER', 'prev_prev', $offer_prev_prev, $mr);
		//foreach($mr as &$item) $this->calc_diff_forItem( 'OFFER', $item, $total_share );

		// CR closin ratio
		// ******************************************************************
		$fileds = ['current', 'prev', 'prev_prev'];

		$mr_total['CR'] = array();
		foreach ($fileds as $field) {
				
			if ( $mr_total['SHR'][$field] == 0) {
				$mr_total['CR'][$field] = 0;
			} else {
				$mr_total['CR'][$field] = round( $mr_total['OFFER'][$field] / $mr_total['SHR'][$field], 4);
			}
				
		} // foreach
		$total_share = $this->calc_diff_forItem( 'CR', $mr_total );

		foreach ($mr as &$item) {
			$item['CR'] = array();
			foreach ($fileds as $field) {

				if ( $item['SHR'][$field] == 0) {
					$item['CR'][$field] = 0;
				} else {
					$item['CR'][$field] = round( $item['OFFER'][$field] / $item['SHR'][$field], 4);
				}
				
			} // foreach

			$this->calc_diff_forItem( 'CR', $item, $total_share );
		} // foreach

		$sort_mr_temp = array();
		foreach ($mr as $value) $sort_mr_temp[] = array($value);
		$sort_mr_temp = $this->sortOutArray($sort_mr_temp);
		$sort_mr = array();
		foreach ($sort_mr_temp as $value) $sort_mr[] = $value[0];

		//usort($sort_mr, "usort_mr");

		$this->data['SAR_MR'] = $sort_mr;
		$this->data['SAR_MR_total'] = $mr_total;

		//echo print_r( $mr_total );

		//$this->data['SAR_MR_shr_current'] = $shr_current;
		//$this->data['SAR_MR_shr_current_total'] = $shr_current_total;

		//$this->data['SAR_MR_shr_prev'] = $shr_prev;
		//$this->data['SAR_MR_shr_prev_total'] = $shr_prev_total;

		//$this->data['SAR_MR_shr_prev_prev'] = $shr_prev_prev;
		//$this->data['SAR_MR_shr_prev_prev_total'] = $shr_prev_prev_total;
		

	} // load_models_report_data

	private function load_data( $options = array() ) {

		// titles array for each tables
	    $titles = array();
	    $titles[0] = 'Date';
	    for($i=1; $i<32; $i++) { 
	    	$titles[$i] = $i; 
	    };
	    $titles[32] = 'TOTAL';
	    $filter = array( 'day' => $options['day'] );


		$this->data['SAR_titles'] = $titles;
		
		$this->data['SAR_showroom_traffic'] = $this->showroom_traffic( $options );
		$this->data['SAR_showroom_traffic'] = $this->applyFilter($filter, $this->data['SAR_showroom_traffic']);
		$this->data['SAR_showroom_traffic_total'] = $this->getTotal( $this->data['SAR_showroom_traffic'] );
		
		$this->data['SAR_leads'] = $this->leads( $options );
		$this->data['SAR_leads'] = $this->applyFilter($filter, $this->data['SAR_leads']);
		$this->data['SAR_leads_total'] = $this->getTotal( $this->data['SAR_leads'] );

		$this->data['SAR_offer'] = $this->offer( $options );
		$this->data['SAR_offer'] = $this->applyFilter($filter, $this->data['SAR_offer']);
		$this->data['SAR_offer_total'] = $this->getTotal( $this->data['SAR_offer'] );

		$this->data['SAR_testdrive'] = $this->testdrive( $options );
		$this->data['SAR_testdrive'] = $this->applyFilter($filter, $this->data['SAR_testdrive']);
		$this->data['SAR_testdrive_total'] = $this->getTotal( $this->data['SAR_testdrive'] );

		$this->data['SAR_sale'] = $this->sale( $options );
		$this->data['SAR_sale'] = $this->applyFilter($filter, $this->data['SAR_sale']);
		$this->data['SAR_sale_total'] = $this->getTotal( $this->data['SAR_sale'] );

		$this->data['SAR_total'] = $this->get_sar_total($this->data);

		$this->data['SAR_analysis'] = $this->get_sar_analysis($this->data);

	}

	private function load_cars_data() {

		$cars = CRM::get_cars();
		$this->cars_data = array();
		$this->cars_data_byName = array();
		foreach ( $cars['Result']['Data'] as $car ) {
			$this->cars_data[ $car['Su_carcomplId'] ] = $car;
			$this->cars_data_byName[ $car['Su_name'] ] = $car;
		} // foreach

	} // load_cars_data

	// SAR_total
	// ****************************************
	private function get_sar_total($params){
		$SAR_index	= self::SAR;
		$SAR_showroom_traffic_total	= $params['SAR_showroom_traffic_total'];
		$SAR_leads_total			= $params['SAR_leads_total'];
		$SAR_offer_total			= $params['SAR_offer_total'];
		$SAR_testdrive_total		= $params['SAR_testdrive_total'];
		$SAR_sale_total				= $params['SAR_sale_total'];

		foreach ($SAR_index as $key => $index) {
			$sar_total[$key][] = substr($index['name'], 0, strpos($index['name'], "-"));;
			$sar_total[$key][] = ${$index['value'].'_total'}[32];

			if($key == 1){
				if(!empty(${$SAR_index[0]['value'].'_total'}[32]) || ${$SAR_index[0]['value'].'_total'}[32] != 0){
					$sar_total[$key][] = round(${$index['value'].'_total'}[32]*100/${$SAR_index[0]['value'].'_total'}[32]).'%';
				} else {
					$sar_total[$key][] = '';
				}

				$sar_total[$key][] = 'від '.substr($SAR_index[0]['name'], 0, strpos($SAR_index[0]['name'], "("));
			} elseif ($key == 2 || $key == 3) {

				if(!empty(${$SAR_index[1]['value'].'_total'}[32]) || ${$SAR_index[1]['value'].'_total'}[32] != 0){
					$sar_total[$key][] = round(${$index['value'].'_total'}[32]*100/${$SAR_index[1]['value'].'_total'}[32]).'%';
				} else {
					$sar_total[$key][] = '';
				}
				
				$sar_total[$key][] = 'від '.substr($SAR_index[1]['name'], 0, strpos($SAR_index[1]['name'], "("));

				if(!empty(${$SAR_index[0]['value'].'_total'}[32]) || ${$SAR_index[0]['value'].'_total'}[32] != 0){
					$sar_total[$key][] = round(${$index['value'].'_total'}[32]*100/${$SAR_index[0]['value'].'_total'}[32]).'%';
				} else {
					$sar_total[$key][] = '';
				}

				if($key == 2){
					$sar_total[$key][] = 'від '.substr($SAR_index[0]['name'], 0, strpos($SAR_index[0]['name'], "(")).' (TD ratio)';
				} elseif($key == 3){
					$sar_total[$key][] = 'від '.substr($SAR_index[0]['name'], 0, strpos($SAR_index[0]['name'], "(")).' (closing ratio)';
				}
			} elseif ($key == 4) {
				if(!empty(${$SAR_index[1]['value'].'_total'}[32]) || ${$SAR_index[1]['value'].'_total'}[32] != 0){
					$sar_total[$key][] = round(${$index['value'].'_total'}[32]*100/${$SAR_index[1]['value'].'_total'}[32]).'%';
				} else {
					$sar_total[$key][] = '';
				}

				$sar_total[$key][] = 'від '.substr($SAR_index[1]['name'], 0, strpos($SAR_index[1]['name'], "("));
				
				if($sar_total[4][1] != 0){
					$sar_total[$key][] = round($sar_total[4][1]*100/$sar_total[3][1]).'%';
				} else {
					$sar_total[$key][] = '';
				}
				$sar_total[$key][] = 'від CC/Offer';
			}
		}

		return $sar_total;
	}


	// sar_analysis
	// ****************************************
	private function get_sar_analysis($params){
		//$SAR_index					= $params['SAR_index'];
		$SAR_index	= self::SAR;
		$SAR_showroom_traffic_total	= $params['SAR_showroom_traffic_total'];
		$SAR_leads_total			= $params['SAR_leads_total'];
		$SAR_offer_total			= $params['SAR_offer_total'];
		$SAR_testdrive_total		= $params['SAR_testdrive_total'];
		$SAR_sale_total				= $params['SAR_sale_total'];
		$SAR_showroom_traffic		= $params['SAR_showroom_traffic'];
		$SAR_leads					= $params['SAR_leads'];
		$SAR_offer					= $params['SAR_offer'];
		$SAR_testdrive				= $params['SAR_testdrive'];
		$SAR_sale					= $params['SAR_sale'];

		$parrams_analysis = array(
			0 => ['1', '0'],
			1 => ['3', '1'],
			2 => ['2', '1'],
			3 => ['2', '0'],
			4 => ['3', '0']
		);

		$sar_analysis[0] = array(
			0 => 'Model',
			1 => 'Lead / SHR traf',
			2 => 'TD / Lead',
			3 => 'CC / Lead',
			4 => 'Closing ratio',
			5 => 'TD ratio'
		);

		for ($i = 0; $i < count(${$SAR_index[0]['value']}); $i++) {
			$sar_analysis[$i+1][0] = ${$SAR_index[0]['value']}[$i][0]['value'];

			for ($j = 0; $j < count($parrams_analysis); $j++) {
				$fp = $parrams_analysis[$j][0];
				$sp = $parrams_analysis[$j][1];

				foreach (${$SAR_index[$fp]['value']} as $index) {
					if($index[0]['value'] == ${$SAR_index[$sp]['value']}[$i][0]['value']){
						if(!empty($index[32]['value']) && !empty(${$SAR_index[$sp]['value']}[$i][32]['value'])){
							$sar_analysis[$i+1][$j+1] = round($index[32]['value']/${$SAR_index[$sp]['value']}[$i][32]['value']*100).'%';
						} else{
							$sar_analysis[$i+1][$j+1] = '';
						}
					}
				}
			}
		}

		return $sar_analysis;
	}

	// dealers for select interface
	// ****************************************

	private function load_dealers() {

		$dealers_crm = CRM::get_dealer_centers();
		$dealers = array();
		$dealers['XXX'] = array('AccountId' => 'XXX', 'Name' => 'Всі дилери');	

		foreach ($dealers_crm['Result']['Data'] as $dealer) {
			if ($dealer['New_rrdi'] == '0000001') continue;
			$dealers[  $dealer['AccountId'] ] = array(
				'AccountId' => $dealer['AccountId'], 
				'Name' => $dealer['Name'],
				'City' => $dealer['New_cityname'],
				'Code' => $dealer['New_rrdi']
			);
		}
		
		usort($dealers, "usort_dealers");
		return $dealers;

	} // load_dealers


	// showroom_traffic table
	// *********************************************************
	public function showroom_traffic( $options = array() ) {
		
		$SUFFIX = 'st';

		//$options = array('month' => 2, 'year' => 2021);
		$crm_options = $options;
		if ( isset($options['dealer']) && !is_array($options['dealer']) ) $options['dealer'] = [ $options['dealer'] ];
		//echo "<script>console.log(`options=". json_encode( $options ) ."`);</script>";
		unset($crm_options['dealer']);

		$showroom_traffic = CRM::get_showroom_traffic_details_v2( $crm_options );
		$data = $showroom_traffic['Result']['Data'];

		//@debug
		//echo '<b>showroom_traffic</b><br>';
		//echo json_encode($showroom_traffic, JSON_PRETTY_PRINT);
		//echo '<br><b>New_showroom_trafficid</b><br>';

		// trafiic interator
		// get details on each interation
		$out_arr = $this->createOutArray( $SUFFIX );
		//return $out_arr;

		foreach ($data as $item) {
			$new_showroom_trafficid = $item['New_showroom_trafficid'];
			$account_id = $item['New_account_id'];

			if ( $account_id == self::TEST_DEALER ) continue;
			if (  is_array($options['dealer']) && !in_array('XXX', $options['dealer']) ) {
				if ( !in_array($account_id, $options['dealer']) ) continue;
			} // if

			//$details = CRM::get_showroom_traffic_details( array('id' => $new_showroom_trafficid ) );
			$details_data = $item['Details'];
			
			$new_car_id = $item['New_car_id'];
			
			// define zerro item of the table row
			// it possible 3 situation:
			// 1. there is the car in the $this->cars_data
			// 2. there is the car in the $this->cars_data AND out_arr_index was set
			// 3. there is not the car in the $this->cars_data
			if ( isset($this->cars_data[ $new_car_id ]) ) {
				
				$out_arr_index = $this->cars_data[ $new_car_id ][ 'index_'.$SUFFIX ];
				if ( isset($out_arr_index) ) {
					$tr = &$out_arr[ $out_arr_index ];
				} else {

					$out_arr[] = array( array('value' => $this->cars_data[ $new_car_id ][ 'Su_name' ], 'fromCarsList' => false) );
					$index_st = array_key_last( $out_arr );
					$tr = &$out_arr[$index_st];

				}

			} else {

				$out_arr[] = array(array( 'car_id' =>  $new_car_id, 'value' => $new_car_id, 'fromCarsList' => false ));
				$index_st = array_key_last( $out_arr );
				$tr = &$out_arr[$index_st];
				
			}
			$tr[0]['id'] = $new_showroom_trafficid;
/*
			for ($i=1; $i<32; $i++) {
				$tr[$i] = array('value' => '', 'id' => '');
			} // for
			$tr[32] = array('value' => $item['New_total_count']);
			if ($tr[32]['value'] == '') $tr[32]['value'] = 0;
*/
			//$details_data = $details['Result']['Data'];
			if ( empty($details_data ) ) {
				//$tr[1]['value'] = $item['New_total_count'];
			} else {
				foreach ($details_data as $details_item) {
					if ($details_item['New_showroom_traffic_parentId'] != $new_showroom_trafficid) continue;
					$day = $details_item['New_day']-100000000;
					if ( !($day>0 && $day<32) ) $day = 1;
					if ( $tr[$day]['value'] == '' ) $tr[$day]['value'] = 0;
					$tr[$day]['value'] += $details_item['New_count'];

					if ( !empty($tr[$day]['id']) ) $tr[$day]['ids'][] = $details_item['New_showroom_traffic_detailsId'];
					$tr[$day]['id'] =  $details_item['New_showroom_traffic_detailsId'];
				}
			}

			//$out_arr[] = $tr;
			//echo json_encode($details, JSON_PRETTY_PRINT);
			//echo $new_showroom_trafficid;
			//echo '<hr>';
		} // foreach

		//echo json_encode($showroom_traffic, JSON_PRETTY_PRINT);
		$this->calc_row_total( $out_arr );
		return $this->sortOutArray($out_arr);
			//return CRM::get_showroom_traffic();
	} // showroom_traffic
	
	public function ___depricated_____________showroom_traffic( $options = array() ) {
		
		$SUFFIX = 'st';

		//$options = array('month' => 2, 'year' => 2021);
		$crm_options = $options;
		if ( isset($options['dealer']) && !is_array($options['dealer']) ) $options['dealer'] = [ $options['dealer'] ];
		//echo "<script>console.log(`options=". json_encode( $options ) ."`);</script>";
		unset($crm_options['dealer']);

		$showroom_traffic = CRM::get_showroom_traffic( $crm_options );
		$data = $showroom_traffic['Result']['Data'];

		//@debug
		//echo '<b>showroom_traffic</b><br>';
		//echo json_encode($showroom_traffic, JSON_PRETTY_PRINT);
		//echo '<br><b>New_showroom_trafficid</b><br>';


		// trafiic interator
		// get details on each interation
		$out_arr = $this->createOutArray( $SUFFIX );
		//return $out_arr;

		foreach ($data as $item) {
			$new_showroom_trafficid = $item['New_showroom_trafficid'];
			$account_id = $item['New_account_id'];

			if ( $account_id == self::TEST_DEALER ) continue;
			if (  is_array($options['dealer']) && !in_array('XXX', $options['dealer']) ) {
				if ( !in_array($account_id, $options['dealer']) ) continue;
			} // if

			$details = CRM::get_showroom_traffic_details( array('id' => $new_showroom_trafficid ) );
			
			$new_car_id = $item['New_car_id'];
			
			// define zerro item of the table row
			// it possible 3 situation:
			// 1. there is the car in the $this->cars_data
			// 2. there is the car in the $this->cars_data AND out_arr_index was set
			// 3. there is not the car in the $this->cars_data
			if ( isset($this->cars_data[ $new_car_id ]) ) {
				
				$out_arr_index = $this->cars_data[ $new_car_id ][ 'index_'.$SUFFIX ];
				if ( isset($out_arr_index) ) {
					$tr = &$out_arr[ $out_arr_index ];
				} else {

					$out_arr[] = array( array('value' => $this->cars_data[ $new_car_id ][ 'Su_name' ], 'fromCarsList' => false) );
					$index_st = array_key_last( $out_arr );
					$tr = &$out_arr[$index_st];

				}

			} else {

				$out_arr[] = array(array( 'car_id' =>  $new_car_id, 'value' => $new_car_id, 'fromCarsList' => false ));
				$index_st = array_key_last( $out_arr );
				$tr = &$out_arr[$index_st];
				
			}
			$tr[0]['id'] = $new_showroom_trafficid;
/*
			for ($i=1; $i<32; $i++) {
				$tr[$i] = array('value' => '', 'id' => '');
			} // for
			$tr[32] = array('value' => $item['New_total_count']);
			if ($tr[32]['value'] == '') $tr[32]['value'] = 0;
*/
			$details_data = $details['Result']['Data'];
			if ( empty($details_data ) ) {
				//$tr[1]['value'] = $item['New_total_count'];
			} else {
				foreach ($details_data as $details_item) {
					if ($details_item['New_showroom_traffic_id'] != $new_showroom_trafficid) continue;
					$day = $details_item['New_day']-100000000;
					if ( !($day>0 && $day<32) ) $day = 1;
					if ( $tr[$day]['value'] == '' ) $tr[$day]['value'] = 0;
					$tr[$day]['value'] += $details_item['New_count'];

					if ( !empty($tr[$day]['id']) ) $tr[$day]['ids'][] = $details_item['New_showroom_traffic_detailsId'];
					$tr[$day]['id'] =  $details_item['New_showroom_traffic_detailsId'];
				}
			}

			//$out_arr[] = $tr;
			//echo json_encode($details, JSON_PRETTY_PRINT);
			//echo $new_showroom_trafficid;
			//echo '<hr>';
		} // foreach

		//echo json_encode($showroom_traffic, JSON_PRETTY_PRINT);
		$this->calc_row_total( $out_arr );
		return $this->sortOutArray($out_arr);
			//return CRM::get_showroom_traffic();
	} // ___depricated_____________showroom_traffic

	// lead table
	// *********************************************
	public function leads($options = array() ) {
		
		$SUFFIX = 'ld';

		//$options = array('month' => 2, 'year' => 2021);
		$crm_options = $options;
		if ( isset($options['dealer']) && !is_array($options['dealer']) ) $options['dealer'] = [ $options['dealer'] ];
		//echo "<script>console.log(`options=". json_encode( $options ) ."`);</script>";
		unset($crm_options['dealer']);
		$leads = CRM::get_leads( $crm_options );
		$leads_details = CRM::get_lead_details_v2( $crm_options );
		$data = $leads['Result']['Data'];
		$details_data = $leads_details['Result']['Data'];

		//@debug
		//echo '<b>leads</b><br>';
		//echo Utils::prettyPrint( $data, '$leads');
		//echo Utils::prettyPrint( $details_data, '$leads_details');
		//return;

		// create index array form leads
		$data_index = array();
		foreach ($data as $item) {
			$data_index[ $item['New_lead_sarId'] ] = $item;
		} // foreach

		$out_arr = $this->createOutArray( $SUFFIX );
				// New_lead_sarId interator
		// get details on each interation
		foreach ($data as $item) {
			$new_lead_sarId = $item['New_lead_sarId'];
			$account_id = $item['New_account_id'];

			if ( $account_id == self::TEST_DEALER ) continue;
			if (  is_array($options['dealer']) && !in_array('XXX', $options['dealer']) ) {
				if ( !in_array($account_id, $options['dealer']) ) continue;
			} // if
 
			//$details = CRM::get_lead_details( array('id' => $new_lead_sarId ) );
			
			$new_car_id = $item['New_car_id'];
			//$tr = array();

			// define zerro item of the table row
			// it possible 3 situation:
			// 1. there is the car in the $this->cars_data
			// 2. there is the car in the $this->cars_data AND out_arr_index was set
			// 3. there is not the car in the $this->cars_data

			//if ($this->cars_data[ $new_car_id ][ 'Su_name' ] == 'Berlingo VU') echo $this->cars_data[ $new_car_id ][ 'Su_name' ].'<br>';

			if ( isset($this->cars_data[ $new_car_id ]) ) {
				
				$out_arr_index = $this->cars_data[ $new_car_id ][ 'index_'.$SUFFIX ];
				if ( isset($out_arr_index) ) {
					$tr = &$out_arr[ $out_arr_index ];
				} else {

					$out_arr[] = array( array('car_id' =>  $new_car_id, 'value' => $this->cars_data[ $new_car_id ][ 'Su_name' ], 'fromCarsList' => false) );
					$out_arr_index = array_key_last( $out_arr );
					$this->cars_data[ $new_car_id ][ 'index_'.$SUFFIX ] = $out_arr_index;
					$tr = &$out_arr[ $out_arr_index ];

				}

			} else {

				$out_arr[] = array(array( 'car_id' =>  $new_car_id, 'value' => $new_car_id, 'fromCarsList' => false ));
				$out_arr_index = array_key_last( $out_arr );
				$tr = &$out_arr[$out_arr_index];
				
			}
			$tr[0]['id'] = $new_lead_sarId;
/*
			for ($i=1; $i<32; $i++) {
				$tr[$i]= array('value' => '', 'id' => '');;
			} // for
			$tr[32] = array('value' => $item['New_total_count'] );
*/
			//$details_data = $details['Result']['Data'];
			if ( empty($details_data ) ) {
				//$tr[1]['value'] = $item['New_total_count'];
			} else {
				foreach ($details_data as $details_item) {
					if ($details_item['New_lead_sar_id'] != $new_lead_sarId) continue;
					$day = $details_item['New_day']-100000000;
					if ( !($day>0 && $day<32) ) $day = 0;
					if ( $tr[$day]['value'] == '' ) $tr[$day]['value'] = 0;
					$tr[$day]['value'] += $details_item['New_count'];
					$tr[$day]['id'] = $details_item['New_lead_sar_detailid'];
				}
			}

			//echo json_encode($details, JSON_PRETTY_PRINT);
			//echo $new_showroom_trafficid;
			//echo '<hr>';
		} // foreach

		//echo json_encode($showroom_traffic, JSON_PRETTY_PRINT);
		$this->calc_row_total( $out_arr );
		return $this->sortOutArray($out_arr);
			//return CRM::get_showroom_traffic();
	} // leads

	// ************************sale************************sale**********************
	// sale
	// *********************************************

	public function sale( $options = array() ) {
		
		$SUFFIX = 'sl';

		//$options = array('month' => 5, 'year' => 2021);
		//$sale = CRM::get_sale( $options );
		//$data = $sale['Result']['Data'];

		if (CRM::$_organization == 'opel' && $this->config->item('cheat_offer_opel') === true) {
			return $this->cheat_opel_offer( $options );
		} // if

		$data = $this->CRM_get_data( $options, 'get_sale' );
		
		//@debug
		//echo '<b>sale</b><br>';
		//echo json_encode($data,  JSON_PRETTY_PRINT);
		//echo '<br><b>New_lead_sarId</b><br>';

		//init out array
		$out_arr = $this->createOutArray( $SUFFIX );
		foreach ($out_arr  as $key => $value) {
			for ($i=1; $i<32; $i++) {
				$out_arr[$key][$i]= array('value' => '');;
			} // for
		}; // foreacj

		// put data into $out_arr
		foreach ($data as $item) {			
			$car_name = $item['VehicleName'];
			//echo $car_name;
			$day = $item['Day'];

			//echo $car_name;

			if ( is_nan( (int)$day ) ) continue;

			// define zerro item of the table row
			// it possible 3 situation:
			// 1. there is the car in the $this->cars_data
			// 2. there is the car in the $this->cars_data AND out_arr_index was set
			// 3. there is not the car in the $this->cars_data

			if ( isset($this->cars_data_byName[ $car_name ]) ) {
				
				$out_arr_index = $this->cars_data_byName[ $car_name ][ 'index_'.$SUFFIX ];
				if ( isset($out_arr_index) ) {
					$tr = &$out_arr[ $out_arr_index ];
				} else {

					$out_arr[] = array( array('value' => $this->cars_data_byName[ $car_name ][ 'Su_name' ], 'fromCarsList' => false) );
					$out_arr_index = array_key_last( $out_arr );
					$this->cars_data_byName[ $car_name ]['index_'.$SUFFIX] = $out_arr_index;
					$tr = &$out_arr[ $out_arr_index ];

				}

			} else {

				$out_arr[] = array(array( 'value' => $this->cars_data_byName[ $car_name ][ 'Su_name' ], 'fromCarsList' => false ));
				$out_arr_index = array_key_last( $out_arr );
				$this->cars_data_byName[ $car_name ]['index_'.$SUFFIX] = $out_arr_index;
				$tr = &$out_arr[$out_arr_index];
				
			}

			if ($tr[$day]['value'] == '') $tr[$day]['value']  = 0;
			$tr[$day]['value']++;

		} // foreach

		// calc total
		/*		
				foreach ($out_arr as $key => $value) {
					$total = 0;
					for ($i=1; $i<32; $i++) {
						if ( isset($out_arr[$key][$i]) && $out_arr[$key][$i]['value'] != '') $total += $out_arr[$key][$i]['value'];
					}
					$out_arr[$key][32] = array('value' => $total);
				} // foreach
		*/

		//echo json_encode($showroom_traffic, JSON_PRETTY_PRINT);
		$this->calc_row_total( $out_arr );
		return $this->sortOutArray($out_arr);
			//return CRM::get_showroom_traffic();
	} // sale

	// ************************testdrive************************testdrive**********************
	// testdrive table
	// *********************************************
	public function testdrive($options = array() ) {
		
		$SUFFIX = 'td';

		//$options = array('month' => 5, 'year' => 2021);
		//$td = CRM::get_testdrive( $options );
		//$data = $td['Result']['Data'];

		$data = $this->CRM_get_data( $options, 'get_testdrive' );
		
		//@debug
		//echo '<b>leads</b><br>';
		//echo json_encode($data,  JSON_PRETTY_PRINT);
		//echo '<br><b>New_lead_sarId</b><br>';

		//init out array
		$out_arr = $this->createOutArray( $SUFFIX );
		foreach ($out_arr  as $key => $value) {
			for ($i=1; $i<32; $i++) {
				$out_arr[$key][$i]= array('value' => '');;
			} // for
		}; // foreacj

		// put data into $out_arr
		foreach ($data as $item) {			
			$car_name = $item['Su_name'];
			$day = $item['Day'];

			if ( is_nan( (int)$day ) ) continue;

			// define zerro item of the table row
			// it possible 3 situation:
			// 1. there is the car in the $this->cars_data
			// 2. there is the car in the $this->cars_data AND out_arr_index was set
			// 3. there is not the car in the $this->cars_data

			if ( isset($this->cars_data_byName[ $car_name ]) ) {
				
				$out_arr_index = $this->cars_data_byName[ $car_name ][ 'index_'.$SUFFIX ];
				if ( isset($out_arr_index) ) {
					$tr = &$out_arr[ $out_arr_index ];
				} else {

					$out_arr[] = array( array('value' => $this->cars_data_byName[ $car_name ][ 'Su_name' ], 'fromCarsList' => false) );
					$out_arr_index = array_key_last( $out_arr );
					$this->cars_data_byName[ $car_name ]['index_'.$SUFFIX] = $out_arr_index;
					$tr = &$out_arr[ $out_arr_index ];

				}

			} else {

				$out_arr[] = array(array( 'value' => $this->cars_data_byName[ $car_name ][ 'Su_name' ], 'fromCarsList' => false ));
				$out_arr_index = array_key_last( $out_arr );
				$this->cars_data_byName[ $car_name ]['index_'.$SUFFIX] = $out_arr_index;
				$tr = &$out_arr[$out_arr_index];
				
			}

			if ($tr[$day]['value'] == '') $tr[$day]['value']  = 0;
			$tr[$day]['value']++;

		} // foreach

		// calc total
		/*		
				foreach ($out_arr as $key => $value) {
					$total = 0;
					for ($i=1; $i<32; $i++) {
						if ( isset($out_arr[$key][$i]) && $out_arr[$key][$i]['value'] != '') $total += $out_arr[$key][$i]['value'];
					}
					$out_arr[$key][32] = array('value' => $total);
				} // foreach
		*/

		//echo json_encode($showroom_traffic, JSON_PRETTY_PRINT);
		$this->calc_row_total( $out_arr );
		return $this->sortOutArray($out_arr);
			//return CRM::get_showroom_traffic();
	} // testdrive

	// ************************offer************************offer**********************
	// offer table
	// *********************************************
		private function cheat_opel_offer($options = array() ) {
		
		$SUFFIX = 'sl'; // sl beacause from sale call`
 
		//$options = array('month' => 5, 'year' => 2021);
		//$crm_options = $options;
		//unset($crm_options['dealer']);
		//$sale = CRM::get_offer( $crm_options  );
		//$data = $sale['Result']['Data'];
				
		$data = $this->CRM_get_data( $options, 'get_offer_opel' );
		//@debug
		//echo '<b>leads</b><br>';
		//echo json_encode($data,  JSON_PRETTY_PRINT);
		//echo '<br><b>New_lead_sarId</b><br>';

		//init out array
		$out_arr = $this->createOutArray( $SUFFIX );
		foreach ($out_arr  as $key => $value) {
			for ($i=1; $i<32; $i++) {
				$out_arr[$key][$i]= array('value' => '');;
			} // for
		}; // foreacj

		// put data into $out_arr
		foreach ($data as $item) {			
			$car_name = $item['New_model_for_td'];
			$day = $item['Day'];

			if ( is_nan( (int)$day ) ) continue;

			// define zerro item of the table row
			// it possible 3 situation:
			// 1. there is the car in the $this->cars_data
			// 2. there is the car in the $this->cars_data AND out_arr_index was set
			// 3. there is not the car in the $this->cars_data

			if ( isset($this->cars_data_byName[ $car_name ]) ) {
				
				$out_arr_index = $this->cars_data_byName[ $car_name ][ 'index_'.$SUFFIX ];
				if ( isset($out_arr_index) ) {
					$tr = &$out_arr[ $out_arr_index ];
				} else {

					$out_arr[] = array( array('value' => $this->cars_data_byName[ $car_name ][ 'Su_name' ], 'fromCarsList' => false) );
					$out_arr_index = array_key_last( $out_arr );
					$this->cars_data_byName[ $car_name ]['index_'.$SUFFIX] = $out_arr_index;
					$tr = &$out_arr[ $out_arr_index ];

				}

			} else {

				$out_arr[] = array(array( 'value' => $this->cars_data_byName[ $car_name ][ 'Su_name' ], 'fromCarsList' => false ));
				$out_arr_index = array_key_last( $out_arr );
				$this->cars_data_byName[ $car_name ]['index_'.$SUFFIX] = $out_arr_index;
				$tr = &$out_arr[$out_arr_index];
				
			}

			if ($tr[$day]['value'] == '') $tr[$day]['value']  = 0;
			$tr[$day]['value']++;

		} // foreach

		// calc total
		/*		
				foreach ($out_arr as $key => $value) {
					$total = 0;
					for ($i=1; $i<32; $i++) {
						if ( isset($out_arr[$key][$i]) && $out_arr[$key][$i]['value'] != '') $total += $out_arr[$key][$i]['value'];
					}
					$out_arr[$key][32] = array('value' => $total);
				} // foreach
		*/

		//echo json_encode($showroom_traffic, JSON_PRETTY_PRINT);
		$this->calc_row_total( $out_arr );
		return $this->sortOutArray($out_arr);
			//return CRM::get_showroom_traffic();
	} // offer


	
	public function offer($options = array() ) {
		
		$SUFFIX = 'of';

		//$options = array('month' => 5, 'year' => 2021);
		//$crm_options = $options;
		//unset($crm_options['dealer']);
		//$sale = CRM::get_offer( $crm_options  );
		//$data = $sale['Result']['Data'];
				

		$data = $this->CRM_get_data( $options, 'get_offer' );
		
		//@debug
		//echo '<b>leads</b><br>';
		//echo json_encode($data,  JSON_PRETTY_PRINT);
		//echo '<br><b>New_lead_sarId</b><br>';

		//init out array
		$out_arr = $this->createOutArray( $SUFFIX );
		foreach ($out_arr  as $key => $value) {
			for ($i=1; $i<32; $i++) {
				$out_arr[$key][$i]= array('value' => '');;
			} // for
		}; // foreacj

		// put data into $out_arr
		foreach ($data as $item) {			
			$car_name = $item['New_model_for_td'];
			$day = $item['Day'];

			if ( is_nan( (int)$day ) ) continue;

			// define zerro item of the table row
			// it possible 3 situation:
			// 1. there is the car in the $this->cars_data
			// 2. there is the car in the $this->cars_data AND out_arr_index was set
			// 3. there is not the car in the $this->cars_data

			if ( isset($this->cars_data_byName[ $car_name ]) ) {
				
				$out_arr_index = $this->cars_data_byName[ $car_name ][ 'index_'.$SUFFIX ];
				if ( isset($out_arr_index) ) {
					$tr = &$out_arr[ $out_arr_index ];
				} else {

					$out_arr[] = array( array('value' => $this->cars_data_byName[ $car_name ][ 'Su_name' ], 'fromCarsList' => false) );
					$out_arr_index = array_key_last( $out_arr );
					$this->cars_data_byName[ $car_name ]['index_'.$SUFFIX] = $out_arr_index;
					$tr = &$out_arr[ $out_arr_index ];

				}

			} else {

				$out_arr[] = array(array( 'value' => $this->cars_data_byName[ $car_name ][ 'Su_name' ], 'fromCarsList' => false ));
				$out_arr_index = array_key_last( $out_arr );
				$this->cars_data_byName[ $car_name ]['index_'.$SUFFIX] = $out_arr_index;
				$tr = &$out_arr[$out_arr_index];
				
			}

			if ($tr[$day]['value'] == '') $tr[$day]['value']  = 0;
			$tr[$day]['value']++;

		} // foreach

		// calc total
		/*		
				foreach ($out_arr as $key => $value) {
					$total = 0;
					for ($i=1; $i<32; $i++) {
						if ( isset($out_arr[$key][$i]) && $out_arr[$key][$i]['value'] != '') $total += $out_arr[$key][$i]['value'];
					}
					$out_arr[$key][32] = array('value' => $total);
				} // foreach
		*/

		//echo json_encode($showroom_traffic, JSON_PRETTY_PRINT);
		$this->calc_row_total( $out_arr );
		return $this->sortOutArray($out_arr);
			//return CRM::get_showroom_traffic();
	} // offer

	// additional functions for data proccessing
	//**************************************************

	private function CRM_get_data( $options, $method) {
		
		$crm_options = $options;

		if ( isset($options['dealer']) && is_array($options['dealer']) ) {

			$ret = array();
			foreach ($options['dealer'] as $dealer) {
				$crm_options['dealer'] = $dealer;
				if ( $dealer == 'XXX' ) unset( $crm_options['dealer'] );
				$ret_dealer = CRM::{$method}( $crm_options );
				$ret = array_merge( $ret, $ret_dealer['Result']['Data'] );
			} // foreach
			
		} else {
			$ret_crm = CRM::{$method}( $options );
			//$ret_crm = CRM::get_testdrive( $options );
			$ret = $ret_crm['Result']['Data'];
		}

		return $ret;

	} // CRM_get_data

	private function calc_row_total( &$out_arr ) {
		
		// calc_row_total
		foreach ($out_arr as $key => $value) {
			$total = 0;
			for ($i=1; $i<32; $i++) {
				if ( isset($out_arr[$key][$i]) && $out_arr[$key][$i]['value'] != '') $total += $out_arr[$key][$i]['value'];
			}
			$out_arr[$key][32] = array('value' => $total);
		} // foreach

	} // calc_row_total

	private function createOutArray( $suffix = '' ) {
		
		$out_arr = array();

		foreach ($this->cars_data as &$car) {
			// for debug
			//if ($car['Su_name'] == 'C3 Aircross') continue;
			
			if ( isset($car['New_new_model_cars_for_td']) && $car['New_new_model_cars_for_td'] === true &&
		         $car['New_out_of_production'] !== true && !empty( $car['New_car_model'] ) ) {

				$out_arr[] = array(array( 'car_id' => $car['Su_carcomplId'], 'value' => $car['Su_name'], 'fromCarsList' => true, 
					'New_td_id' => $car['New_td_id'] ));
			    $ind = array_key_last( $out_arr );

			    if ($suffix != '') {
					for ($i=1; $i<32; $i++) {
						$out_arr[ $ind ][]= array('value' => '', 'id' => '');
					} // for
					$out_arr[ $ind ][32] = array('value' => 0 );
				}

				$car['index_'.$suffix] = array_key_last( $out_arr );
				$this->cars_data_byName[ $car['Su_name'] ]['index_'.$suffix]  = $car['index_'.$suffix];

			} // if

		} // foreach

		return $out_arr;

	} // createOutArray

	private function sortOutArray( $arr = array() ) {

		//return $arr;

		// create 3 arrays:
		// 1. when  "fromCarsList" = true AND "New_td_id" != null - FOR sorting by New_td_id  - add this array to first of the table
		// 2. when  "fromCarsList" = true AND "New_td_id" != null - FOR sorting by value - add this array to second of the table
		// 3. when  "fromCarsList" = false FOR sorting by value - add this array to the end of the table

		$arr1 = array();
		$arr2 = array();
		$arr3 = array();

		foreach ($arr as $key => $value) {
			if ( $arr[$key][0]['fromCarsList'] === true && !is_null( $arr[$key][0]['New_td_id'] ) ) {
				$arr1[] = $arr[$key];
			} elseif ( $arr[$key][0]['fromCarsList'] === true && is_null( $arr[$key][0]['New_td_id'] ) ) {
				$arr2[] = $arr[$key];
			} else {
				$arr3[] = $arr[$key];
			}
		} // foreach

		usort($arr1, "usort_11");
		usort($arr2, "usort_22");
		usort($arr3, "usort_22");
		
		$ret = array_merge ($arr1, $arr2, $arr3);
		return $ret;
	} // sortOutArray

	private function getTotal( &$arr ) {
		//$ret_arr  = array();
		$ret_arr  = array_fill(0, 33, 0);
		$ret_arr[0] = 'TOTAL';

		foreach ($arr as $key => $value) {
			$row_total = 0;
			//$last_key = array_key_last($value);
			foreach ($value as $key1 => $value1) {
				if ($key1 == 32) {
					$value1['value'] = $row_total;
					//echo $arr[$key][$key1]['value'].' '.$row_total.'<br>';
					$arr[$key][$key1]['value'] = $row_total;
				} 
				if ($key1=='0') continue;
				if ( !isset($ret_arr[ $key1 ]) ) $ret_arr[ $key1 ] = 0;
				if ($value1['value'] == '') continue;
				$ret_arr[ $key1 ] += $value1['value'];
				$row_total +=  $value1['value'];
			}
		}
		return $ret_arr;
	} // getTotal

	public function test22($options = array() ) {
		
		//echo Utils::prettyPrint( $this->user_info, '$this->user_info' );
		//echo '<hr>';

		$this->load->model('Exel_model');
		$web_user_id = $this->Exel_model->get_web_user_id();
		echo '<h3>$this->dealer = '. json_encode( $this->dealer ).'</h3>';
		echo '<hr>';
		echo '<h3>$this->account_id = '. json_encode( $this->account_id ).'</h3>';
		echo '<hr>';

		echo '<h3>$_SESSION[\'access_employees\']= '. json_encode( $_SESSION['access_employees']).'</h3>';
		echo '<hr>';

		echo Utils::prettyPrint( $_SESSION, '$_SESSION');

		//$dealers = CRM::get_dealer_centers();
		$dealers = $this->load_dealers();
		echo Utils::prettyPrint( $dealers, 'dealers');


		$st = $this->showroom_traffic( array('month'=>2, 'year' => 2021) );
		//echo Utils::prettyPrint( $st, 'showroom_traffic');
		
		$ld = $this->leads( array('month'=>2, 'year' => 2021) );
		//echo Utils::prettyPrint( $ld, 'leads');
		
		$alex = 'get_lead_details_v2';
		$leads_details = CRM::{$alex}( array('month'=>5, 'year' => 2021) );
		//$leads_details = CRM::[$alex]( array('month'=>5, 'year' => 2021) );

		//$leads_details = CRM::get_lead_details_v2( array('month'=>5, 'year' => 2021) );
		echo Utils::prettyPrint( $leads_details, 'leads_details');
		$total = 0;
		foreach ($leads_details['Result']['Data'] as $key => $value) {
			$total += $value['New_count'];
		};
		echo '<hr>total of leads_details = '.$total;


		$td = $this->testdrive( array('month'=>2, 'year' => 2021) );
		//echo Utils::prettyPrint( $td, 'testdrive');


		$of = $this->offer( array('month'=>5, 'year' => 2021) );
		//echo Utils::prettyPrint( $of, 'offer');

		//$sl = $this->sale( array('month'=>2, 'year' => 2021) );
		$sl = CRM::get_sale(  array('month'=>2, 'year' => 2021) );
		//echo Utils::prettyPrint( $sl, 'crm:sale');

		$sl_1 = $this->sale( array('month'=>2, 'year' => 2021) );
		//echo Utils::prettyPrint( $sl_1, 'sale' );


		//echo Utils::prettyPrint($this->cars_data, '$this->cars_data');
		//echo Utils::prettyPrint($this->cars_data_byName, '$this->cars_data_byName');
		

		echo '<hr>';
		echo 'CRM::$_organization='.CRM::$_organization.'<br>';

		echo '<hr>';
		echo Utils::prettyPrint($_SESSION, '$_SESSION');

		echo '<hr>';
		echo Utils::prettyPrint($this->user_info, '$this->user_info');

		
		/*
		echo json_encode($_SESSION, JSON_PRETTY_PRINT);
		echo '<h2>picklist</h2>';
		$picklist = CRM::test_cars_picklist();
		echo json_encode($picklist, JSON_PRETTY_PRINT);*/
	}

	public function repair_data() {


		$period_start = date('Y-m-d');
		$period_start_arr = explode('-', $period_start);
		$crm_options = array( 'month' => $period_start_arr[1], 'year' => $period_start_arr[0] );

		$dealers_data = $this->load_dealers();
		$dealers = array();
		foreach($dealers_data as $d) $dealers[ $d['AccountId'] ] = $d;
		//echo Utils::prettyPrint($dealers, '$dealers');
		
		$dublicates = array();

		$showroom_traffic = CRM::get_showroom_traffic( $crm_options );
		$data_shr = $showroom_traffic['Result']['Data'];


// *********************SHR***************************** */

		$index = 'SAR_showroom_traffic';
		foreach($data_shr as $data_item) {
			$new_showroom_trafficid = $data_item['New_showroom_trafficid'];
			
			$account_id = $data_item['New_account_id'];
			$account_name = '';
			if ( isset($dealers[ $account_id ]) ) $account_name = $dealers[ $account_id ]['Name'];
			
			$new_car_id = $data_item['New_car_id'];
			$car_name = 'undefined';
			if ( isset($this->cars_data[ $new_car_id ]) ) $car_name = $this->cars_data[ $new_car_id ][ 'Su_name' ];

			//echo $account_name.': '.$car_name.'<br>';

			$details = CRM::get_showroom_traffic_details( array('id' => $new_showroom_trafficid ) );
			$details_data = $details['Result']['Data'];
			
			$days = array();
			foreach ($details_data as $details_item) {
				if ($details_item['New_showroom_traffic_id'] != $new_showroom_trafficid) continue;
				
				$details_id = $details_item['New_showroom_traffic_detailsId'];
				$day = $details_item['New_day']-100000000;

				if ( !($day>0 && $day<32) ) $day = 1;
				$elem =  array(  'car_id' =>  $new_car_id, 'car_name' => $car_name,
							     'account_id' => $account_id, 'account_name' => $account_name,
							     'day' => $day, 'value' => $details_item['New_count'],
							     'details_id' => $details_id, 
							     'item_id' => $new_showroom_trafficid,
							     //'dd' => $details_item
							  );

				if ( isset($days[ $new_car_id ][ $day ]) ) {
					$e = $days[ $new_car_id ][ $day ];
					$dublicates[ $index ][ $account_id ][ $new_car_id ][ $day ][ $e['details_id'] ] = $e;
					$dublicates[ $index ][ $account_id ][ $new_car_id ][ $day ][ $elem['details_id'] ] = $elem;
				} 
				$days[ $new_car_id][ $day ] = $elem;
				

			} // foreach $details_data

		} // foreach $data

// *********************LEADS***************************** */

		$leads = CRM::get_leads( $crm_options );
		$data_leads = $leads['Result']['Data'];

		$leads_details = CRM::get_lead_details_v2( $crm_options );
		$details_data = $leads_details['Result']['Data'];

		$index = 'SAR_leads';

		foreach ($data_leads as $item) {
			$new_lead_sarId = $item['New_lead_sarId'];
			
			$account_id = $item['New_account_id'];
			$account_name = '';
			if ( isset($dealers[ $account_id ]) ) $account_name = $dealers[ $account_id ]['Name'];

			$new_car_id = $item['New_car_id'];
			$car_name = 'undefined';
			if ( isset($this->cars_data[ $new_car_id ]) ) $car_name = $this->cars_data[ $new_car_id ][ 'Su_name' ];
			
			$days = array();
			foreach ($details_data as $details_item) {
				if ($details_item['New_lead_sar_id'] != $new_lead_sarId) continue;
				
				$details_id = $details_item['New_lead_sar_detailid'];
				$day = $details_item['New_day']-100000000;

				if ( !($day>0 && $day<32) ) $day = 1;
				$elem =  array(  'car_id' =>  $new_car_id, 'car_name' => $car_name,
							     'account_id' => $account_id, 'account_name' => $account_name,
							     'day' => $day, 'value' => $details_item['New_count'],
							     'details_id' => $details_id, 
							     'item_id' => $new_lead_sarId,
							     //'dd' => $details_item
							  );

				if ( isset($days[ $new_car_id ][ $day ]) ) {
					$e = $days[ $new_car_id ][ $day ];
					$dublicates[ $index ][ $account_id ][ $new_car_id ][ $day ][ $e['details_id'] ] = $e;
					$dublicates[ $index ][ $account_id ][ $new_car_id ][ $day ][ $elem['details_id'] ] = $elem;
				} 
				$days[ $new_car_id][ $day ] = $elem;
				

			} // foreach $details_data

		} // foreach

		echo Utils::prettyPrint($dublicates, CRM::$_organization.' organization DUBLICATES:');

		// create post_delete
		//*******************************************************
		$post_delete = array();
		foreach ($dublicates as $index => $table) {
			foreach ($table as $account) {
				foreach ($account as $car) {
					foreach ($car as $day) {
						$count = 0;
						foreach ($day as $elem) {
							$count++;
							if ($count==1) continue;
							$details_id = $elem['details_id'];
							$post_delete[] = array( 'EntityId' => $details_id, 'EntityName' => self::SAR_API_PARAM[$index]['EntityName'] );
						} // elem
					} // day
				} // account			
			} // table
		} // dublicates
		


		echo Utils::prettyPrint($post_delete,  '$post_delete:');


		// DETAIILS delete
		// ******************************************************************
		foreach ($post_delete as $data_item) {

			$ret = array();

			// uncomment to execute deletion:
			//$ret = CRM::sar_delete( array('post_data' => $data_item ) );

			//echo Utils::prettyPrint($ret, 'Data delete');
			if ( (!isset( $ret['Status'] ) || $ret['Status'] != 'OK') ) {
			} // if

		} // foreach


	} // repair_data

	/*************************************************************
	  SETTING DATA THROW API
	***********************************************************/
	public function update() {
		
		//echo Utils::prettyPrint( $_POST, '$_POST');
		//return;
		//echo Utils::prettyPrint( $_SESSION, '$_SESSION');

		$dealers = $this->session->selected_dealer;
		
		//echo Utils::prettyPrint( $dealers, '$dealers');

		if ( !isset($dealers) || $dealers == '' ) $dealers = array($this->dealer);
		if ( count( $dealers ) != 1 || !is_array( $dealers ) ) {
			$this->error( array('error_description' => '<h3>Iternal server error: incorret session.selected_dealer</h3>' ) );
			return;
		} // if

		$dealer = $dealers[0];
		/*
		if ($dealer != '7b081dd9-260d-ea11-81ae-00155d1f050b') {
			echo 'Вибачте, тесніческіе роботи. Збереження тимчасово недоступно.';
			exit;
		}*/
		
		//  period_start parsing
		$period_start =$this->session->SAR_period_start;
		if( !isset( $period_start ) ) {
			$this->error( array('error_description' => '<h3>Iternal server error: not set session.SAR_period_start.</h3>' ) );
			return;
		}
		$period_start_arr = explode('-', $period_start);
		$month = $period_start_arr[1];
		$year = $period_start_arr[0];
		$load_options = array('month' => $month, 'year' => $year, 'dealer' => $dealer);

		// parsing POST data
		// and create rows if it needs

		$post_update = array();
		$post_create = array();
		$post_total = array();
		$post_delete = array();

		foreach (self::SAR as $data_item) {

			$index = $data_item['value'];
			$data_loader =self::SAR_API_PARAM[$index]['data_loader'];

			if (self::SAR_API_PARAM[$index]['updatable'] !== true) continue;

			$current_data = $this->$data_loader( $load_options );
			//echo Utils::prettyPrint( $current_data, '$current_data');
			//echo Utils::prettyPrint( $data_loader, $data_loader);

			$count = $_POST[$index.'_count']; // row count
			if ( !isset($count) ) continue;
			
			for ($i=0; $i<$count; $i++) {
				
				$car_id = $_POST[$index.'_car_id_'.$i];
				$item_id = $_POST[$index.'_id_'.$i];


				if ( !isset($car_id) ) continue;

				//echo $i.$index.'<br>';
				$total = 0; 
				$isTotalUpdate = false;
				for ($j=1; $j<32; $j++) {

					$value = $_POST[$index.'_value_details_'.$i.'_'.$j];
					$old_value = $_POST[$index.'_old_value_details_'.$i.'_'.$j];
					$details_id = $_POST[$index.'_id_details_'.$i.'_'.$j];
					
					// already SET current current_details_id
					// and delete one of them if there are two items
					// *************************************** 2021-06-08
					$current_details_id = null;
					if ( isset($current_data[$i][$j]) ) $current_details_id = $current_data[$i][$j]['id'];
					//echo '$current_details_id= '.$current_details_id.'<br>';

					if ($value != '') $total += (int)$value;

					//echo $i.'_'.$j.'_'.$index.' '.$value.'<br>';
					$condition1 = $details_id == '' && $value != '' && $old_value == '' && $value != 0;
					$condition2 = $value != ''  && $old_value != '' && $value != $old_value && $details_id != '' && $value != 0;
					$condition3 = $value != ''  && $old_value != '' && $value != $old_value && $details_id != '' && $value == 0; 
					$condition4 = $value == ''  && $old_value != '' && $details_id != '';

					// we need before all create the row and get $item_id
					if ( ($condition1 || $condition2) && empty($item_id) ) {
						
						$post_data = array(
							'EntityName' => self::SAR_API_PARAM[$index]['Lookups_EntityName'],
							'Picklists' => array (
								array('AttributeName' => 'new_month', 'Value' => (int)$month+100000000)
							),
							'Lookups' => array(
								array('AttributeName' => 'new_account_id',
									  'EntityName' => 'account',
									  //'EntityId' => $this->account_id
									  'EntityId' => $dealer
								     ),
								array('AttributeName' => 'new_car_id',
									  'EntityName' => 'su_carcompl',
									  'EntityId' => $car_id 
								     )
							),
							'OtherAttributes' => array(
								array( 'AttributeName' => 'new_total_count', 'Value' => 0 ),
								array( 'AttributeName' => 'new_year', 'Value' => (int)$year )
							)
						);
						//echo Utils::prettyPrint( $post_data, '$post_data for row create' );
						
						
						$ret = CRM::sar_create( array('post_data' => $post_data ) );
						if ( !isset( $ret['Status'] ) || $ret['Status'] != 'OK' ) {
							$this->error( array('error_description' => Utils::prettyPrint($ret, 'Data update error. Server response:') ) );
							return;
						} // if
						
						//echo Utils::prettyPrint( $ret, '$ret' );
						$item_id = $ret['Result']['Data']['Id'];
						$isTotalUpdate = true;
						//echo $item_id.'<br>';
					
					} // if - create the row

					if 	( $condition1 ) {
						if ( !empty( $current_details_id ) ) $post_delete[] = array( 'EntityId' => $current_details_id, 'EntityName' => self::SAR_API_PARAM[$index]['EntityName'] );
						$post_create[] = array( 'index' => $index, 'id' => $item_id, 'total_count' => $value, 'day' => $j );
					}


					if ( $condition2 ) {
						
						if ( !empty( $current_details_id ) && $current_details_id != $details_id ) {
							$post_delete[] = array( 'EntityId' => $details_id, 'EntityName' => self::SAR_API_PARAM[$index]['EntityName'] );
							$post_update[] = array( 'index' => $index, 'id' => $item_id, 'details_id' => $current_details_id, 
																										'total_count' => $value, 'day' => $j );
							
						} elseif ( !empty( $current_details_id ) && $current_details_id == $details_id ) {
							$post_update[] = array( 'index' => $index, 'id' => $item_id, 'details_id' => $current_details_id, 
																										'total_count' => $value, 'day' => $j );
						} elseif ( empty( $current_details_id ) && !empty( $details_id ) ) {
							$post_delete[] = array( 'EntityId' => $details_id, 'EntityName' => self::SAR_API_PARAM[$index]['EntityName'] );
							$post_create[] = array( 'index' => $index, 'id' => $item_id, 'total_count' => $value, 'day' => $j );
						}

					}

					if ( $condition3 || $condition4) {
						if ( !empty( $current_details_id ) && $current_details_id != $details_id ) {
							$post_delete[] = array( 'EntityId' => $current_details_id, 'EntityName' => self::SAR_API_PARAM[$index]['EntityName'] );
						}
						$post_delete[] = array( 'EntityId' => $details_id, 'EntityName' => self::SAR_API_PARAM[$index]['EntityName'] );
					}

					if ( $condition1 || $condition2 || $condition3 || $condition4) $isTotalUpdate = true;

				} // for j

				if ($isTotalUpdate)
					$post_total[] = array( 'index' => $index, 'id' => $item_id, 'car_id' => $car_id, 'total_count' => $total );

			} // for i

			

		} // foreach

		//echo Utils::prettyPrint( $post_update, '$post_update' );
		//echo Utils::prettyPrint( $post_create, '$post_create' );
		//echo Utils::prettyPrint( $post_total, '$post_total' );
		//echo Utils::prettyPrint( $post_delete, '$post_delete' );

		//return;

		// DETAILS update 
		// ******************************************************************

		foreach ($post_update as $data_item) {
			$index = $data_item['index'];
			$details_id = $data_item['details_id'];
			$day = (int)$data_item['day']+100000000;
			$id = $data_item['id'];
			$total_count = $data_item['total_count'];

			if ( !isset(self::SAR_API_PARAM[$index]) ) continue;

			$post_data = array(
				'EntityName' => self::SAR_API_PARAM[$index]['EntityName'],
				'Id' => $details_id,
				'Picklists' => array (
					array('AttributeName' => 'new_day', 'Value' => $day)
				),
				'Lookups' => array(
					array('AttributeName' => self::SAR_API_PARAM[$index]['Lookups_EntityName'].'_id',
						  'EntityName' => self::SAR_API_PARAM[$index]['Lookups_EntityName'],
						  'EntityId' => $id 
					     )
				),
				'OtherAttributes' => array(
					array( 'AttributeName' => 'new_count', 'Value' => (int)$total_count )
				)
			);
			
			//echo Utils::prettyPrint( $post_data, '$post_data' );

			$ret = CRM::sar_update( array('post_data' => $post_data ) );
			if ( !isset( $ret['Status'] ) || $ret['Status'] != 'OK' ) {
				$this->error( array('error_description' => Utils::prettyPrint($ret, 'Data update (details) error. Server response') ) );
				return;
			} // if
			//echo Utils::prettyPrint( $ret, '$ret' );

		} // foreach
		
		// DETAIILS create
		// ******************************************************************

		foreach ($post_create as $data_item) {
			$index = $data_item['index'];
			$day = (int)$data_item['day']+100000000;
			$id = $data_item['id'];
			$total_count = $data_item['total_count'];

			if ( !isset(self::SAR_API_PARAM[$index]) ) continue;

			$post_data = array(
				'EntityName' => self::SAR_API_PARAM[$index]['EntityName'],
				'Picklists' => array (
					array('AttributeName' => 'new_day', 'Value' => $day)
				),
				'Lookups' => array(
					array('AttributeName' => self::SAR_API_PARAM[$index]['Lookups_EntityName'].'_id',
						  'EntityName' => self::SAR_API_PARAM[$index]['Lookups_EntityName'],
						  'EntityId' => $id 
					     )
				),
				'OtherAttributes' => array(
					array( 'AttributeName' => 'new_count', 'Value' => (int)$total_count )
				)
			);
			
			//echo Utils::prettyPrint( $post_data, '$post_data' );

			$ret = CRM::sar_create( array('post_data' => $post_data ) );
			if ( !isset( $ret['Status'] ) || $ret['Status'] != 'OK' ) {
				// echo Utils::prettyPrint($post_data, 'Post Data');
				$this->error( array('error_description' => Utils::prettyPrint($ret, 'Data create error. Server response') ) );
				return;
			} // if
			
			//echo Utils::prettyPrint( $ret, '$ret' );

		} // foreach
		
		// DETAIILS delete
		// ******************************************************************
		foreach ($post_delete as $data_item) {

			$ret = CRM::sar_delete( array('post_data' => $data_item ) );
			//echo Utils::prettyPrint($ret, 'Data delete');
			if ( (!isset( $ret['Status'] ) || $ret['Status'] != 'OK') ) {
				//$this->error( array('error_description' => Utils::prettyPrint($ret, 'Data delete error. Server response') ) );
				//return;

				// exclude error catching
				
			} // if

		} // foreach

		// TOTAL update
		// ******************************************************************

		foreach ($post_total as $data_item) {
			$index = $data_item['index'];
			$id = $data_item['id'];
			$total_count = $data_item['total_count'];
			$car_id = $data_item['car_id'];

			if ( !isset(self::SAR_API_PARAM[$index]) ) continue;

			$post_data = array(
				'EntityName' => self::SAR_API_PARAM[$index]['Lookups_EntityName'],
				'Id' => $id,
				'Picklists' => array (
					array('AttributeName' => 'new_month', 'Value' => (int)$month+100000000)
				),
				'Lookups' => array(
								array('AttributeName' => 'new_account_id',
									  'EntityName' => 'account',
									  'EntityId' => $dealer
									  //'EntityId' => $this->account_id
								     ),
								array('AttributeName' => 'new_car_id',
									  'EntityName' => 'su_carcompl',
									  'EntityId' => $car_id 
								     )
							),
				'OtherAttributes' => array(
					array( 'AttributeName' => 'new_total_count', 'Value' => (int)$total_count ),
					array( 'AttributeName' => 'new_year', 'Value' => (int)$year )
				)
			);
			
			//echo Utils::prettyPrint( $post_data, '$post_data' );

			$ret = CRM::sar_update( array('post_data' => $post_data ) );
			if ( !isset( $ret['Status'] ) || $ret['Status'] != 'OK' ) {
				$this->error( array('error_description' => Utils::prettyPrint($ret, 'Data update error (total data). Server response') ) );
				return;
			} // if
			//echo Utils::prettyPrint( $ret, '$ret' );

		} // foreach


		redirect('sar', 'refresh');

	} // update
	
	/**
    * Filling out the form 
    * POST['SAR_testdrive_car_id'], POST['SAR_request_type']
    * author Suslov Igor <igirsuslov@gmail.com>
    * 2021.06.01
    **/
    public function requests_add(){
    	if(!empty($_POST['SAR_testdrive_car_id'])){
    		$_SESSION['SAR_testdrive_car_id'] = $_POST['SAR_testdrive_car_id'];
    	}
    	if(!empty($_POST['SAR_request_type'])){
    		$_SESSION['SAR_request_type'] = $_POST['SAR_request_type'];
    	}
    }	

	/*************************************************************
	*************************************************************
	  
	  EXCEL TABLE CREATING
	  
	***********************************************************  
	***********************************************************/

   /****************************************************************
     Daily (full) Report
	 @daily_report
	 @full_report
   ****************************************************************/

	// excel_print_table
	private function excel_print_table( $options = array() ) {

		/**
		* set showroom_traffic array
		**/
		$index = $options['index'];
		$sar_total_date = $options['sar_total_date'];
		$sheet = $options['sheet'];
		$start_row = $options['start_row'];

		if (!empty($sar_total_date[ $index ])){
			foreach ($sar_total_date[ $index ] as $key => $value) {
				/*
				foreach ($sar_total_date[ $index ][$key] as $item) {
					$table_body[$key][] = $item['value'];
				}
				*/
				$item = $sar_total_date[ $index ][$key];
				for ($i=0; $i<33; $i++) {
					if ( isset($item[$i]) ) {
						$table_body[$key][]= $item[$i]['value'];
					} else {
						$table_body[$key][]= '';
					}
				} // for
			} // foreach;
		} // if

		$table_body_rows = count($table_body);
		$last_table_row = $start_row + $table_body_rows + 2;

		/**
		 table header
		*/	
		$sheet->setCellValueByColumnAndRow(1, $start_row, $options['name']);
		$style = array(
			'font' => array(
				'color'     => array('rgb' => 'ffffff')
			),
			'fill' => array(
				'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => array('rgb' => '223464')
			)
		);
		$sheet->getStyle("A{$start_row}:AG{$start_row}")->applyFromArray($style);

		/**
		* start y
		*/
		$y = $start_row+1;
		/**
		* add showroom_traffic for excel
		*/
		$style = array(
			'font' => array(
				'color'     => array('rgb' => 'ffffff')
			)
		);
		$sheet->getStyle("A{$y}:AG{$y}")->applyFromArray($style);

		// bold last row (total)
		$style = array(
			'font' => array(
				'bold'     => true
			)
		);
		$sheet->getStyle("A{$last_table_row}:AG{$last_table_row}")->applyFromArray($style);

		// borders for table body
		$border = array(
			'borders'=>array(
				'inside' => array(
					'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
				),
				'outline' => array(
					'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
				)
			)
		);
		$start_table_body_row = $start_row + 2;
		$last_table_body_row = $last_table_row-1;
		//echo "A{$start_table_body_row}:A{$last_table_body_row}".'<br>';
		$sheet->getStyle("A{$start_table_body_row}:AF{$last_table_body_row}")->applyFromArray($border);

		$x = 1;
		foreach ($sar_total_date['SAR_titles'] as $SAR_titles) {
			$sheet->setCellValueByColumnAndRow($x, $y, $SAR_titles);
			$sheet->getStyleByColumnAndRow($x, $y)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('7030a0');
			$x++;
		}
		$y++;

		foreach ($table_body as $key => $value) {
			$x = 1;
			foreach ($table_body[$key] as $table_body_item) {
				$sheet->setCellValueByColumnAndRow($x, $y, $table_body_item);
				$x++;
			}
			$y++;
		}

		for ($i = $start_row+2; $i < $y; $i++) {
			$sheet->getStyleByColumnAndRow(33, $i)->getFill()->setFillType(PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('bfbfbf');
			$sheet->getStyle("AG{$i}")->getFont()->setBold(true);
		}

		$x = 1;
		foreach ($sar_total_date[$index.'_total'] as $sar_total_date_item) {
			$sheet->setCellValueByColumnAndRow($x, $y, $sar_total_date_item);
			$sheet->getStyleByColumnAndRow($x, $y)->getFill()->setFillType(PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('bfbfbf');
			$x++;
		}
		$y++;
		
		// all body cells with title center alligment
		$k = $start_table_body_row-1;
		$sheet->getStyle("B{$k}:AG{$last_table_row}")->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle("A{$start_table_body_row}:A{$last_table_row}")->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

		//echo "<script>console.log(`". json_encode( $last_table_row ) ."`);</script>";
		return $last_table_row;
	} // excel_print_table

	private function excel_print_diagram( $options = array() ) {

		//**********************************************************
		//**********************************************************

		// printing diagrams

		// *********************************************************
		// *********************************************************
		$start_row = $options['start_row'];
		$total_row_index = $options['total_row_index'];
		$sheet = $options['sheet'];
		$sheet_title = $sheet->getTitle();
		$xAxisRow =  $options['xAxisRow'];

		$start_row += 2;
		$chart_height = 25;

		$xAxisTickValues = array(
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $sheet_title.'!$B$'.$xAxisRow .':$AF$'.$xAxisRow, NULL, 31), //  Jan to Dec
		);
	

		$sheet->setCellValueByColumnAndRow(2, $start_row+2, 'Showroom traffic');
		$sheet->setCellValueByColumnAndRow(2, $start_row+3, 'Lead');
		$sheet->setCellValueByColumnAndRow(2, $start_row+4, 'Offer');
		$sheet->setCellValueByColumnAndRow(2, $start_row+5, 'Test drive');
		$sheet->setCellValueByColumnAndRow(2, $start_row+6, 'Sale');
		//$sheet->setCellValueByColumnAndRow(1, $start_row+7, $sheet_title );

		$dataseriesLabels1 = array(
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $sheet_title.'!$B$'.($start_row+2), NULL, 1), //  Temperature
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $sheet_title.'!$B$'.($start_row+6), NULL, 1), //  Humidity
		);
		$dataseriesLabels2 = array(
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $sheet_title.'!$B$'.($start_row+3), NULL, 1), //  Rainfall
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $sheet_title.'!$B$'.($start_row+4), NULL, 1), //  Rainfall
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $sheet_title.'!$B$'.($start_row+5), NULL, 1), //  Humidity
		);

		$dataSeriesValues1 = array(
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $sheet_title.'!$B$'.$total_row_index[ 'SAR_showroom_traffic' ].':$AF$'.$total_row_index[ 'SAR_showroom_traffic' ], NULL, 31),
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $sheet_title.'!$B$'.$total_row_index[ 'SAR_sale' ].':$AF$'.$total_row_index[ 'SAR_sale' ], NULL, 31),
		);
		$series1 = new DataSeries(
			DataSeries::TYPE_AREACHART, // plotType
			DataSeries::GROUPING_STANDARD, // plotGrouping
			range(0, count($dataSeriesValues1) - 1), // plotOrder
			$dataseriesLabels1, // plotLabel
			$xAxisTickValues, // plotCategory
			$dataSeriesValues1                              // plotValues
		);

		$dataSeriesValues2 = array(
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $sheet_title.'!$B$'.$total_row_index[ 'SAR_leads' ].':$AF$'.$total_row_index[ 'SAR_leads' ], NULL, 31, array(), array('symbol' => 'none')),
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $sheet_title.'!$B$'.$total_row_index[ 'SAR_offer' ].':$AF$'.$total_row_index[ 'SAR_offer' ], NULL, 31, array(), array('symbol' => 'none')),
			new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $sheet_title.'!$B$'.$total_row_index[ 'SAR_testdrive' ].':$AF$'.$total_row_index[ 'SAR_testdrive' ], NULL, 31, array(), array('symbol' => 'none') ),
		);
		
		foreach ($dataSeriesValues2 as $dataSeriesValue2) {
			$dataSeriesValue2->setLineWidth(40000);
		}
		
		$series2 = new DataSeries(
			DataSeries::TYPE_LINECHART, // plotType
			DataSeries::GROUPING_STANDARD, // plotGrouping
			range(0, count($dataSeriesValues2) - 1), // plotOrder
			$dataseriesLabels2, // plotLabel
			$xAxisTickValues, // plotCategory
			$dataSeriesValues2                              // plotValues
		);


		$layout1 = new Layout();
		//$layout1->setShowVal(TRUE);

		$plotarea = new PlotArea($layout1, array( $series1, $series2) );
		$legend = new Legend(Legend::POSITION_RIGHT, NULL, false);
		//$title = new Title('Chart awesome');
		$title = NULL;
		//  Create the chart
		$chart = new Chart(
			'chart1', // name
			$title, // title
			$legend, // legend
			$plotarea, // plotArea
			true, // plotVisibleOnly
			0, // displayBlanksAs
			NULL, // xAxisLabel
			NULL  // yAxisLabel
		);
		//  Set the position where the chart should appear in the worksheet
	
		$chart->setTopLeftPosition( 'A'.$start_row );
		$chart->setBottomRightPosition( 'AH'.($start_row+$chart_height) );


		//  Add the chart to the worksheet
		$sheet->addChart($chart);

		$start_row += $chart_height;

		return $start_row;

	} // excel_print_table

	private function print_excel_sheet( $options = array() ) {


		$sheet = $options['sheet'];
		$sar_total_date = $options['sar_total_date'];

				// white background for all sheet cells	
		$bg = array(
			'fill' => array(
				'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => array('rgb' => 'ffffff')
			)
		);
		//$sheet->getDefaultStyle()->applyFromArray($bg);
		$sheet->getStyle('A:AX')->applyFromArray($bg);

		// sheet header
	 	if ( isset($sar_total_date ['SAR_period_start']) ) {
			$sheet->setCellValueByColumnAndRow(2, 2, 'Sales Activity Report');
			$sheet->setCellValueByColumnAndRow(2, 3, 'As for:');
			$sheet->setCellValueByColumnAndRow(4, 3, $sar_total_date ['SAR_period_start']);
			$style = array(
				'font' => array(
					'bold'     => true
				)
			);
			$sheet->getStyle("B1:D3")->applyFromArray($style);
		};

		// printing all data tables in according SAR constant
		// *****************************************
		$start_row = 5;
		if ( isset($options['start_row']) ) $start_row  = $options['start_row'];
		$xAxisRow = $start_row+1;
		$total_row_index = array();
		foreach (self::SAR as $data_item) {
			
			$index = $data_item['value'];
			$name =  $data_item['name'];

			$options = array(
				'sar_total_date' => &$sar_total_date,
				'name' => $name,
	            'sheet' => $sheet,
	            'start_row' => $start_row,
	            'index' => $index);
		
			$start_row = $this->excel_print_table( $options );
			$total_row_index[ $index  ] = $start_row;
			$start_row += 2;

		} // foreach

		/**
		* column width setting
		*/
		$sheet->getColumnDimension('A')->setWidth(20);
		//$sheet->getColumnDimensionByColumn("A")->setAutoSize(true);

		foreach (range('B', 'Z') as $letter) {
			$sheet->getColumnDimension($letter)->setWidth(4);
		}
		foreach (range('A', 'F') as $letter) {
			$sheet->getColumnDimension('A'.$letter)->setWidth(4);
		}
		$sheet->getColumnDimension('AG')->setWidth(9);

		// SAR_total printing
		//************************************
		//echo Utils::prettyPrint($sar_total_date['SAR_total'], '$sar_total_date[SAR_total]');
		$start_row += 2;
		$start_row_SAR_total = $start_row;
		$format=[8,1,2,5,2,8]; // column increment
		$center=[1,2,4]; // array index for center text
		$bkg_colors=[ ['223464', 'ffffff'],
		              ['f00000', 'ffffff', '223464','223464'],
		              ['1a237e', 'ffffff', '223464','223464', '223464','223464'],
		              ['0070a1', 'ffffff', '223464','223464', '223464','223464'],
		              ['808080', 'ffffff', '223464','223464', '223464','223464']
		            ];
		foreach ($sar_total_date['SAR_total'] as $row_index => $row) {
			$column = 0;
			foreach ($row as $index => $cell) {
				$sheet->setCellValueByColumnAndRow($column+1, $start_row, $cell);
				if ($format[$index]>1) {
					$sheet->mergeCells(chr(65+$column).$start_row.':'.chr(65+$column+$format[$index]-1).$start_row);
				}

				// bkg color
				if ( isset($bkg_colors[$row_index]) && isset($bkg_colors[$row_index][$index]) ) {
					$sheet->getStyleByColumnAndRow($column+1, 
					   $start_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($bkg_colors[$row_index][$index]);
				}

				// center text formst
				if ( in_array($index, $center) ) {
					$sheet->getStyleByColumnAndRow($column+1, $start_row)->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				}

				// vertical centering for all cell
				$sheet->getStyleByColumnAndRow($column+1, $start_row)->getAlignment()->setVertical(PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


				// border for EACH cell
				for ($j=$column; $j<$column+$format[$index]; $j++) {
					$sheet->getStyleByColumnAndRow($j+1, $start_row)->applyFromArray(array(
						'borders' => array(
							'bottom'=> array('borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN),
							'right'	=> array('borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN),
							'top'	=> array('borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN),
							'left'	=> array('borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
						)
					));
				}

				$column += $format[$index];
			}
			
			// row height
			$sheet->getRowDimension($start_row)->setRowHeight(30);
			$start_row++;


			//echo Utils::prettyPrint($item, 'item');
		} // foreach
		
		// white color for all cells
		$style = array(
			'font' => array(
				'color'     => array('rgb' => 'ffffff')
			)
		);		
		$sheet->getStyle("A{$start_row_SAR_total}:Z{$start_row}")->applyFromArray($style);

		// black and bold font for first column
		$style = array(
			'font' => array(
				'color'    => array('rgb' => '000000'),
				'bold'     => true
			)
		);	
		$sheet->getStyle( chr(65+$format[0]).$start_row_SAR_total.':'.chr(65+$format[0]).$start_row )->applyFromArray($style);

		// diagram printing
		// XYZ
		$options = array( 'start_row' => $start_row,
			              'sheet' => $sheet,
			              'xAxisRow' => $xAxisRow,
			              'total_row_index' => $total_row_index );

		$start_row = $this->excel_print_diagram( $options );

		//analytics printing
		//************************************************************

		$start_row += 2;
		$start_analysis_row = $start_row;
		//echo Utils::prettyPrint($sar_total_date['SAR_analysis'], "sar_total_date['SAR_analysis']");
		foreach ($sar_total_date['SAR_analysis'] as  $row_index => $row) {
			
			$column = 0;
			foreach ($row as $index => $cell) {
				// format for header
				if ( $row_index == 0) {
					// row height
					$sheet->getRowDimension($start_row)->setRowHeight(20);
					// aligments
					$sheet->getStyleByColumnAndRow($column+1, $start_row, $column+1+3, $start_row)->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
					$sheet->getStyleByColumnAndRow($column+1, $start_row, $column+1+3, $start_row)->getAlignment()->setVertical(PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
					//echo $column+1;
					//echo '<br>';
					$sheet->getStyleByColumnAndRow($column+1, $start_row, $column+1+3, $start_row)->applyFromArray(array(
						'font' => array(
							'color'    => array('rgb' => 'eeeeee'),
							'bold'     => true
						),
						'fill' => array(
							'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
							'startColor' => array('rgb' => '223464')
						)		
					));
					
				}
				
				$sheet->setCellValueByColumnAndRow($column+1, $start_row, $cell);

				// merge cell
				$sheet->mergeCells(chr(65+$column).$start_row.':'.chr(65+$column+3).$start_row);


				for ($j=0; $j<4; $j++) {
					// border for EACH cell
					$sheet->getStyleByColumnAndRow($column+$j+1, $start_row)->applyFromArray(array(
						'borders' => array(
							'bottom'=> array('borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN),
							'right'	=> array('borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN),
							'top'	=> array('borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN),
							'left'	=> array('borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
						)
					));
				} // for



				$column += 4;
			} // foreach
			
			$start_row++;
		} // foreach

		$sheet->getStyle("A".$start_analysis_row.":X".$start_row)->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	} // print_excel_sheet

	public function excel( $f_options = array() ) {

		$today = date('d.m.Y');
		
		if ( isset( $f_options['spreadsheet'] ) ) {
			$document = $f_options['spreadsheet'];
			$sheet = $document->createSheet();
		} else {	
			$document = new Spreadsheet();
			$sheet = $document->setActiveSheetIndex(0);
		}
		
		$firstSheetName = 'TOTAL_PCU';
		
		if ( (isset($_SESSION['access_employees']) && $_SESSION['access_employees'] != true) ) {

			$this->load->model('Exel_model');
			$user_info = $this->Exel_model->get_user_info();
			//echo Utils::prettyPrint($user_info, 'user_info');
			if ( !empty($user_info[0]['rrdi']) ) {
				$firstSheetName =  $user_info[0]['rrdi'];
			} else {	
				if ( !is_null($this->dealer) ) {
					
					$dealers = $this->load_dealers();
					foreach ($dealers as $dealer) {
						if ( $dealer['AccountId'] == $this->dealer ) {
							$firstSheetName = $dealer['Code'];
							break;
						}
					}
					// echo Utils::prettyPrint($dealers, 'dealers');
				}
				
			} // if
			//settype($firstSheetName, "string");

		} // if
		$sheet->setTitle($firstSheetName);

		if ( $f_options['sar_excel'] ) {
			$sar_total_date = $f_options['sar_excel'];
		} else {
			//$sar_total_date = json_decode( $this->input->post('sar_excel'), true);
			$sar_total_date = $_GET;
		}

		$period_start = $sar_total_date['SAR_period_start'];
		$period_start_arr = explode('-', $period_start);
		$load_data_options = array( 'month' => $period_start_arr[1], 'year' => $period_start_arr[0], 'day' =>  $period_start_arr[2] );

		if ( !is_null($this->dealer) && $f_options['access_employees'] !== true) $load_data_options['dealer'] = $this->dealer;


		//echo Utils::prettyPrint($sar_total_date, 'sar_total_date');
		// echo Utils::prettyPrint($sar_total_date['SAR_analysis'], 'sar_total_date');
		//return;
		
		if ( !isset($sar_total_date['SAR_titles']) ) {
			$this->load_data( $load_data_options );
			$sar_total_date = $this->data;
			$sar_total_date['SAR_period_start'] = $period_start;
		}

		// this sheet is printing always
		$options = array( 'sheet' => $sheet,
	              		  'sar_total_date' => $sar_total_date);
		$this->print_excel_sheet( $options );

		// other sheets printing by condition
		// *****************************************************
		if ( 	isset( $f_options['skip_dealers'] ) == false && 
				(
					(isset($_SESSION['access_employees']) && $_SESSION['access_employees'] === true) || $f_options['access_employees'] === true
				)
			) {

			// load base data
			$dealers = $this->load_dealers();
			
			// create DEALERS sheet
			//************************************************************
			$sheet = $document->createSheet();
			$sheet->setTitle('DEALERS');

			// title
			$title = [['#', 'City', 'Dealer Name', 'Dealer Code']];
			$sheet->fromArray($title, NULL, 'A1');

			$arr = array();
			$i=1;
			foreach ($dealers as $dealer) {
				if (empty($dealer['Code']) || $dealer['Code'] == '0000001') continue;
				$arr[] = [$i, $dealer['City'], $dealer['Name'], $dealer['Code']];
				$i++;
			} // foreach
			$sheet->fromArray($arr, NULL, 'A2');

			// add style for DELERS sheet
			$sheet->getStyle("A1:D1")->applyFromArray(array(
				'font' => array(
					'color'    => array('rgb' => 'ffffff'),
					'bold'     => true
				),
				'fill' => array(
					'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'startColor' => array('rgb' => '223464')
				)		
			));

			$sheet->getStyle("A1:D".($i))->applyFromArray(array(
				'borders'=>array(
					'inside' => array(
						'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
					),
					'outline' => array(
						'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					)
				)		
			));

			$sheet->getStyle("A1:D1")->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			$sheet->getColumnDimensionByColumn("A")->setAutoSize(true);
			$sheet->getColumnDimension("B")->setWidth(16);
			$sheet->getColumnDimension("C")->setWidth(19);
			$sheet->getColumnDimension("D")->setWidth(11);


			// create sheets for every DEALERS
			//************************************************************
			// $i=0;
				
			foreach ($dealers as $dealer) {
				if (empty($dealer['Code']) || $dealer['Code'] == '0000001') continue;

				$load_data_options['dealer'] = $dealer['AccountId'];
				$this->load_data($load_data_options);
				
				$sheet = $document->createSheet();
				$title = substr($dealer['Code'], -3);
				//echo $title.'<br>';
				$sheet->setTitle( $title );

				// header for dealer sheet
				$header = [
					['Дилер:', '', $dealer['Name']],
					['Код дилера:', '', $dealer['Code']],
					['Дата заполнения:', '', $today]
				];
				$sheet->fromArray($header, NULL, 'A1');
				// bold for header
				$style = array(
					'font' => array(
						'bold'=> true
					),
					'fill' => array(
						'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
						'startColor' => array('rgb' => 'ffffff')
					)
				);
				$sheet->getStyle("A1:C3")->applyFromArray($style);
			
				$excel_sheet_options = array( 
					'sheet' => $sheet,
					'sar_total_date' => $this->data,
					'start_row' => 4
				);

				$this->print_excel_sheet( $excel_sheet_options );
				// if ($i==2) break;
				// $i++;
			} // foreach
				
		} // if

		$sheet = $document->setActiveSheetIndex(0);

		/**
		* save
		*/
		if ( isset($f_options['spreadsheet']) ) return;
			
		$writer = IOFactory::createWriter($document, 'Xlsx');
		$writer->setIncludeCharts(TRUE);

		if ( !empty($f_options['filename']) ) {

			$writer->save( $f_options['filename'] );
			
		} else {

			header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Content-type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=Daily_Report(".$sar_total_date['SAR_period_start'].").xlsx");

			$writer->save('php://output');

			/**
			 * preloader hide 
			 * @author Suslov Igor
			 */
			// echo '<script>$(".loading_SAR").hide();</script>';

			exit();
		}

	} // excel

		// require_once __DIR__.'/Sar_models_report.php';

	/** *
	 * SAR project part 2
	 * models report
	 * @author aws
	 * @version 2021-05-2*
	 */
     /****************************************************************
     Models Report
	 @models_report
   ****************************************************************/

	private function cc_diagram( $options = array() ) {

		$dataSeriesLabels = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $options['labels'],     null, 1, [], NULL, 
			                                                    isset($options['dataSeries_color']) ? $options['dataSeries_color'] : '632523') ];
		$xAxisTickValues =  [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $options['xAxis'],      null, 3) ];
		$dataSeriesValues = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $options['dataSeries'], null, 3) ];
			                   //isset($options['dataSeries_color']) ? $options['dataSeries_color'] : '632523' ) ];


		// Build the dataseries
		$series = new DataSeries(
		    DataSeries::TYPE_BARCHART, // plotType
		    DataSeries::GROUPING_STANDARD, // plotGrouping
		    range(0, count($dataSeriesValues) - 1), // plotOrder
		    $dataSeriesLabels, // plotLabel
		    $xAxisTickValues, // plotCategory
		    $dataSeriesValues       // plotValues
		);

		// Set the series in the plot area
		$layout = new Layout([], array('barChart' => array( 'label_bold' => true ) ));
		$layout->setShowVal(true);

		$plotArea = new PlotArea($layout, [$series], array( 'background_color' => "dce6f2"));

		// Set the chart legend
		$legend = new Legend(Legend::POSITION_BOTTOM, null, false);

		$title = new Title( $options['title_prefix'].' CC movement', null,  new Font());
		$title->getFont()->setSize(10);

		//$title->font = new Font();
		//$title->font->setSize(10);

		//$title->getLayout()->setHeight(10);
		//->getFont()->setSize(10);

		$xAxisLabel = new Title('Counts');
		$yAxisLabel = new Title('Values');

		// Create the chart
		$chart = new Chart(
		    'stock-chart', // name
		    $title, // title
		    $legend, // legend
		    $plotArea, // plotArea
		    true, // plotVisibleOnly
		    //DataSeries::EMPTY_AS_GAP, // displayBlanksAs
		    'gap',
		    null, // xAxisLabel
		    null  // yAxisLabel
		);
		$chart->getMajorGridlines()->setLineStyleProperties(0);
		//echo $chart->getMajorGridlines()->getLineStyleProperty('noFill').'<br>';
		//$mgl->setLineStyleProperties(0);

		//$mgl->setLineColorProperties('dce6f2');

		// Set the position where the chart should appear in the worksheet
		$chart->setTopLeftPosition($options['topLeft']);
		$chart->setBottomRightPosition($options['bottomRight']);
		$chart->setBottomRightOffset(0, 0);
		// Add the chart to the worksheet
		$options['diagram_sheet']->addChart($chart);


	} // cc_diagram

	private function shr_diagram( $options = array() ) {

		$dataSeriesLabels_1 = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $options['labels_1'],     null, 1, [], NULL, 
			                             isset($options['dataSeries_1_color']) ? $options['dataSeries_1_color'] : '604a7b') ];
		$dataSeriesLabels_2 = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $options['labels_2'],     null, 1) ]; // [], NULL, 'FF0000'
		$xAxisTickValues =    [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $options['xAxis'],        null, 3) ];
		$dataSeriesValues_1 = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $options['dataSeries_1'], null, 3) ];
		$dataSeriesValues_2 = [ new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $options['dataSeries_2'], null, 3, [], 
			              array('symbol' => 'circle', 'size' => 7)) ];

		$dataSeriesValues_2[0]->setLineWidth(40000);

		// Build the dataseries
		$series_1 = new DataSeries(
		    DataSeries::TYPE_BARCHART, // plotType
		    DataSeries::GROUPING_STANDARD, // plotGrouping
		    range(0, count($dataSeriesValues_1) - 1), // plotOrder
		    $dataSeriesLabels_1, // plotLabel
		    $xAxisTickValues, // plotCategory
		    $dataSeriesValues_1       // plotValues
		);

		$series_2 = new DataSeries(
		    DataSeries::TYPE_LINECHART, // plotType
		    DataSeries::GROUPING_STANDARD, // plotGrouping
		    range(0, count($dataSeriesValues_2) - 1), // plotOrder
		    $dataSeriesLabels_2, // plotLabel
		    $xAxisTickValues, // plotCategory
		    $dataSeriesValues_2       // plotValues
		);


		// Set the series in the plot area

		//  label_color' => 'FFFFFF'
		// 'label_position' => 'inEnd'
		// 'label_position' => 't'
		$layout = new Layout([], array('barChart' => array( 'label_bold' => true ),
									   DataSeries::TYPE_LINECHART => array( 'label_bold' => true, 'label_position' => 'l', 'label_color' => 'c0504d')
									  ));
		$layout->setShowVal(true);
		//$layout->setWidth(400);

		$plotArea = new PlotArea($layout, [$series_1], array( 'background_color' => "dce6f2"), [$series_2] );
		//$addParam = $plotArea->getAddParam();
		//echo Utils::prettyPrint($addParam, '$addParam');

		// Set the chart legend
		$legend = new Legend(Legend::POSITION_BOTTOM, null, false);


		$title = new Title( $options['title_prefix'].' SHR traffic & Closing ratio movement', null, new Font() );
		$title->getFont()->setSize(10);

		$ss = $title->getFont();
		//$title->getFont()->setSize(10);

		//$title->font = new Font();
		//$title->font->setSize(8);

		//$title->getLayout()->setHeight(10);
		//->getFont()->setSize(10);

		$xAxisLabel = new Title('Counts');
		$yAxisLabel = new Title('Values');
		$secondaryYAxisLabel = new Title('');
		// Create the chart
		$chart = new Chart(
		    'stock-chart-1', // name
		    $title, // title
		    $legend, // legend
		    $plotArea, // plotArea
		    true, // plotVisibleOnly
		    //DataSeries::EMPTY_AS_GAP, // displayBlanksAs
		    'gap',
		    null,  // xAxisLabel
		    null,  // yAxisLabel
			null,  // xAxis
			null,  // yAxis
			null,  // majorGridlines
			null,  // minor Gridlines
			$secondaryYAxisLabel    // secondaryYAxisLabel			
		);
		$chart->getMajorGridlines()->setLineStyleProperties(0);

		// Set the position where the chart should appear in the worksheet
		$chart->setTopLeftPosition($options['topLeft']);
		$chart->setBottomRightPosition($options['bottomRight']);
		$chart->setBottomRightOffset(0, 0);
		// Add the chart to the worksheet
		$options['diagram_sheet']->addChart($chart);


	} // shr_diagram


	// excel report  for employes
	public function excel_models_report( $options = array() ) {
		//echo 11;
		// echo Utils::prettyPrint($options, 'excel_models_report options');
		// print_r( $options );

		if ( isset($options['period_start']) ) {
			$period_start = $options['period_start'];
		} else {
			$period_start = $this->session->SAR_period_start;
		}
		//

		if ( !isset( $period_start ) ) $period_start = date('Y-m-d');
		$period_start_arr = explode('-', $period_start);
		$day =  $period_start_arr[2];
		$options_date = array( 'month' => $period_start_arr[1], 'year' => $period_start_arr[0], 'day' => $day  );
		
		// load data
		$this->load_models_report_data( $options_date );

		//echo Utils::prettyPrint($this->data, 'this->data');

		$mr = $this->data['SAR_MR'];
		$mr_total = $this->data['SAR_MR_total'];
		$mr_index = $this->data['SAR_MR_index'];

		//echo print_r( $this->data['SAR_MR_total'] );

		// Create new Spreadsheet object
		if ( isset($options['spreadsheet']) ) {
			$spreadsheet = $options['spreadsheet'];
		} else {
			$spreadsheet = new Spreadsheet();
		}
		// Set document properties
		$spreadsheet->getProperties()->setCreator('SAR EXCEL REPORT')
		    ->setLastModifiedBy('SAR EXCEL REPORT')
		    ->setTitle('Office 2007 XLSX Test Document')
		    ->setSubject('Office 2007 XLSX Test Document')
		    ->setDescription('MODELS REPORT, generated using SAR EXCEL REPORT.')
		    ->setKeywords('office 2007 openxml php')
		    ->setCategory('Models Report');

		$diagram_sheet = $spreadsheet->setActiveSheetIndex(0);
		$diagram_sheet->setTitle('Models_Report');
		$sheet = $spreadsheet->addSheet(new Worksheet($spreadsheet));
		$sheet->setTitle('Models_Data');

		// header
		// *************************************************************
		$row=0;
		$sheet->setCellValue('A'.++$row, 'Model Name');
		$row++; $col=1; 
		$diagram_labels = array();
		foreach( self::SAR_MODELS_REPORT as $key => $smr) {
			$sheet->setCellValueByColumnAndRow(++$col, $row-1, $smr['name']);
			$diagram_labels[ $smr['name'] ] = 'Models_Data!$'.chr(65+$col-1).'$'.($row-1);

		    $sheet->setCellValueByColumnAndRow($col, $row, $mr_index['prev_prev']);
		    $sheet->setCellValueByColumnAndRow(++$col, $row, $mr_index['prev']);
		    $sheet->setCellValueByColumnAndRow(++$col, $row, $mr_index['current']);
		    $sheet->setCellValueByColumnAndRow(++$col, $row, 'Diff');
		    $sheet->setCellValueByColumnAndRow(++$col, $row, 'Diff(%)');
		    if ($smr['value'] != 'CR') $sheet->setCellValueByColumnAndRow(++$col, $row, 'Share');

		} // foreach
		$sheet->getColumnDimension('A')->setWidth(17);
		$sheet->getStyleByColumnAndRow(1, 1, $col, 1)->getFont()->setBold(true);

		//echo Utils::prettyPrint($diagram_labels, '$diagram_labels');

		// body
		// **************************************************
		$first_body_row = $row+1;
		foreach ($mr as $item) {
			// car name
			//$row++;
		    //$sheet->setCellValue('A'.$row, $item['value']);
		    $sheet->setCellValueByColumnAndRow(1, ++$row, $item['value']);

		    // calced data
		    $col=1;
		    foreach( self::SAR_MODELS_REPORT as $smr) {
		    	$name = $smr['value'];

		    	$sheet->setCellValueByColumnAndRow(++$col, $row, $item[$name]['prev_prev']);
		    	$sheet->setCellValueByColumnAndRow(++$col, $row, $item[$name]['prev']);
		    	$sheet->setCellValueByColumnAndRow(++$col, $row, $item[$name]['current']);
		    	$sheet->setCellValueByColumnAndRow(++$col, $row, $item[$name]['diff']);
		    	$sheet->setCellValueByColumnAndRow(++$col, $row, $item[$name]['diff_percent']);
		    	if ($name != 'CR') $sheet->setCellValueByColumnAndRow(++$col, $row, $item[$name]['share']);

			} // foreach

		} // foreach

		$sheet->getStyleByColumnAndRow(1, $first_body_row, 1, $row)->getAlignment()->setHorizontal( \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

		// total
		// ********************************************************
		$row++; $col=1;
		$total_row = $row;
		$sheet->setCellValue('A'.$row, 'TOTAL');

		//echo print_r($mr_total);

		foreach( self::SAR_MODELS_REPORT as $smr ) {
			$name = $smr['value'];
		    $sheet->setCellValueByColumnAndRow(++$col, $row, $mr_total[$name]['prev_prev']);
		    $sheet->setCellValueByColumnAndRow(++$col, $row, $mr_total[$name]['prev']);
		    $sheet->setCellValueByColumnAndRow(++$col, $row, $mr_total[$name]['current']);
		    $sheet->setCellValueByColumnAndRow(++$col, $row, $mr_total[$name]['diff']);
		    $sheet->setCellValueByColumnAndRow(++$col, $row, $mr_total[$name]['diff_percent']);
		    if ($name != 'CR') $sheet->setCellValueByColumnAndRow(++$col, $row, $mr_total[$name]['share']);
		} // foreach
		$sheet->getStyleByColumnAndRow(1, $row, $col, $row)->getFont()->setBold(true);

		// cell format 
		// *********************************************************
		$col=1;
		foreach( self::SAR_MODELS_REPORT as $smr ) {
			$col += 6;
			$sheet->getStyleByColumnAndRow($col-1, 3, $col, $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
			//echo $smr['value'];
			if ($smr['value'] == 'CR') {
				$sheet->getStyleByColumnAndRow($col-5, 3, $col-2, $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
			}
		} // foreach

		// *********************************************************************
		// DIAGRAMS
		// *********************************************************************
		
		$number = 1;
		//echo json_encode( getCoordinate($number) );
		$this->cc_diagram( array(
		                   'labels' => 'Models_Data!$H$1',
						   'xAxis' => 'Models_Data!$B$2:$D$2',
		                   'dataSeries' => 'Models_Data!$H$'.$total_row.':$J$'.$total_row,
		                   'dataSeries_color' => '31859c',
		                   'title_prefix' => 'TTL', 
		                   'topLeft' => getCoordinate($number)['topLeft'], 
		                   'bottomRight' => getCoordinate($number)['bottomRight'],
		                   'diagram_sheet' => $diagram_sheet
		                  ));
		$number++;
		$this->shr_diagram( array(
		                   'labels_1' => 'Models_Data!$B$1',
		                   'labels_2' => 'Models_Data!$N$1',
						   'xAxis' => 'Models_Data!$B$2:$D$2',
		                   'dataSeries_1' => 'Models_Data!$B$'.$total_row.':$D$'.$total_row,
		                   'dataSeries_1_color' => '17375e',
		                   'dataSeries_2' => 'Models_Data!$N$'.$total_row.':$P$'.$total_row,
		                   'title_prefix' => 'TTL', 
		                   'topLeft' => getCoordinate($number)['topLeft'],
		                   'bottomRight' => getCoordinate($number)['bottomRight'],
		                   'diagram_sheet' => $diagram_sheet
		                  ));



		// car foreach for diagram building
		// **********************
		foreach ($mr as $ind => $item) {
			// @debug
			//break;
			$number++;
			$this->cc_diagram( array(
		                   'labels' => 'Models_Data!$H$1',
						   'xAxis' => 'Models_Data!$B$2:$D$2',
		                   'dataSeries' => 'Models_Data!$H$'.($first_body_row+$ind).':$J$'.($first_body_row+$ind),
		                   'title_prefix' => $item['value'], 
		                   'topLeft' => getCoordinate($number)['topLeft'], 
		                   'bottomRight' => getCoordinate($number)['bottomRight'],
		                   'diagram_sheet' => $diagram_sheet
		                  ));
			$number++;
			$this->shr_diagram( array(
		                   'labels_1' => 'Models_Data!$B$1',
		                   'labels_2' => 'Models_Data!$N$1',
						   'xAxis' => 'Models_Data!$B$2:$D$2',
		                   'dataSeries_1' => 'Models_Data!$B$'.($first_body_row+$ind).':$D$'.($first_body_row+$ind),
		                   'dataSeries_2' => 'Models_Data!$N$'.($first_body_row+$ind).':$P$'.($first_body_row+$ind),
		                   'title_prefix' => $item['value'],
		                   'topLeft' => getCoordinate($number)['topLeft'],
		                   'bottomRight' => getCoordinate($number)['bottomRight'],
		                   'diagram_sheet' => $diagram_sheet
		                  ));
		
		} // foreach


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->setIncludeCharts(true);		

		if ( !empty($options['filename']) ) {

			$writer->save( $options['filename'] );
			
		} elseif ( !empty($options['spreadsheet']) ) {
			
			return;
			
		} else {

			// Redirect output to a client’s web browser (Xlsx)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="models_report.xlsx"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
			header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header('Pragma: public'); // HTTP/1.0
			
			$writer->save( 'php://output' );
			exit;
		}
		

	} // excel_models_report 


	/********************************************************
	  send email FOR DEALERS
	*********************************************************/
	public function build_dealer_reports(&$log_msg) {
		
		$t0 = microtime(true);
		
		$today = date('d.m.Y');
				
		// options for excel builders
		$options = array($options);
		$options['period_start'] = date('Y-m-d');;
		$options['skip_dealers'] = true; // to skip dealers sheets
	
		// options for data loader
		$period_start_arr = explode('-', $options['period_start']);
		$load_data_options = array( 'month' => $period_start_arr[1], 'year' => $period_start_arr[0], 'day' =>  $period_start_arr[2] );
		
		$total_count = 0;
		foreach(self::ORGANIZATIONS as $organization) {
			
    		CRM::$_organization = $organization;
    		
			// empty dealer for total pcu sheet
			unset($load_data_options['dealer']); 
			// load data
			$dealers = $this->load_dealers();
			$this->load_cars_data();
			$this->load_data($load_data_options);
		
			// create new spreadsheet object
			$spreadsheet = new Spreadsheet();
			$options['spreadsheet'] = $spreadsheet;
			
			// build excel_model_report
			$this->excel_models_report($options);
			
			// build excel_daily report
			$options['sar_excel'] = $this->data;
			$options['sar_excel']['SAR_period_start'] = $options['period_start'];
			$this->excel($options);

			$count = 0;
			foreach ($dealers as $dealer) {
				
				if (empty($dealer['Code']) || $dealer['Code'] == '0000001') continue;
				$count++;
				
				$load_data_options['dealer'] = $dealer['AccountId'];
				$this->load_data($load_data_options);
				
				$sheet = $spreadsheet->createSheet();
				$title = substr($dealer['Code'], -3);
				//echo $title.'<br>';
				$sheet->setTitle( $title );

				// header for dealer sheet
				$header = [
					['Дилер:', '', $dealer['Name']],
					['Код дилера:', '', $dealer['Code']],
					['Дата заполнения:', '', $today]
				];
				$sheet->fromArray($header, NULL, 'A1');
				// bold for header
				$style = array(
					'font' => array(
						'bold'=> true
					),
					'fill' => array(
						'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
						'startColor' => array('rgb' => 'ffffff')
					)			
				);
				$sheet->getStyle("A1:C3")->applyFromArray($style);
			
				$excel_sheet_options = array( 
					'sheet' => $sheet,
					'sar_total_date' => $this->data,
					'start_row' => 4
				);

				$this->print_excel_sheet( $excel_sheet_options );
				// if ($i==2) break;
				// $i++;
				
				$filename = './sar_reports/dealer_reports/'.$organization.'/'.$title.'_dealer_report.xlsx';
				
				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				$writer->setIncludeCharts(true);		
				$writer->save( $filename );
				
				$spreadsheet->removeSheetByIndex( $spreadsheet->getIndex($sheet) );
				
				//if ($count>5) break;
			} // foreach $dealers
			
			$total_count += $count;		
			echo "<b>".$organization."</b> build_dealer_reports finished (".$count." files)<br>";
		} // foreach $organization
		echo "<hr><b>ALL</b> build_dealer_reports finished (".$total_count." files)<br>";
		
		$t1 = microtime(true);
		$log_msg .= "successfuly created ".$total_count." dealers report files. Elapsed time, s: ". round($t1-$t0, 2).PHP_EOL;
		echo "Elapsed time: ". round($t1-$t0, 2) ." s.";

	} // build_dealer_reports
	
	
	// *********************************************depricated
	// depricated 2021-10-02
	// *********************************************depricated
	public function _____send_email_excel_toDealers() {
		
		$today = date('d.m.Y');
	
		$config = $this->config->item('dealer_report');
		if (empty($config)) {
			echo '<div style="color:red">Not found "dealer_report" section in config file "config/sar_congig"<br>Controller abort.</div>';
			return;
		};
		$config = Utils::render_config($config, $today);

		$flagToSend = false;
		if ( date("H:i") == $config['cron_time'] ) $flagToSend = true;
		
		$max_emails_inLetter = 4;
		if ( isset($config['max_emails_inLetter']) && $config['max_emails_inLetter'] >=1 && $config['max_emails_inLetter'] <= 20 )
			$max_emails_inLetter = $config['max_emails_inLetter'];
			
		echo "<h1>send_email_excel_toDealers</h1>";
		echo '<hr>';
		
		$log_file = './sar_reports/send_email_excel_toDealers.log';
		$log_msg = '';
		$log_msg .= 'send_email_excel_toDealers started at  '.date('Y-m-d H:i:s').PHP_EOL;
		//$log_msg .= 'flagToSend='.(int)$flagToSend.PHP_EOL;
		
		// render config
		echo Utils::prettyPrint($config, 'config(rendered) from "config/sar_congig.php", "dealer_report" section:');
		echo '<hr>';
		
		// @cheat
		$cheat_list = [];
		if ( count($cheat_list) > 0) echo "Count of cheatList=".count($cheat_list).'<br>';
		
		foreach ($cheat_list as $key => $value) {
			$arr = explode(', ', $value);
			sort($arr);
			$cheat_list[$key] = implode(', ', $arr);
		}
	
		//	build dealer reports
		// ***********************************************
		$this->build_dealer_reports($log_msg);
		$t1 = microtime(true);
		
		// generate users email array
		// *****************************************
		echo '<h3>generate users email</h3>';
		
		$users = array();
		$count_email = 0;
		$count_skip_byAccessPosition = 0;
		$users_debug = array(); 
		$ACCESS_POSITION = ['100000000', '100000001', '100000004'];
		foreach(self::ORGANIZATIONS as $organization) {
			
			// load dealers
			CRM::$_organization = $organization;
			$dealers = $this->load_dealers();
			$dealers_index = array();
			foreach ($dealers as $dealer) {
				$dealers_index [ $dealer['AccountId'] ] = $dealer;
			}
			// echo Utils::prettyPrint($dealers, 'dealers');
			
			// load users
			$organization_users = CRM::sar_dealers_users( array( 'organization' => $organization) );
			// echo Utils::prettyPrint($organization_users, 'organization_users');
			
			$users[ $organization ] = array();
			$users_debug[ $organization ] = array();
			foreach ( $organization_users['Result']['Data'] as $user) {
					
				if ( empty($user['New_email']) ) {
					echo '<div style="color:magenta">User '.$user['New_name'].' '.$user['New_lastname'].' ('.$organization.') has not an email. Skipped.</div>';
					continue;
				}
				
				if ( $user['New_access_employees'] == true ) {
					echo '<div style="color:magenta">User '.$user['New_email'].' ('.$organization.'). has access_employees. Skipped.</div>';
					continue;
				}
				if (!in_array($user['New_position'], $ACCESS_POSITION )) {
					// echo '<div style="color:magenta">User '.$user['New_email'].' ('.$organization.'). has wrong access position: "'.$user['New_position'].'". Skipped.</div>';
					$count_skip_byAccessPosition++;
					continue;
				}
				
				$dealer = $dealers_index[ $user['New_dealer'] ];
				if ( !isset($dealer) ) {
					echo '<div style="color:red">For user '.$user['New_email'].' ('.$organization.') not found dealer by id "'.$user['New_dealer'].'"</div>';
					continue;
				}
				
				$title = substr($dealer['Code'], -3);	
				$filename = './sar_reports/dealer_reports/'.$organization.'/'.$title.'_dealer_report.xlsx';
				if ( !file_exists($filename) ) {
					echo '<div style="color:red">For user '.$user['New_email'].' ('.$organization.') not found report file "'.$filename.'"</div>';
					continue;
				} // if
				
				if ( !isset( $users[ $organization ][ $title ] ) ) {
					$users[ $organization ][ $title ] = array('dealer_name' => $dealer['Name'],
				                                              'filename' => $filename,
															  'emails' => [] );
				}
				
				if ( in_array($user['New_email'], $users[ $organization ][ $title ][ 'emails' ] ) ) {
					echo '<div style="color:magenta">Dublicate user '.$user['New_email'].' ('.$organization.'). Skipped.</div>';
					continue;
				}
				
				$count_email++;
				$users_debug[ $organization ][$title][] = $user['New_email'].', position: '.$user['New_position'].', dealer name: '.$dealer['Name'].', filename: \''.$filename.'\'';
				$users[ $organization ][ $title ][ 'emails' ][] = $user['New_email'];
			} // foreach

		} // foreach organizations
		$t2 = microtime(true);
		
		echo '<h3 style="color:magenta">'.$count_skip_byAccessPosition.' contacts was skiped due to access position.</h3>';
		echo '<hr>';
		
		// create letters
		// used $max_emails_inLetter from config
		
		$count_letter = 0;
		$count_dealer = 0;
		foreach($users as $organization => $organization_dealers) {
			foreach ($users[$organization] as $dealer_key => $organization_dealer) {
				sort($users[$organization][$dealer_key]['emails']);
				$users[$organization][$dealer_key]['count_emails'] = count($users[$organization][$dealer_key]['emails']);
				$count_dealer++;
				$letters = [];
				$count=0;
				$email_list = '';
				foreach( $users[$organization][$dealer_key]['emails'] as $email ) {
					$count++;
					if ( $count >=  $max_emails_inLetter ) {
						$letters[] = $email_list.', '.$email;
						$count_letter++;
						$email_list = '';
						$count = 0;
					} else {
						if ($count>1) $email_list .= ', ';
						$email_list .= $email;
					}
				} // foreach emails
				if ($email_list != '') {
					$count_letter++;
					$letters[] = $email_list;
				}
				$users[$organization][$dealer_key]['letters']=$letters;
			} // foreachorganization_dealer
		} // users
				
		//echo Utils::prettyPrint($users_debug, 'final email list ('.$count_email.')');
		echo Utils::prettyPrint($users, 'final email list (email addresses: '.$count_email.', dealers: '.$count_dealer.', letters: '.$count_letter.')');
		$log_msg .= 'Email list has '.$count_email.' recepients. Dealers: '.$count_dealer.', Letters: '.$count_letter.
				      '. Elapsed time, s: '.round($t2-$t1, 2).PHP_EOL;
		
    	// send email
    	// ******************************

    	$this->load->library('email');
				
	    if ($config['debug'] ===true) $flagToSend = true;
		$count_sended=0;
		$count_dealer_sended=0;
		$count_emails_sended=0;
		
		foreach($users as $organization => $organization_dealers) {
			foreach ($organization_dealers as $organization_dealer) {
				$filename = $organization_dealer['filename'];
				
				//@cheat
				//if ( !in_array( $email, $cheat_list ) ) continue;
								
				// if ( count($organization_dealer['letters'])==1 ) continue;
				
				$count_dealer_sended++;
				foreach ($organization_dealer['letters'] as $key => $email) {
										
					if ($config['debug'] ===true) $email  = $config['debug_email'];
					
					$this->email->clear(TRUE);
					$this->email->to( $email );
					$this->email->from($organization.'_feedback@800.com.ua');
					$this->email->cc($organization.'_feedback@800.com.ua');
					if ( !empty($config['bcc'])) $this->email->bcc($config['bcc']);
					$this->email->subject($config['subject']);
					$this->email->message($config['body']);
					$this->email->attach($filename);
					
					if ( $flagToSend === true ) {
						if( $this->email->send() ){
							echo '<div>email sent to "'.$email.'"</div>';
							$count_sended++;
							$count_emails_sended += substr_count($email, ',')+1;
						} else {
							echo '<div style="color:red;">ERROR send to "'.$email.'"</div>';
							$log_msg .= 'ERROR_ send mail to "'.$email.'"'.PHP_EOL;
							// show_error( $this->email->print_debugger() );
						}
						sleep(1);
						if ( $count_sended == 30 || $count_sended == 60 || $count_sended == 90 ) sleep(3*60);
					}
					if ($config['debug'] === true) break;
				} // foreach letters
				
				if ($config['debug'] === true) break;
				
			} // organization_dealers
			
		} // foreach users
		$t3 = microtime(true);
    	// send mail		

    	// exit;
		if ($config['debug'] ===true) $count_emails_sended = count( explode( ',', $config['debug_email'] ) );
		if ($count_sended == 0 && $count_emails_sended == 0) {
			echo "<hr><h2>No Email's was sent</h2>";
		} else {
			echo '<hr><h2>'.$count_sended.' letters were sent to '.$count_dealer_sended.' dealers, to '.
			            $count_emails_sended.' email addresses totaly. Elapsed time, s: '.round($t3-$t2, 2).'</h2>';
		}
		
		$log_msg .= $count_sended.' letters were sent to '.$count_dealer_sended.' dealers, to '.
			            $count_emails_sended.' email addresses totaly. Elapsed time, s: '.round($t3-$t2, 2).PHP_EOL;
		$log_msg .= '------------------------------------------------------------------------'.PHP_EOL;
		file_put_contents($log_file, $log_msg, FILE_APPEND);

	} // send_email_excel_toDealers
	//***********************************************
	// END depricated
	
	/**
	  send_email_excel_toDealers
	  @version 2021-10-02
	*/
	public function send_email_excel_toDealers() {
		
		$today = date('d.m.Y');
	
		$config = $this->config->item('dealer_report');
		if (empty($config)) {
			echo '<div style="color:red">Not found "dealer_report" section in config file "config/sar_congig"<br>Controller abort.</div>';
			return;
		};
		$config = Utils::render_config($config, $today);

		$flagToSend = false;
		if ( date("H:i") == $config['cron_time'] ) $flagToSend = true;
		
		$max_emails_inLetter = 4;
		if ( isset($config['max_emails_inLetter']) && $config['max_emails_inLetter'] >=1 && $config['max_emails_inLetter'] <= 20 )
			$max_emails_inLetter = $config['max_emails_inLetter'];
			
		echo "<h1>send_email_excel_toDealers</h1>";
		echo '<hr>';
		
		$log_file = './sar_reports/send_email_excel_toDealers.log';
		$log_msg = '';
		$log_msg .= 'send_email_excel_toDealers started at  '.date('Y-m-d H:i:s').PHP_EOL;
		//$log_msg .= 'flagToSend='.(int)$flagToSend.PHP_EOL;
		
		// render config
		echo Utils::prettyPrint($config, 'config(rendered) from "config/sar_congig.php", "dealer_report" section:');
		echo '<hr>';
		
		//	build dealer reports
		// ***********************************************
		//$this->build_dealer_reports($log_msg);
		$t1 = microtime(true);
		
		// generate users email array
		// *****************************************
		echo '<h3>generate users email</h3>';
		
		$users = array();
		$count_email = 0;
		$count_skip_byAccessPosition = 0;
		$users_debug = array(); 
		$ACCESS_POSITION = ['100000000', '100000001', '100000004'];
		foreach(self::ORGANIZATIONS as $organization) {
			
			// load dealers
			CRM::$_organization = $organization;
			$dealers = $this->load_dealers();
			$dealers_index = array();
			foreach ($dealers as $dealer) {
				$dealers_index [ $dealer['AccountId'] ] = $dealer;
			}
			// echo Utils::prettyPrint($dealers, 'dealers');
			
			// load users
			$organization_users = CRM::sar_dealers_users( array( 'organization' => $organization) );
			// echo Utils::prettyPrint($organization_users, 'organization_users');
			
			$users[ $organization ] = array();
			$users_debug[ $organization ] = array();
			foreach ( $organization_users['Result']['Data'] as $user) {
					
				if ( empty($user['New_email']) ) {
					echo '<div style="color:magenta">User '.$user['New_name'].' '.$user['New_lastname'].' ('.$organization.') has not an email. Skipped.</div>';
					continue;
				}
				
				if ( $user['New_access_employees'] == true ) {
					echo '<div style="color:magenta">User '.$user['New_email'].' ('.$organization.'). has access_employees. Skipped.</div>';
					continue;
				}
				if (!in_array($user['New_position'], $ACCESS_POSITION )) {
					// echo '<div style="color:magenta">User '.$user['New_email'].' ('.$organization.'). has wrong access position: "'.$user['New_position'].'". Skipped.</div>';
					$count_skip_byAccessPosition++;
					continue;
				}
				
				$dealer = $dealers_index[ $user['New_dealer'] ];
				if ( !isset($dealer) ) {
					echo '<div style="color:red">For user '.$user['New_email'].' ('.$organization.') not found dealer by id "'.$user['New_dealer'].'"</div>';
					continue;
				}
				
				$title = substr($dealer['Code'], -3);	
				$filename1 = './sar_reports/daily_report_'.$organization.'.xlsx';
				$filename2 = './sar_reports/models_report_'.$organization.'.xlsx';;
				if ( !file_exists($filename1)  || !file_exists($filename2) ) {
					echo '<div style="color:red">For user '.$user['New_email'].
					    ' ('.$organization.') not found report file "'.$filename1.'" or "'.$filename2.'"</div>';
					continue;
				} // if
				
				if ( !isset( $users[ $organization ][ $title ] ) ) {
					$users[ $organization ][ $title ] = array('dealer_name' => $dealer['Name'],
				                                              'filename' =>[$filename1, $filename2],
															  'emails' => [] );
				}
				
				if ( in_array($user['New_email'], $users[ $organization ][ $title ][ 'emails' ] ) ) {
					echo '<div style="color:magenta">Dublicate user '.$user['New_email'].' ('.$organization.'). Skipped.</div>';
					continue;
				}
				
				$count_email++;
				$users_debug[ $organization ][$title][] = $user['New_email'].', position: '.$user['New_position'].', dealer name: '.$dealer['Name'].', filename: \''.$filename.'\'';
				$users[ $organization ][ $title ][ 'emails' ][] = $user['New_email'];
			} // foreach

		} // foreach organizations
		$t2 = microtime(true);
		
		echo '<h3 style="color:magenta">'.$count_skip_byAccessPosition.' contacts was skiped due to access position.</h3>';
		echo '<hr>';
		
		// create letters
		// used $max_emails_inLetter from config
		
		$count_letter = 0;
		$count_dealer = 0;
		foreach($users as $organization => $organization_dealers) {
			foreach ($users[$organization] as $dealer_key => $organization_dealer) {
				sort($users[$organization][$dealer_key]['emails']);
				$users[$organization][$dealer_key]['count_emails'] = count($users[$organization][$dealer_key]['emails']);
				$count_dealer++;
				$letters = [];
				$count=0;
				$email_list = '';
				foreach( $users[$organization][$dealer_key]['emails'] as $email ) {
					$count++;
					if ( $count >=  $max_emails_inLetter ) {
						$letters[] = $email_list.', '.$email;
						$count_letter++;
						$email_list = '';
						$count = 0;
					} else {
						if ($count>1) $email_list .= ', ';
						$email_list .= $email;
					}
				} // foreach emails
				if ($email_list != '') {
					$count_letter++;
					$letters[] = $email_list;
				}
				$users[$organization][$dealer_key]['letters']=$letters;
			} // foreachorganization_dealer
		} // users
				
		//echo Utils::prettyPrint($users_debug, 'final email list ('.$count_email.')');
		echo Utils::prettyPrint($users, 'final email list (email addresses: '.$count_email.', dealers: '.$count_dealer.', letters: '.$count_letter.')');
		$log_msg .= 'Email list has '.$count_email.' recepients. Dealers: '.$count_dealer.', Letters: '.$count_letter.
				      '. Elapsed time, s: '.round($t2-$t1, 2).PHP_EOL;
		
    	// send email
    	// ******************************

    	$this->load->library('email');
				
	    if ($config['debug'] ===true) $flagToSend = true;
		$count_sended=0;
		$count_dealer_sended=0;
		$count_emails_sended=0;
		
		foreach($users as $organization => $organization_dealers) {
			foreach ($organization_dealers as $organization_dealer) {
				$filenames = $organization_dealer['filename'];
				
				//@cheat
				//if ( !in_array( $email, $cheat_list ) ) continue;
								
				// if ( count($organization_dealer['letters'])==1 ) continue;
				
				$count_dealer_sended++;
				foreach ($organization_dealer['letters'] as $key => $email) {
										
					if ($config['debug'] ===true) $email  = $config['debug_email'];
					
					$this->email->clear(TRUE);
					$this->email->to( $email );
					$this->email->from($organization.'_feedback@800.com.ua');
					$this->email->cc($organization.'_feedback@800.com.ua');
					if ( !empty($config['bcc'])) $this->email->bcc($config['bcc']);
					$this->email->subject($config['subject']);
					$this->email->message($config['body']);
					
					foreach ($filenames as $filename) {
						$this->email->attach($filename);
					} // foreach
					
					if ( $flagToSend === true ) {
						if( $this->email->send() ){
							echo '<div>email sent to "'.$email.'"</div>';
							$count_sended++;
							$count_emails_sended += substr_count($email, ',')+1;
						} else {
							echo '<div style="color:red;">ERROR send to "'.$email.'"</div>';
							$log_msg .= 'ERROR_ send mail to "'.$email.'"'.PHP_EOL;
							// show_error( $this->email->print_debugger() );
						}
						sleep(1);
						if ( $count_sended == 30 || $count_sended == 60 || $count_sended == 90 ) sleep(3*60);
					}
					if ($config['debug'] === true) break;
				} // foreach letters
				
				if ($config['debug'] === true) break;
				
			} // organization_dealers
			
		} // foreach users
		$t3 = microtime(true);
    	// send mail		

    	// exit;
		if ($config['debug'] ===true) $count_emails_sended = count( explode( ',', $config['debug_email'] ) );
		if ($count_sended == 0 && $count_emails_sended == 0) {
			echo "<hr><h2>No Email's was sent</h2>";
		} else {
			echo '<hr><h2>'.$count_sended.' letters were sent to '.$count_dealer_sended.' dealers, to '.
			            $count_emails_sended.' email addresses totaly. Elapsed time, s: '.round($t3-$t2, 2).'</h2>';
		}
		
		$log_msg .= $count_sended.' letters were sent to '.$count_dealer_sended.' dealers, to '.
			            $count_emails_sended.' email addresses totaly. Elapsed time, s: '.round($t3-$t2, 2).PHP_EOL;
		$log_msg .= '------------------------------------------------------------------------'.PHP_EOL;
		file_put_contents($log_file, $log_msg, FILE_APPEND);

	} // send_email_excel_toDealers
	
	/********************************************************
	  send email FOR PCU
	*********************************************************/
    public function send_email_excel() {
		
		$t0 = microtime(true);
		
		$log_file = './sar_reports/send_email_excel.log';
		$log_msg = '';
		$log_msg .= 'send_email_excel started at  '.date('Y-m-d H:i:s').PHP_EOL;
	
		$flagToSend = false;
		if( date("H:i") == '11:00' ) $flagToSend = true;

    	//$froms = ['citroen_feedback@800.com.ua', 'peugeot_feedback@800.com.ua', 'opel_feedback@800.com.ua', 'ds_feedback@800.com.ua'];

    	// load and calculate data
    	// ****************************
		$options = array( 'period_start' => date('Y-m-d'), 'saveToFile' => true, 'access_employees' => true);
		
		// options for data loader
		$period_start_arr = explode('-', $options['period_start']);
		$load_data_options = array( 'month' => $period_start_arr[1], 'year' => $period_start_arr[0], 'day' =>  $period_start_arr[2] );

		$filenames = array();
    	foreach(self::ORGANIZATIONS as $organization) {
			
    		CRM::$_organization = $organization;
			$this->load_cars_data();
			// $dealers = $this->load_dealers();
    		$this->load_data($load_data_options);
    		
			// models report
			$options['filename'] = './sar_reports/models_report_'.$organization.'.xlsx';
			$filenames[] = $options['filename'];
			$this->excel_models_report( $options );

			// daily report
    		$options['sar_excel'] = $this->data;
    		$options['sar_excel']['SAR_period_start'] = $options['period_start'];
    		$options['filename'] = './sar_reports/daily_report_'.$organization.'.xlsx';
    		$filenames[] = $options['filename'];
			
			$this->excel( $options );
			
    	};
		$t1 = microtime(true);
		
		$log_msg .= 'Dealer and Models report were builded. Elapsed time, s: '.round($t1-$t0, 2).PHP_EOL;
		
		// generate users email array
		// *****************************************

		$users = array(); $users_debug = array(); $users_debug2 = array();
		$ACCESS_POSITION = ['100000000', '100000001', '100000004'];
		foreach(self::ORGANIZATIONS as $organization) {
			
			//$db = $this->load->database($organization.'_db', true);
			//$query = $db->query('SELECT * FROM `users`');

			$organization_users = CRM::sar_pcu_users( array( 'organization' => $organization) );

			foreach ( $organization_users['Result']['Data'] as $user) {
				/*
				if ( $user['access_employees'] ==='1' && !empty($user['email']) && $user['StateCode'] != '1' ) {
					$users_debug[] = $organization.': '.$user['email'];
					$users[] = $user['email'];
					//echo Utils::prettyPrint( $user, 'user' );
				}
				$user['organization'] = $organization;

				if ($user['email'] == 'evgenya.derevianko@mpsa.com' ||
					 $user['email'] == 'alla.khoroshykh@mpsa.com' ) $users_debug2[] = $user;
				*/
				$users_debug[] = $organization.': '. ( empty($user['New_email']) ? 'NO EMAIL for user: '.$user['New_mobilephone'] : $user['New_email'] )
				                              .', position: '.($user['New_position'] == null ? 'null' : $user['New_position']);
				if (!empty($user['New_email']) && $user['New_position'] != null
				     && in_array($user['New_position'], $ACCESS_POSITION ) ) $users[] = $user['New_email'];
			} // foreach

		} // foreach
		$t2 = microtime(true);
		$users = array_unique( $users );

		//echo Utils::prettyPrint($users_debug2, 'users info for '.count($users_debug2).' accounts');
		echo Utils::prettyPrint($users_debug, 'all users from all organizations ('.count($users_debug).') throw GET /api/{organization}/webuser/active');
		echo Utils::prettyPrint($users, 'final email list ('.count($users).')');
		
		$log_msg .= 'Email list has '.count($users).' (+1 for bebug) recepients. Elapsed time, s: '.round($t2-$t1, 2).PHP_EOL;
		
		$users_send = $users;
		$users_send[] = 'igirsuslov@gmail.com';

    	// send email
    	// ******************************

    	$this->load->library('email');

    	// $users_send = ['adam@esprit.name'];
		// $users_send = ['igirsuslov@gmail.com'];
		// $flagToSend = true;
    	// $users_send = ['oi@800.com.ua','igirsuslov@gmail.com'];
		
		$count_sended = 0;
    	foreach ($users_send as $user_send) {
		        $this->email->clear(TRUE);

		        $this->email->from('peugeot_feedback@800.com.ua');
		        $this->email->to( $user_send );
		        $this->email->cc('peugeot_feedback@800.com.ua');
		        $this->email->subject('Daily Report on '.$options['period_start']);
		        $this->email->message('<p>Добрый день!</p><p>Во вложении представлен отчёт Daily Report и Models report состоянием на текущий момент.</p><p>С уважением,<br>Служба поддержки<br>PCU Portal</p>');

		        foreach ($filenames as $key => $filename) {
		    		$this->email->attach($filename);
		    	} // foreach

				if ($flagToSend === true) {
			    	if( $this->email->send() ){
			    		echo '<h5>email to '.$user_send.' sent</h5>';
						$count_sended++;
			    	} else {
			    		echo '<h5>error send to '.$user_send.'</h5>';
						$log_msg .= ' "'.$user_send.'"'.PHP_EOL;
			    	}
			        sleep(1);
				}

		} // foreach
		$t3 = microtime(true);
    	// send mail		

    	// exit;
		if ($flagToSend == false) echo "<h4>No Email's was sent</h4>";
		
		$log_msg .= $count_sended.' emails were sent. Elapsed time, s: '.round($t3-$t2, 2).PHP_EOL;
		$log_msg .= '------------------------------------------------------------------------'.PHP_EOL;
		file_put_contents($log_file, $log_msg, FILE_APPEND);

    } // send_email_excel
	
	public function send_email_test() {
		$this->load->library('email');
		
		echo '<h1>send_email_test</h1>';
		
		$count_sended = 0;
		// v.golodna@peugeot-autopassage.com
		$users_send = [ 'xxxx@test-xxx78924.com',
						'andreyshtokalo@gmail.com',
						'adam@esprit.name',
						'admin@esprit.name',
						'igirsuslov@gmail.com'];
		$filename = './sar_reports/dealer_reports/citroen/54G_dealer_report.xlsx';
		foreach ($users_send as $user_send) {
		    $this->email->clear(TRUE);

		    $this->email->from('peugeot_feedback@800.com.ua');
		    $this->email->to( $user_send );
		    $this->email->cc('peugeot_feedback@800.com.ua');
		    $this->email->subject('Daily Report test');
		    $this->email->message('<p>Добрый день!</p><p>Во вложении представлен отчёт Daily Report и Models report состоянием на текущий момент.</p><p>С уважением,<br>Служба поддержки<br>PCU Portal</p>');
			
		    $this->email->attach($filename);
		 

			if( $this->email->send() ){
				echo '<h5>email to '.$user_send.' sent</h5>';
				$count_sended++;
			} else {
				echo '<h5>error send to "'.$user_send.'"</h5>';
				$log_msg .= ' "'.$user_send.'"'.PHP_EOL;
			}
			sleep(1);
		
		} // foreach
		echo '<h3>sent '.$count_sended.' from '.count($users_send).' successfully</h3>'; 
	} // send_email_test
	
	public function timeTest() {
		$sleepTime = 60*15;
		sleep( $sleepTime );
		echo '<h1>sleep(' . $sleepTime . ') was successfuly executed!</h1>';
	} // timeTest

} // Sar controller

function usort_11($a, $b) {
	return (int)$a[0]['New_td_id'] - (int)$b[0]['New_td_id'];
} //usort_11

function usort_22($a, $b) {
	if ($a[0]['value'] == $b[0]['value']) return 0;
	if ($a[0]['value'] > $b[0]['value']) {
		return 1;
	} else {
		return 0;
	}
} //usort_22

function usort_mr($a, $b) {
	if ($a['value'] == $b['value']) return 0;
	if ($a['value'] > $b['value']) {
		return 1;
	} else {
		return 0;
	}
} //usort_mr

function usort_dealers($a, $b) {
	if ($a['Code'] == $b['Code']) return 0;
	if ($a['Code'] > $b['Code']) {
		return 1;
	} else {
		return 0;
	}
} //usort_dealers

function getCoordinate(int $number) {

    $COLUMNS = 6;
    $ROWS = 16;
    $DIAGS_IN_ROW = 4;
       
	$row = intdiv($number,  $DIAGS_IN_ROW)+1;
	$mod = $number %  $DIAGS_IN_ROW;
	if ($mod==0) {
		$mod = $DIAGS_IN_ROW;
		$row--;
	}
	$topLeft = chr(65+($mod-1)*$COLUMNS).(($row-1)*$ROWS+1);
	$bottomRight = chr(65+($mod)*$COLUMNS).(($row)*$ROWS+1);
		
	return array('topLeft' => $topLeft, 'bottomRight' => $bottomRight);

} // getCoordinate
