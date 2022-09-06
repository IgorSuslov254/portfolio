<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__.'/myPhpOffice/PhpSpreadsheet/Spreadsheet.php';
require_once __DIR__.'/myPhpOffice/PhpSpreadsheet/IOFactory.php';
require_once __DIR__.'/myPhpOffice/PhpSpreadsheet/Worksheet/Worksheet.php';
require_once __DIR__.'/myPhpOffice/PhpSpreadsheet/Style/Conditional.php';
require_once __DIR__.'/myPhpOffice/PhpSpreadsheet/Style/NumberFormat.php';
require_once __DIR__.'/myPhpOffice/PhpSpreadsheet/Style/Color.php';


// use myPhpOffice\PhpSpreadsheet\Spreadsheet;
// use myPhpOffice\PhpSpreadsheet\IOFactory;
// use myPhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use myPhpOffice\PhpSpreadsheet\Style\Conditional;
// use myPhpOffice\PhpSpreadsheet\Style\NumberFormat;
// use myPhpOffice\PhpSpreadsheet\Style\Color;

class Mcd extends Admin_Controller{

	public function __construct() {

	    parent::__construct();
		$this->load->library('Utils');

	}


	public function index() {

		$filename_src = __DIR__ . '/../../template/source_mcd.xlsx';
		$filename_tmpl = __DIR__ . '/../../template/tmpl_mcd.xlsx';
		$filename_out = __DIR__ . '/../../template/out_mcd.xlsx';

		if ( !file_exists($filename_src)) {
			echo 'Error: not file exist:  '.$filename_src;
			return;
		}
		if ( !file_exists($filename_tmpl)) {
			echo 'Error: not file exist:  '.$filename_tmpl;
			return;
		}
		
		// if ( copy($filename_tmpl, $filename_out) !== true) {
		// 	echo 'Error: copy fail:  '.$filename_out;
		// 	retrun;
		// }

		//echo $filename_src.'<br>';
		//echo $filename_tmpl.'<br>';
		//echo $filename_out.'<br>';
		//echo $filename_tmpl.' <b>ncopied to</b> '.$filename_out.'<br>';

		$spreadsheet_src = IOFactory::load($filename_src);
		$spreadsheet_tmpl = IOFactory::load($filename_tmpl);
		
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
		//print_r($data);
		//echo 'CRM::$_organization='.CRM::$_organization.'<br>';
		

		// working with out

		//$sheet = $spreadsheet_out->getSheetByName('Список звернень');
		//$sheet->fromArray( $data );

		//$writer = IOFactory::createWriter($spreadsheet_tmpl, 'Xlsx');
		//$writer->save($filename_out);

		$this->excel_report( array('data' => $data) );

		//echo "Test MCD";
	} // index


	private function excel_report( $options = array() ) {

 		$data = $options['data'];
 		if ( empty($data) ) {
 			echo "Error MCD Excel Report. Data is empty<br>";
 			return false;
 		}

        //****************************************************************
 		// data prepare

 		$TOTAL_NAME = 'ЗАГАЛОМ';
 		$V_GROUP = ['Операційний менеджер', 'Консультант', 'Ресторан'];
 		$H_GROUP = 'Розділ звернення';
 		$T_DATA = '';
 		$start_col_h_fields = count($V_GROUP)+1;

 		$prepare_data = array();

 		foreach ($data[0] as $col_ind => $col_data) {
 			$fields[ $col_data ] = $col_ind;
 		}

 	// 	echo Utils::prettyPrint($fields, '$fields');
		// echo '<hr>';

		// filling group data
		// **********************************************************
		$group_data = array();
		$h_fileds = array();
 		foreach ($data as $row_ind => $row_data) {
 			if ( $row_ind == 0 ) continue;
 			$h_value = $row_data[ $fields[$H_GROUP] ];
 			$h_fileds [ $h_value ] = 1;
 			// echo $h_value.'<br>';
 			$item = &$group_data;
 			foreach($V_GROUP as $v_item) {
 				$v_value = $row_data[ $fields[$v_item] ];
 				if ( !isset($item[$v_value]) ) $item[$v_value] = array();
 				$item = &$item[$v_value];
 			} // V_GROUP
 			if ( !isset( $item[ $h_value ] ) ) $item[ $h_value ] = 0;
 			$item[ $h_value ]++;
 		} // foreach

 		// sorting group data
 		// **********************************************************
 		ksort($group_data);
 		
 		ksort($h_fileds);
 		$h_fileds[$TOTAL_NAME] = 1;
 		$col = $start_col_h_fields;
 		// create index of column for h_fields
 		foreach ($h_fileds as $key => $value) {
 			$h_fileds[ $key ] = $col;
 			$col++;
 		} // foreach

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

 		// calculate totals
 		// *********************************************************
 		$total_0 = array();

 		function calc_total($TOTAL_NAME, &$arr) {
 			$total = 0;
 			foreach ($arr as $value) {
 				$total += $value;
 			}
 			$arr[ $TOTAL_NAME ] = $total;
 		}

 		foreach($group_data as &$lvl_1) {
 			
 			$total_1 = array();
 			foreach ($lvl_1 as &$lvl_2) {
 				
 				$total_2 = array();
 				foreach ($lvl_2 as $key_3 => $lvl_3) {

 					foreach ($lvl_3 as $key_4 => $lvl_4) {
 						if ( !isset($total_0[ $key_4 ]) ) $total_0[ $key_4 ] = 0;
 						if ( !isset($total_1[ $key_4 ]) ) $total_1[ $key_4 ] = 0;
 						if ( !isset($total_2[ $key_4 ]) ) $total_2[ $key_4 ] = 0;
 						$total_0[ $key_4 ] += $lvl_4;
 						$total_1[ $key_4 ] += $lvl_4;
 						$total_2[ $key_4 ] += $lvl_4;
 					} // // foreach lvl 4
 					
 				} // foreach lvl 3
 				calc_total($TOTAL_NAME, $total_2);
 				$lvl_2[ 'total' ] = $total_2;

 			} // foreach lvl 2
 			calc_total($TOTAL_NAME, $total_1);
 			$lvl_1[ 'total' ] = $total_1;

 		} // foreach lvl 1
 		calc_total($TOTAL_NAME, $total_0);
		$group_data[ 'total' ] = $total_0;



 	// 	echo Utils::prettyPrint($h_fileds, '$h_fileds');
		// echo '<hr>';

 	// 	echo Utils::prettyPrint($group_data, '$group_data');
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
		
		$data_sheet->fromArray($data);
		$source_total_col = isset( $data[0] ) ? count($data[0]) : 1;

		// header format
		$styleArray_header = [
		    'font' => [
		        'bold' => true,
		    ],
		    'alignment' => [
		        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
		        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
		        'wrapText' => true
		    ],
		    'borders' => [
		        'top' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		    'fill' => [
		        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
		        'rotation' => 90,
		        'startColor' => [
		            'rgb' => 'fce5cd',
		        ],
		        'endColor' => [
		            'rgb' => 'ffc000',
		        ],
		    ],
		];
		$data_sheet->getStyleByColumnAndRow(1, 1, $source_total_col, 1)->applyFromArray($styleArray_header);
		for($i=1; $i<$source_total_col; $i++) {
			if ($data[0][$i-1] == 'Суть звернення (текстовий опис)') {
				$data_sheet->getColumnDimension( chr(65+$i-1) )->setWidth(120);
			} else {
				$data_sheet->getColumnDimension(chr(65+$i-1))->setAutoSize(true);
			}
		}

		// [END] write SOURCE data
		// **********************************************************



		// [START] write GROUP data
		// **********************************************************

		// write $h_fileds as header from column 3
		$h_fileds_width = 10;

		$col = $start_col_h_fields;
		$row = 1;
		foreach($h_fileds as $key => $item) {
			$key = preg_replace("/\(.*\)/", "", $key);
			$sheet->setCellValueByColumnAndRow($col, $row, $key);
			$sheet->getColumnDimension(chr(65+$col-1))->setWidth($h_fileds_width);
			$col++;
		} // foreach

		// total column
		$total_col = $col-1;
		//$sheet->setCellValueByColumnAndRow($col, $row, 'ЗАГАЛОМ');
		$sheet->getColumnDimension(chr(65+$col-1))->setWidth($h_fileds_width);

		$sheet->getStyleByColumnAndRow(1,1,$total_col ,1)->applyFromArray($styleArray_header);
		$sheet->getStyleByColumnAndRow($total_col, 1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0070c0');
		
		// body format
		$styleArray_0 = [
		    'font' => [
		        'bold' => true,
		    ],
		    'fill' => [
		        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
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
		        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
		        'startColor' => [
		            'rgb' => 'ffff99',
		        ],
		    ],
		];	
	

		// writing data
		// ***************************************************************

		function print_total($sheet, $h_fileds, $row, $total) {

			foreach ($total as $key => $value) {
				$col = $h_fileds[ $key ];
				$sheet->setCellValueByColumnAndRow($col, $row, $value);
			}

		};
		
		$row++;
		foreach ($group_data as $key_0 => $item_0) {
			if ($key_0 == 'total') continue;
			
			$col = 1;
 			$sheet->setCellValueByColumnAndRow($col, $row, $key_0);
 			$sheet->getStyleByColumnAndRow(1,$row,$total_col ,$row)->applyFromArray($styleArray_0);
 			print_total($sheet, $h_fileds, $row, $item_0['total']);

 			$col++;
 			$row++;
			foreach ($item_0 as $key_1 => $item_1) {
				if ($key_1 == 'total') continue;
				
				$col=2;
				$sheet->setCellValueByColumnAndRow($col, $row, $key_1);
				$sheet->getStyleByColumnAndRow(1,$row,$total_col ,$row)->applyFromArray($styleArray_1);
				print_total($sheet, $h_fileds, $row, $item_1['total']);

				$row++;
				$col=3;
				foreach ($item_1 as $key_2 => $item_2) {
					if ($key_2 == 'total') continue;
					$sheet->setCellValueByColumnAndRow($col, $row, $key_2);
					calc_total($TOTAL_NAME, $item_2);
					print_total($sheet, $h_fileds, $row, $item_2);
					$row++;
				} // foreach lvl2
				
			} // foreach lvl1
			
		} // foreach lvl0

		// writing last row total
		// ********************************************************
		$sheet->setCellValueByColumnAndRow(1, $row, $TOTAL_NAME);
		$styleArray = [
		    'font' => [
		        'bold' => true,
		    ],
		    'fill' => [
		        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
		        'startColor' => [
		            'rgb' => 'ffc000',
		        ],
		    ],
		];	
		$sheet->getStyleByColumnAndRow(1,$row,$total_col ,$row)->applyFromArray($styleArray);
		$sheet->getStyleByColumnAndRow($total_col, $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0070c0');
		print_total($sheet, $h_fileds, $row, $group_data['total']);


		// formatting column width
		// ********************************************************

		// column width
		$group_with = 4;
		$sheet->getColumnDimension('A')->setWidth($group_with);
		$sheet->getColumnDimension('B')->setWidth($group_with);
		$sheet->getColumnDimension('C')->setAutoSize(true);

		$sheet->freezePane('A2');

		// conditional

		$conditional1 = new Conditional();
		$conditional1->setConditionType(Conditional::COLOR_SCALE) 
    		->setOperatorType(Conditional::OPERATOR_MIN_MAX)
    		->addCondition('0');
		$conditional1->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);

		$conditionalStyles = $spreadsheet->getActiveSheet()->getStyle('D4:M10')->getConditionalStyles();
		$conditionalStyles[] = $conditional1;
		$spreadsheet->getActiveSheet()->getStyle('D4:M10')->setConditionalStyles($conditionalStyles);


		// [END] write GROUP data
		// **********************************************************




		$spreadsheet->setActiveSheetIndex(0);

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		
		if ( !empty($options['filename']) ) {

			$writer->save( $options['filename'] );

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


	} // excel_report

} // Mcd 


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
}
