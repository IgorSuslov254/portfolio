<?php
defined('BASEPATH') OR exit('No direct script access allowed');
	
	use PhpOffice\PhpSpreadsheet\IOFactory;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
	use myPhpOffice\PhpSpreadsheet\Style\Color;

class Stellantis_contacts extends Admin_Controller {

	const ORGANIZATIONS = ['citroen', 'peugeot', 'opel', 'ds'];

	public function __construct() {

		parent::__construct();
		$this->load->library('Utils');
		$this->load->library('MyCrm');
		$this->load->model('Stellantis_contacts_model');

		$this->config->set_item('language', 'ukrainian');

		$this->lang->load('stellantis_contacts/stellantis_contacts_lang', 'ukrainian');
		$this->data['lang'] = $this->lang;
		$this->data['access_employees'] = isset($_SESSION['access_employees']) && $_SESSION['access_employees'] == true ? 1 : 2;
		// $this->data['access_employees2'] = isset($_SESSION['access_employees']) && $_SESSION['access_employees'] == true ? 1 : 0;
		// echo "<script>console.log(`access_employees= `,`". $this->data['access_employees2']  ."`);</script>";

	}

	public function index() {

		// $this->load_tree();
		// echo Utils::prettyPrint($this->data['tree'], 'Tree '.count($this->data['tree']), array('htmlspecialchars' => true));

		$this->data['tree'] = $this->get_html_tree();
		$this->template->admin_render('stellantis_contacts/stellantis_contacts_view', $this->data);

	} // index


	private function error( $options = array() ) {
		
		// $this->data['error'] = true;
		// if ( isset($options['error_code']) ) $this->data['error_code'] = $options['error_code'];
		// if ( isset($options['error_description']) ) $this->data['error_description'] = $options['error_description'];

		
		$this->data['type_info'] = 'error';
		$this->data['info'] = $options['error_description'];
		
		if ($options['load_tree'] === true) $this->data['tree'] = $this->get_html_tree();

		$this->template->admin_render('stellantis_contacts/stellantis_contacts_view', $this->data);
	} // error

	private function crm_get_tree() {
		$data = MyCrm::allow_employees_users();
		if ($data['Status'] != 'OK') {
			$this->error(array('error_description' => $this->lang->line('sc_ERR_crm_get_tree') ));  
			return null;
		}
		return $data['Result']['Data'];
	} // crm_get_tree

	private function get_array_tree() {
		
		$data = $this->crm_get_tree();
		if (is_null($data)) return;
		
		// sort
		usort($data, "usort_22");

		function get_children(&$data, $id, &$count) {
			
			$children = [];
			foreach ($data as $data_item) {
				if ($data_item['New_parent_id'] === $id) {
					$data_item['children'] = get_children($data, $data_item['New_web_userId'], $count);
					$children[] = $data_item;
					$count++;
				}
			} // foreach
			
			return $children;

		} //  get_children

		$count = 0;
		$tree = get_children($data, null, $count);
		// echo 'Count of allow_employees_users = '.count($data).', used in tree = '.$count.'<br>';


		return $tree;
	} // load_tree

	public function get_html_tree() {
		
		$data = $this->crm_get_tree();
		if (is_null($data)) return;

		// sort
		usort($data, "usort_22");

		// load photos

		foreach ($data as &$data_item) {
			$img = $this->Stellantis_contacts_model->get_by_ext( $data_item['New_ext'] );
			if ( !empty($img) )	{
				//echo $img[0]['file_name'].'<br>';
				if ( file_exists( $this->Stellantis_contacts_model->IMG_PATH.$img[0]['file_name'] ) ) {
					$data_item['file_name'] = $img[0]['file_name'];
				}
			} // if

			if (!is_null( $data_item['New_birthday'] ) ) {
				//$data_item['New_birthday'] = date('Y-m-d', $data_item['New_birthday'] );
				$data_item['New_birthday'] = date('Y-m-d', $data_item['New_birthday'] );
				$data_item['New_birthday2'] = date('m-d', strtotime($data_item['New_birthday']) );
			}

		} // foreach

		function get_children(&$data, $id, $base_path) {
			
			$li_html = '';
			foreach ($data as $data_item) {
				if ($data_item['New_parent_id'] === $id) {
					// if( $data_item['New_name'].' '.$data_item['New_lastname'] == 'Оксана Иванец' || $data_item['New_name'].' '.$data_item['New_lastname'] == 'Марина Лузан' || $data_item['New_name'].' '.$data_item['New_lastname'] == 'Андрей ' || $data_item['New_name'].' '.$data_item['New_lastname'] == 'Колл-центра Оператор') continue;

					if ( $data_item['New_service_access'] == true ) continue;
					
					if (!empty($data_item['file_name'])) {
						$img_src = base_url( $base_path.$data_item['file_name'] );
					} else {
						$img_src = base_url( $base_path."car_default.jpg" );
					}
					
					//<div class="customer_tree" data-item=\''. json_encode( $data_item ) .'\'>
					
					$data_item_str = json_encode( $data_item );
					//$data_item_str = preg_replace("/\'/gm", 'P', $data_item_str);
					$data_item_str = str_replace("'", '&#8242;', $data_item_str);
					
					$birthday = '';
          			if ( date('m-d') == $data_item['New_birthday2'] ) $birthday = '<i class="fa fa-birthday-cake birthday" aria-hidden="true"></i>';

		            $li_html .= '
		            <li>
		              <div class="customer_tree" data-item=\''. $data_item_str .'\'>
		                '.$birthday.'
		                <div> <img src="'.$img_src.'" alt=""> </div>
		                <div>
		                  <p>'.$data_item['New_name'].' '.$data_item['New_lastname'].'</p>
		                  <p>'.$data_item['New_appointment'].'</p>
		                </div>
		              </div>';

					$children_html = get_children($data, $data_item['New_web_userId'], $base_path);
					if (!empty($children_html)) $li_html .= '<ul>'.$children_html.'</ul>';

					$li_html .= '</li>';
				}
			} // foreach
			

			return $li_html;

		} //  get_children

		$tree_html = '<ul>'.get_children($data, null, $this->Stellantis_contacts_model->IMG_PATH).'</ul>';
		// $tree_html .= "<script>console.log(`$data= `,`". json_encode($data)."`);</script>";

		
		// echo $tree;
		//echo Utils::prettyPrint($tree, 'Tree '.count($tree), array('htmlspecialchars' => true));

		return $tree_html;

	} // get_html_tree

	private function upload_img()
	{

	    $config['upload_path'] = $this->Stellantis_contacts_model->IMG_PATH;
	    $config['allowed_types'] = 'gif|jpg|png';
	    $config['max_size'] = '10000000';
	    $config['max_width']  = '2080';
	    $config['max_height']  = '4608';

	    $this->load->library('upload', $config);

	    // Automatically finds your user's file in $_FILES
	    if ( ! $this->upload->do_upload( 'file' ) )
	    {
	    	$this->error( array('error_description'=> $this->upload->display_errors(), 'load_tree' => true) );
	    	//echo $this->upload->display_errors();
	    	return null;
	    }
	    else
	    {
	        $data = array('upload_data' => $this->upload->data());

			$config2['image_library'] = 'gd2';
			$config2['source_image'] = $this->Stellantis_contacts_model->IMG_PATH.$data['upload_data']['file_name'];
			$config2['create_thumb'] = false;
			$config2['maintain_ratio'] = false;
			$config2['width']         = 175;
			$config2['height']       = 175;

			$this->load->library('image_lib', $config2);
			$this->image_lib->resize();


	        return $data;
	    }

	} // upload_img


	public function update_employee() {
		
		// echo Utils::prettyPrint( $_POST, 'POST' );
		// echo Utils::prettyPrint( $_FILES, 'FILE' );
		// echo '<hr>';

		if ( empty($_POST['ext']) ) {
			// Error update. Not defined "EXT" data.
			$this->error( array('error_description' => $this->lang->line('sc_ERR_update_employee_ext'), 'load_tree' => true ));
			return false;
		}

		// img upload and save
		// *****************************
		if ( isset($_FILES['file']) && !empty($_FILES['file']['name']) ) {
			$img = $this->upload_img();
			if ( is_null($img) ) return false;
			if ( empty( $img['upload_data']['file_name']) ) {
				$this->error( array('error_description'=> $this->lang->line('sc_ERR_update_employee_img'), 'load_tree' => true ));
				return false;				
			}

			// DO NOT DO IT 
			// ALL DATA WILL BE LOST
			
			//$ret_init = $this->Stellantis_contacts_model->init_data_table();
			// echo 'init '.$ret_init.'<hr>';

			// DO NOT DO IT

			// echo Utils::prettyPrint( $img, '$img' );

			$ret_upd = $this->Stellantis_contacts_model->update(array(
				'ext' => $_POST['ext'],
				'file_name' => $img['upload_data']['file_name']
			));

			if ( $ret_upd != 1 ) {
				
				// Error saving img info to the database.
				//
				$this->error( array('error_description' => $this->lang->line('sc_ERR_update_employee_dbimg'), 'load_tree' => true ));
				return false;
			}

		} // if 

		// crm data save
		// *****************************
		$birthday = null;
		if (!empty($_POST['birthday'])) $birthday = strtotime($_POST['birthday']);


		if ( isset($_POST['id'] ) ) {
			$post_data = array(
				"EntityName" =>  "New_web_user",
  				"Id" =>  $_POST['id'],
  				"Picklists" => array( 
  					array( 
         				"AttributeName" => "New_dept",
         				"Value" => intval($_POST['dept'])
         			)
    			),
    			"DateTimes" => array( 
   					array( 
      					"AttributeName" => "New_birthday",
      					"Value" => $birthday
 					)
    			),
  				"OtherAttributes"=> array(
    				array(
      					"AttributeName"=> "New_name",
      					"Value"=> $_POST['name']
    				),
    				array(
      					"AttributeName"=> "New_lastname",
      					"Value"=> $_POST['last_name']
    				),
    				array(
      					"AttributeName"=> "New_appointment",
      					"Value"=> $_POST['appointment']
    				),
    				array(
      					"AttributeName"=> "New_work_phone",
      					"Value"=> $_POST['work_phone']
    				),
    				array(
      					"AttributeName"=> "New_close_cellphone",
      					"Value"=> $_POST['close_cellphone'] == 'on' ? true : false
    				),
    				array(
      					"AttributeName"=> "New_mobilephone",
      					"Value"=> $_POST['mobilephone']
    				),
    				array(
      					"AttributeName"=> "New_email",
      					"Value"=> $_POST['email']
    				),
    				array(
      					"AttributeName"=> "New_contact_email",
      					"Value"=> $_POST['contact_email']
    				),    				
    				array(
      					"AttributeName"=> "New_text",
      					"Value"=> $_POST['text']
    				)    				
    			)			
  			);

			// echo Utils::prettyPrint( $post_data, 'post_data' );

			$current_organization = CRM::$_organization;
  			$upd_ret = myCRM::employee_update( array( 'post_data' => $post_data) );
  			// echo Utils::prettyPrint( $upd_ret, '___update_crm '.$current_organization );

  			foreach (self::ORGANIZATIONS as $organization) {
  				
  				if ( $organization == $current_organization) continue;

  				$employee = myCRM::employee_by_ext( array('ext'          => $_POST['ext'], 
  														  'organization' => $organization ) );
  				if ( isset( $employee['Result'] ) && 
  					 isset( $employee['Result']['Data']) &&
  					 isset( $employee['Result']['Data']['New_web_userId'] ) ) {

					// update employee in the other database  					
					// **************************************
					$post_data['Id'] = $employee['Result']['Data']['New_web_userId'];
					$upd_ret = myCRM::employee_update( array( 'post_data'    => $post_data,
															  'organization' => $organization) );
					// echo Utils::prettyPrint( $upd_ret, 'update_crm '.$organization.' id='.$post_data['Id'] );

	  			} // if
  			} // foreach
			
			//'Данные обновлены'
  			$this->data['info'] = $this->lang->line('sc_update_employee_success');
			$this->data['type_info'] = 'success';

  			

		} // if
		
		

		// $get_all = $this->Stellantis_contacts_model->get_all();
		// echo 'update '.$ret_upd;
		// echo '<hr>';

		// echo Utils::prettyPrint( $get_all, 'get_all' );
		
		// echo '<hr>';
		// echo 'FINISH';

		$this->index();
		return true;
			
	} // update_employee

	// excel report  
	// ***************************************************************
	public function excel_report( $options = array() ) {
		
		// Create new Spreadsheet object
		$spreadsheet = new Spreadsheet();

		// Set document properties
		$spreadsheet->getProperties()->setCreator('stellantis contacts EXCEL REPORT')
		    ->setLastModifiedBy('stellantis contacts EXCEL REPORT')
		    ->setTitle('Office 2007 XLSX Test Document')
		    ->setSubject('Office 2007 XLSX Test Document')
		    ->setDescription('stellantis contacts REPORT, generated using stellantis contacts EXCEL REPORT.')
		    ->setKeywords('office 2007 openxml php')
		    ->setCategory('stellantis contacts Report');


		$sheet = $spreadsheet->setActiveSheetIndex(0);
		$sheet->setTitle('PCU_Contacts');
		
		// data
		// *************************************************************
		$organization = CRM::$_organization;
		$tree = $this->get_array_tree();
		// echo Utils::prettyPrint($tree, '$tree');

		// header
		// *************************************************************
		$row=0;
		$sheet->setCellValue('A'.++$row, mb_strtoupper($organization).' UKRAINE');
		$row++;
		$row++;
		$title_row = $row;
		$row++;
		$sheet->getStyleByColumnAndRow(1, 1, $col, 1)->getFont()->setBold(true);

		// body
		// **************************************************
		function get_max_col($arr, $col) {
			
			$max_col = $col;
			foreach ($arr as $employee) {
				if (count($employee['children'])>0) $max_col = max($max_col, get_max_col($employee['children'], $col+1) );
			}
			return $max_col;

		} 

		$max_col = get_max_col($tree, 1);
		//echo '$max_col='.$max_col.'<br>';

		function print_employee($sheet, $arr, $col, $row, $max_col) {
			
			// if ($col > $max_col && count($arr)>0) $max_col = $col;
			//echo $max_col.'<br>';

			foreach ($arr as $employee) {
				
				if ( $employee['New_service_access'] == true ) continue;

			    // calculate $mail and 
				if (isset($_SESSION['access_employees']) && $_SESSION['access_employees'] == true) {
					
					$mobilephone = $employee['New_mobilephone'];
					if ( !empty($employee['New_contact_email']) &&  !empty($employee['New_email']) ) {
						$mail = $employee['New_contact_email'].', '.PHP_EOL.$employee['New_email'];
					} else {
						$mail = empty($employee['New_contact_email']) ? $employee['New_email'] : $employee['New_contact_email'];
					}

				} else {
					$mobilephone = $employee['New_close_cellphone'] === true  ? '' : $employee['New_mobilephone'];
					$mail = empty($employee['New_contact_email']) ? $employee['New_email']: $employee['New_contact_email'];
				}


				$name = $employee['New_name'].' '.$employee['New_lastname'];
				$sheet->setCellValueByColumnAndRow($col, $row, $name);
				$sheet->setCellValueByColumnAndRow($max_col+1, $row, $employee['New_ext']);
				$sheet->setCellValueByColumnAndRow($max_col+2, $row, " ".$employee['New_work_phone']);
				$sheet->setCellValueByColumnAndRow($max_col+3, $row, " ".$mobilephone);
				$sheet->setCellValueByColumnAndRow($max_col+4, $row, $mail);
				$sheet->setCellValueByColumnAndRow($max_col+5, $row, $employee['New_appointment']);
				$sheet->setCellValueByColumnAndRow($max_col+6, $row, $employee['New_birthday'] ? date('d-m-Y', $employee['New_birthday']) : "" );
				$sheet->setCellValueByColumnAndRow($max_col+7, $row, $employee['New_text']);
				$row++;
				$row = print_employee($sheet, $employee['children'], $col+1, $row, $max_col );
			} // foreach

			return $row;

		} // print_employee
		
		// $max_col = 1;
		$last_row = print_employee($sheet, $tree, 1, $row, $max_col);

		// column width
		// echo $max_col.'<br>';
		$width = 4;
		for($i=1; $i<$max_col; $i++){
			$sheet->getColumnDimension( chr(65+$i-1) )->setWidth($width);
		}

		// title
		//**********************************
		
		$sheet->setCellValueByColumnAndRow(1, $title_row, 'NAME');
		$sheet->mergeCellsByColumnAndRow(1, $title_row, $max_col, $title_row);
		
		$titles = ['EXT', 'PSTN Tel', 'MOBILE', 'E-MAIL', 'JOB TITLE', 'BIRTHDAY', "JOB RESPONSIBILIIES"];
		$total_col = $max_col + count($titles);

		for($i=$max_col; $i<=$total_col; $i++){
			$sheet->getColumnDimension( chr(65+$i-1))->setAutoSize(true);
		}

		$col = $max_col+1;
		foreach ($titles as $title) {
			$sheet->setCellValueByColumnAndRow($col, $title_row, $title);
			$col++;
		} // foreach

		$styleArray = [
		    'font' => [
		        'bold' => true,
		    ],
		    'fill' => [
		        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
		        'startColor' => [
		            'rgb' => 'd9d9d9',
		        ],
		    ],
		];	
 		$sheet->getStyleByColumnAndRow(1, $title_row, $total_col ,$title_row)->applyFromArray($styleArray);
		
		$sheet->freezePane('A4');
		// no sammary below
		$sheet->setShowSummaryBelow(false);

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		//$spreadsheet->setActiveSheetIndex(0);

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->setIncludeCharts(true);		

		if ( !empty($options['filename']) ) {

			$writer->save( $options['filename'] );

		} else {

			// Redirect output to a client’s web browser (Xlsx)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="pcu_contacts.xlsx"');
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
		

	} // excel_report

	
	// DEV function
	// ****************************
	public function test22() {
		$img = $this->Stellantis_contacts_model->get_by_ext( '649760' );
		echo Utils::prettyPrint( $img, '$img' );
		echo Utils::prettyPrint( $_SESSION, '$_SESSION' );
		return true;
	}

} // END OF CLASS Stellantis_contacts

function usort_22($a, $b) {
	$key_a = empty( $a['New_order'] ) ? $a['New_lastname'] : (int)$a['New_order'];
	$key_b = empty( $b['New_order'] ) ? $b['New_lastname'] : (int)$b['New_order'];


	if ( $key_a == $key_b ) return 0;
	if ( $key_a > $key_b ) {
		return 1;
	} else {
		return 0;
	}
} //usort_22

