<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

//Hi, developing Contracts Section (c) Developer FathomCode
class Contract extends Admin_Controller{

    public function __construct()
    {
        parent::__construct();
        /* Load :: Common */
        $this->load->helper('number');
        $this->load->model('booking/testdrives_model');
        $this->load->model('booking/testdrivecars_model');
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
    }

    public function index(){

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        else {
            /* Title Page */
            $this->page_title->push($this->lang->line('menu_contracts'), $this->lang->line('breadcrumbs_all'));
            $this->data['pagetitle'] = $this->page_title->show();

            /* Breadcrumbs */
            $this->breadcrumbs->unshift(1, $this->lang->line('menu_contracts'), 'contract');
            $this->data['breadcrumb'] = $this->breadcrumbs->show();

            $dealer_centers_options = CRM::get_dealer_centers_helper();
            $this->data['dealer_centers_options'] = $dealer_centers_options;




            $cars_list_options2 = array();
            $options = array();
          

     $cars_list = CRM::get_cars();
             foreach ($cars_list['Result']['Data'] as $car) {
                if ( isset($car['New_new_model_cars_for_td']) && $car['New_new_model_cars_for_td'] === true &&
                    $car['New_out_of_production'] !== true && !empty( $car['New_car_model'] ) ) {
                    $cars_list_options2[] = array("Su_carcomplId" => $car["Su_carcomplId"], "New_td_id" => $car["New_td_id"], "models" => $car["Su_name"] );
           sort($cars_list_options2);

                }

                  }
                  unset($cars_list_options2['New_td_id']);
              $this->data['cars_list_options2'] = $cars_list_options2;

            //MANAGERS LIST IN ADD FORM
            $this->db->select('*')->where('company', $this->user_company)->from("users");

            $managers = $this->db->get()->result_array();
            $managers_options = array();
            foreach ($managers as $manager) {
                if ($manager['has_acces_tdrive'] && $manager['StateCode'] != 1) {
                    $managers_options[$manager['web_userid']] = $manager['first_name'] . " " . $manager['last_name'];
                }
            }

            $ContractStatus_picklist = CRM::picklist_options('new_vehiclestatus');
            $this->data['ContractStatus_picklist'] = $ContractStatus_picklist;
            $this->data['managers_options'] = $managers_options;


            /* Load Template */
            $this->template->admin_render('contract/index', $this->data);
        }
    }


    /**
     * Return all planning actions to DataTable
     * @param $type 'all' OR 'archive'
     * @return Response
     */
    public function get_contracts($type = 'all')
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        else {
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));

            $contract_date_start = strtotime($this->input->get("contract_date_start"));
            $contract_date_end = strtotime($this->input->get("contract_date_end"));
			
			//if (date('H:i:s', $contract_date_end) == '00:00:00' ) $contract_date_end += 24*60*60-1;
			
            //delivered_date
            $delivered_date_start = strtotime($this->input->get("delivered_date_start"));
            $delivered_date_end = strtotime($this->input->get("delivered_date_end"));

			// car_filter
			$car_name = $this->input->get("car_name");

            $start_pagination = ($start/50)+1;
            $CountAllRequests = 0;

            /*
             * Wait API methods
             */
            if ($this->userdata['access_employees']) {
                $contracts = CRM::get_contracts(null);
                $contracts = $contracts['Result']['Data'];
            } else {
                $contracts = CRM::get_contracts($this->ion_auth->user()->row()->company);
                $contracts = $contracts['Result']['Data'];
            }
            $contracts_list = [];
            $i = 0;


            $cars_list_options = CRM::get_booking_cars_helper('Su_carcomplId', true);

            if ($this->userdata['access_employees']) {
                $managers = CRM::get_booking_managers_helper();
            } else {
                $managers = CRM::get_booking_managers_helper($this->ion_auth->user()->row()->company);
            }

            $warehouse_picklist = CRM::picklist_options('new_warehouse');
            $ContractStatus_picklist = CRM::picklist_options('new_contractstatus');
            $VehicleStatus_picklist = CRM::picklist_options('new_vehiclestatus');
			
			// @debug
			// $this->load->library('Utils');
			// file_put_contents('myTest22.html', Utils::prettyPrint($contracts, '$contracts'));
			

        $cars_by_parentmodels = array();
        foreach ($cars_by_parentmodel as $item_parentmodel) {
            $cars_by_parentmodels[$car_by_parentmodel['Su_carcomplId']] = $car_by_parentmodel['Su_name']; 
        }


        $cars_by_parentmodel = CRM::get_booking_cars();
        $cars_by_parentmodel = $cars_by_parentmodel['Result']['Data'];



        $cars_by_parentmodels = array();
        foreach ($cars_by_parentmodel as $item_parentmodel) {
            $cars_by_parentmodels[$item_parentmodel['Su_carcomplId']] = $item_parentmodel['Su_name']; 
        }
            foreach ($contracts as $contract) {
                //ADDED FILTER BY PERIOD New_contractdate
                if ($contract_date_start != false && $contract_date_start > $contract['New_contractdate']) {
                    continue;
                }
                if ($contract_date_end != false && $contract_date_end < $contract['New_contractdate']) {
                    continue;
                }
                //ADDED FILTER BY SALE_DATE New_delivered
                if ($delivered_date_start != false && $delivered_date_start > $contract['New_delivered']) {
                    continue;
                }
                if ($delivered_date_end != false && $delivered_date_end < $contract['New_delivered']) {
                    continue;
                }
	/*
	* filter by SAR CAR NAME`
	*/			
				if ( !empty($car_name) ) {
					
					$sar_car_name = $cars_list_options[$contract['New_model_for_td']];
					if ($sar_car_name != $car_name) continue;
					
				}

                //TYPE SELECT FILTERS
                /*
                 отображается 2 списка:

“В роботі” (она открывается по-умолчанию)
записи, удовлетворяющие следующим критериям:
( ( New_vehiclestatus не равен 5 (відмова) && New_vehiclestatus не равен 6 (Продано) )

( New_delivered == null или (New_delivered.Year == Today.Year && New_delivered.Month == Today.Month) ) )
“Архів”, в которые будут попадать остальные записи */
                if ($type == 'full') { //“Вcі

                } elseif ($type == "archive") {

                /*    echo "<pre>";
                    echo "<pre>";
                    var_dump(!(($contract['New_vehiclestatus'] == 5 || $contract['New_vehiclestatus'] == 6)
                        ||
                        ($contract['New_delivered'] != null &&
                            (date('n', $contract['New_delivered']) != date('n') || date('Y', $contract['New_delivered']) != date('Y'))  )));
                    var_dump(($contract['New_vehiclestatus'] != 5 && $contract['New_vehiclestatus'] != 6));
                    var_dump(($contract['New_delivered'] == null  ||
                        (date('n', $contract['New_delivered']) == date('n') && date('Y', $contract['New_delivered']) == date('Y')) ));
                    echo "</hr>";*/

                    if (
                        ($contract['New_vehiclestatus'] != 5 && $contract['New_vehiclestatus'] != 6)
                       &&
                        ($contract['New_delivered'] == null  ||
                            (date('n', $contract['New_delivered']) == date('n') && date('Y', $contract['New_delivered']) == date('Y')) )
                    ) {
                        continue;
                    }
                } else { //“В роботі
                    if (
                        ($contract['New_vehiclestatus'] == 5 || $contract['New_vehiclestatus'] == 6)
                        ||
                        ($contract['New_delivered'] != null &&
                            (date('n', $contract['New_delivered']) != date('n') || date('Y', $contract['New_delivered']) != date('Y'))  )
                    ) {
                        continue;
                    }
                }

                //Contract ID
                //$contracts_list[$i][] = $contract['New_offerId'];

                //Request ID
                $contracts_list[$i][] = $contract['New_registration_tdriveId'];

                //Дилер
                $contracts_list[$i][] = $contract['New_salon_name'];

                //CreatedOn
                //if ($contract['CreatedOn'] != null) { $contracts_list[$i][] = date("d.m.Y",$contract['CreatedOn']); } else { $contracts_list[$i][] = ""; }

                //Дата контракту new_ContractDate
                if ($contract['New_contractdate'] != null) { $contracts_list[$i][] = date("d.m.Y",$contract['New_contractdate']); } else { $contracts_list[$i][] = ""; }
                //$contracts_list[$i][] = $contract['New_contractdate'];

                //Клієнт (ПІБ)
                //if ($contract['FullName'] != null) { $contracts_list[$i][] = $contract['FullName']; } else { $contracts_list[$i][] = ""; }
                $contracts_list[$i][] = $contract['LastName'] . " " . $contract['FirstName'] . " " . $contract['MiddleName'];

                //Мобільний телефон
                //if ($contract['MobilePhone'] != null) { $contracts_list[$i][] = $contract['MobilePhone']; } else { $contracts_list[$i][] = ""; }
                $contracts_list[$i][] = $contract['MobilePhone'];

                //Склад/виробництво
                $contracts_list[$i][] = @$warehouse_picklist[$contract['New_warehouse']];
                /*if($contract['New_warehouse'] == 1) {
                    $contracts_list[$i][] = "Склад";
                }elseif($contract['New_warehouse'] == 2) {
                    $contracts_list[$i][] = "Замовлення у виробництво";
                } else {
                    $contracts_list[$i][] = "";
                }*/
                //$contracts_list[$i][] = $contract['New_warehouse'];

                //Номер кузова
                //if ($contract['Su_name'] != null) { $contracts_list[$i][] = $contract['Su_name']; } else { $contracts_list[$i][] = ""; }
                $contracts_list[$i][] = $contract['New_vin'];

                //Номер замовлення у виробництво
                $contracts_list[$i][] = $contract['New_order_name'];

                //Модель SAR DONE: Need add car name to CRM API
                $contracts_list[$i][] = $cars_list_options[$contract['New_model_for_td']];
                //$contracts_list[$i][] = $contract['New_model'];







                $contracts_list[$i][] = $cars_by_parentmodels[$contract['New_modelversionid']];

                //Модель new_Model
 //               //$vehicle = CRM::get_suvehicle_by_id($contract['New_vehicle_id']);
                //$vehicle = $vehicle['Result']['Data'];/
                //$contracts_list[$i][] = $contract['Su_carcomplvehicleName'];
                $contracts_list[$i][] = $contract['New_model'];


                //Тип оплати  new_ContractStatus
                $contracts_list[$i][] = @$ContractStatus_picklist[$contract['New_contractstatus']];

                //Статус контракту
                $contracts_list[$i][] = @$VehicleStatus_picklist[$contract['New_vehiclestatus']];

                //new_Delivere
                if ($contract['New_delivered'] != null) { $contracts_list[$i][] = date("d.m.Y",$contract['New_delivered']); } else { $contracts_list[$i][] = ""; }

               /* if ($contract['New_delivered'] != null) {
                    echo "<pre>";
             var_dump($contract);
                    die;
                }*/
                //Продавець - поле “Менеджер”
                if ($contract['New_web_user_id'] != null) {
                    //$web_user = CRM::get_webuser_by_id($contract['New_web_user_id']);
                    //$web_user = $web_user['Result']['Data'];
                    //$contracts_list[$i][] = $web_user['New_lastname'] . " " . $web_user['New_name'];

                    //NEW OPTIMIZATION LOADING SPEED
                    $contracts_list[$i][] = $managers[$contract['New_web_user_id']];
                } else { $contracts_list[$i][] = ""; }

                //Дата гарантійного листа - new_GuaranteeLetter
                //if ($contract['New_guaranteeletter'] ) { $contracts_list[$i][] = date("d.m.Y",$contract['New_guaranteeletter']); } else { $contracts_list[$i][] = ""; }

                //Дата угоди в банку - new_PaymentRemittance
                //if ($contract['New_paymentremittance'] ) { $contracts_list[$i][] = date("d.m.Y",$contract['New_paymentremittance']); } else { $contracts_list[$i][] = ""; }


//                $requests_list[$i][] = @$ContractStatus_picklist[$contract['New_ContractStatus']];
/*                if ($contract['New_contractstatus'] == 1 ) {
                    $contracts_list[$i][] = "Так";
                } else if ($contract['New_contractstatus'] == 2 ) {
                    $contracts_list[$i][] = "Ні";
                } else {
                    $contracts_list[$i][] = "";
                }*/

                //Митне очищення new_CustomsClearance 1 = Так  2 = Ні
                if ($contract['New_customsclearance'] == 1 ) {
                    $contracts_list[$i][] = "Так";
                } else if ($contract['New_customsclearance'] == 2 ) {
                    $contracts_list[$i][] = "Ні";
                } else {
                    $contracts_list[$i][] = "";
                }
                //$contracts_list[$i][] = $contract['Так'];

                // Місцезнаходження авто - new_CarLocation
                /*1 = Дилер; 2 = Консигнація іншого дилера; 3 = Склад в Києві; 4 = Транзит дилерам; 5 = Міжнародний транзит; 6 = Замовлення у виробництв*/
                $CarLocationList = array(
                    '1' => "Дилер",
                    '2' => "Консигнація іншого дилера",
                    '3' => "Склад в Києві",
                    '4' => "Транзит дилерам",
                    '5' => "Міжнародний транзит",
                    '6' => "Замовлення у виробництв");
                $contracts_list[$i][] = $CarLocationList[$contract['New_carlocation']];

                //Дата прибуття до дилера - new_ArrivalToDealer
                if ($contract['New_arrivaltodealer'] ) { $contracts_list[$i][] = date("d.m.Y",$contract['New_arrivaltodealer']); } else { $contracts_list[$i][] = ""; }
                //$contracts_list[$i][] = $contract['06.05.2021'];

                // 100% буде продано в поточному місяці - new_ConfidentDelivery
                if ($contract['New_confidentdelivery'] ) { $contracts_list[$i][] = date("d.m.Y",$contract['New_confidentdelivery']); } else { $contracts_list[$i][] = ""; }
                //$contracts_list[$i][] = $contract['06.05.2021'];

                //Запланована дата продажу в наступних місяцях - new_PostponedDelivery
                if ($contract['New_postponeddelivery'] ) { $contracts_list[$i][] = date("d.m.Y",$contract['New_postponeddelivery']); } else { $contracts_list[$i][] = ""; }
                //$contracts_list[$i][] = $contract['06.05.2021'];

                //Продано в поточному місяці - new_Delivered
                //if ($contract['New_delivered'] ) { $contracts_list[$i][] = date("d.m.Y",$contract['New_delivered']); } else { $contracts_list[$i][] = ""; }
                //$contracts_list[$i][] = $contract['06.05.2021'];

                //Коментарі - new_Comments
                $contracts_list[$i][] = $contract['New_comments'];

                //Конкуренти, з якими клієнт порівнював
                $contracts_list[$i][] = $contract['New_competitorcriteria'];

                //Що було визначальним у явиборі
                $contracts_list[$i][] = $contract['New_competitornames'];


                //New_pricelist_price New_invoice_price
                $contracts_list[$i][] = $contract['New_pricelist_price'];
                $contracts_list[$i][] = $contract['New_invoice_price'];

                //$contracts_list[$i][] = '<td><button data-offerid="'. $contract['New_offerId'] .'" class="btn-delete-contract"><i class="fa fa-trash"></i></button></td>';

                $client_type = $contract['New_client_type'];
                if (2 == $contract['New_client_type'] && 100000005 == $contract['New_saleschannel'])
                {
                    $client_type = '2r';
                }
                
                $contracts_list[$i][] = $client_type;
				
                $i++;
            }
			
            if (isset($_REQUEST['lead_type']) && !empty($_REQUEST['lead_type']))
            {
                $lead_types = explode(';', $_REQUEST['lead_type']);

                $contracts_list = array_filter($contracts_list, function($el) use($lead_types) {
                    $last_item = count($el) - 1;

                    if (null == $el[$last_item] && in_array('1', $lead_types))
                    {
                        return true;
                    }
					

                    if (in_array(strval($el[$last_item]), $lead_types))
                    {
                        return true;
                    }

                    return false;
                });
            }

            $result = array(
                "draw" => $draw,
                "recordsTotal" => count($contracts_list), //$CountAllRequests+$start,
                "recordsFiltered" => count($contracts_list), //$CountAllRequests+$start,
                "data" => array_values($contracts_list)
            );

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit();
        }

    }

    //Contract edit
    function edit($contractId = null) {
        /*if ($contractId == null) {
            $this->session->set_flashdata('message', "Ошибка! Контракт не найден");
            redirect('contract');
        }*/
        $contractId = urldecode($contractId);
        $user_company = $this->ion_auth->user()->row()->company;



        if ($this->ion_auth->is_admin() || $this->userdata['has_acces_newtdrive'] || $this->userdata['access_employees']) {
            $this->page_title->push($this->lang->line('breadcrumbs_client_edit'));
            $this->data['pagetitle'] = $this->page_title->show();


            $cars_list_options = array();
            $cars_list = CRM::get_booking_cars();
            foreach ($cars_list['Result']['Data'] as $car) {
                $cars_list_options[$car['Su_carcomplId']] = $car['Su_name'];
            }

            $dealer_centers_options = CRM::get_dealer_centers_helper($this->ion_auth->user()->row()->company);


            /* Get contract INFO */
            //TODO: get contract CRM API method
            $contract = CRM::get_contract($contractId);

            $contract = $contract['Result']['Data'];
            //$contract = $this->get_contracts_array();
            //$contract = $contract[0];

            /* Get contract requests *///TODO: get_contract_registrations
            //$contract_requests = array();//CRM::get_contract_registrations($contractId);
            //$contract_requests = CRM::get_contract_registrations($contractId);
            $contract_requests = CRM::get_registrations_all_by_phone($contract['MobilePhone']);
            $contract_requests = $contract_requests['Result']['Data'];


            if (empty($contract)) {
                $this->session->set_flashdata('message', "Помилка! Контракт не знайдений");
                redirect('contract');
            }


            /* Validate form input */
            $this->form_validation->set_rules('contractId', 'ID контракту', 'required');
            $this->form_validation->set_rules('new_warehouse', ' Склад/виробництво', 'required');
            $this->form_validation->set_rules('new_VIN', 'Номер кузова', 'required');
            $this->form_validation->set_rules('new_ContractStatus', 'Тип оплати', 'required');
            $this->form_validation->set_rules('new_ContractDate', 'Дата контракту', 'required');
            $this->form_validation->set_rules('new_VehicleStatus', 'Статус контракту', 'required');

            if ($this->form_validation->run() == TRUE) {

                $crm_data = array(
                    'contractId' => $this->input->post('contractId'),
                    'new_VIN' => $this->input->post('new_VIN'),
                    //'registration_tdriveId' => "1b31f3fa-6719-eb11-81c0-00155d1f050b",//TEST registration_tdriveId
                    //'New_web_userId' => "87965993-6ab3-e911-81a9-00155d1f050b",//TEST registration_tdriveId
                    'registration_tdriveId' => $this->input->post('registration_tdriveId'),
                    'New_web_userId' => $this->input->post('New_web_userId'),
                    'new_warehouse' => $this->input->post('new_warehouse'),
                    'new_OrderNumber' => $this->input->post('new_OrderNumber'),
                    'new_Model' => $this->input->post('new_Model'),
                    'new_ContractDate' => strtotime($this->input->post('new_ContractDate')),
                    'new_ArrivalToDealer' => strtotime($this->input->post('new_ArrivalToDealer')),
                    'new_PostponedDelivery' => strtotime($this->input->post('new_PostponedDelivery')),
                    'new_GuaranteeLetter' => strtotime($this->input->post('new_GuaranteeLetter')),
                    'new_PaymentRemittance' => strtotime($this->input->post('new_PaymentRemittance')),
                    'new_ConfidentDelivery' => strtotime($this->input->post('new_ConfidentDelivery')),
                    'new_Delivered' => strtotime($this->input->post('new_Delivered')),
                    'new_ContractStatus' => $this->input->post('new_ContractStatus'),
                    'new_CustomsClearance' => $this->input->post('new_CustomsClearance'),
                    'new_VehicleStatus' => $this->input->post('new_VehicleStatus'),
                    'new_CarLocation' => $this->input->post('new_CarLocation'),
                    'new_CompetitorCriteria' => $this->input->post('new_CompetitorCriteria'),
                    'new_CompetitorNames' => $this->input->post('new_CompetitorNames'),
                    'new_Comments' => $this->input->post('new_Comments'),
                    'New_order_id' => $contract['New_order_id'],
                    'New_pricelist_price' => $contract['New_pricelist_price'],
                    'New_invoice_price' => $contract['New_invoice_price']
                );
            }


            if ($this->form_validation->run() == TRUE && CRM::put_contract_edit($crm_data)) {
                $this->session->set_flashdata('message', "Контракт успішно змінений");
                redirect('contract/edit/'.$crm_data['contractId']);
            } else {
                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

                $this->data['cars_list_options'] = $cars_list_options;
                $this->data['dealer_centers_options'] = $dealer_centers_options;

                $this->data['contract'] = $contract;
                $this->data['contract_requests'] = $contract_requests;

                /*Сontract inputs*/
                //new_VIN
                $this->data['new_VIN'] = array(
                    'name' => 'new_VIN', 'id' => 'new_VIN',
                    'type' => 'input', 'class' => 'form-control',
                    //'disabled' => 'disabled',
                    'minlength'=>"17", 'maxlength'=>"17",
                    'value' => $contract['New_vin'],
                );

                //new_OrderNumber
                $this->data['new_OrderNumber'] = array(
                    'name' => 'new_OrderNumber', 'id' => 'new_OrderNumber',
                    'type' => 'input', 'class' => 'form-control',
                    'value' => $contract['New_OrderNumber'],
                );

                //new_warehouse
                $this->data['new_warehouse'] = array(
                    'name' => 'new_warehouse', 'id' => 'new_warehouse',
                    'type' => 'select', 'class' => 'form-control',
                    'required' => "required",
                    'value' => $contract['New_warehouse'],
                );

                //new_Model
                $this->data['new_Model'] = array(
                    'name' => 'new_Model', 'id' => 'new_Model',
                    'type' => 'input', 'class' => 'form-control',
                    'value' => $contract['New_model'],
                );

                $this->data['New_dealer'] = array(
                    'name' => 'New_dealer', 'id' => 'New_dealer',
                    'type' => 'select', 'class' => 'form-control',
                    'disabled' => 'disabled',
                    'value' => $contract['Su_accountvehicle'],
                );

                //new_ContractDate
                $this->data['new_ContractDate'] = array(
                    'name' => 'new_ContractDate', 'id' => 'new_ContractDate',
                    'type' => 'input', 'class' => 'form-control datepicker',
                    'value' => ($contract['New_contractdate'] != null) ? date("d.m.Y", $contract['New_contractdate']) : "",
                );

                /*Contract lead inputs*/
                $this->data['FullName'] = array(
                    'name' => 'FullName', 'id' => 'FullName',
                    'type' => 'input', 'class' => 'form-control',
                    'value' => $contract['FullName'],
                );

                $this->data['contract_phone'] = array(
                    'name' => 'contract_phone', 'id' => 'contract_phone',
                    'type' => 'text', 'class' => 'form-control',
                    'value' => $contract['MobilePhone'],
                );

                //Dates
                //   new_GuaranteeLetter & new_PaymentRemittance
                $this->data['new_GuaranteeLetter'] = array(
                    'name' => 'new_GuaranteeLetter', 'id' => 'new_GuaranteeLetter',
                    'type' => 'input', 'class' => 'form-control datepicker',
                    'value' => ($contract['New_guaranteeletter'] != null) ? date("d.m.Y", $contract['New_guaranteeletter']) : "",
                );

                //new_PaymentRemittance
                $this->data['new_PaymentRemittance'] = array(
                    'name' => 'new_PaymentRemittance', 'id' => 'new_PaymentRemittance',
                    'type' => 'input', 'class' => 'form-control datepicker',
                    'value' => ($contract['New_paymentremittance'] != null) ? date("d.m.Y", $contract['New_paymentremittance']) : "",
                );

                //Statuses
                //new_ContractStatus & new_VehicleStatus
                $this->data['new_ContractStatus'] = array(
                    'name' => 'new_ContractStatus', 'id' => 'new_ContractStatus',
                    'type' => 'select', 'class' => 'form-control',
                    'value' => $contract['New_contractstatus'],
                );
                $this->data['new_VehicleStatus'] = array(
                    'name' => 'new_VehicleStatus', 'id' => 'new_VehicleStatus',
                    'type' => 'select', 'class' => 'form-control',
                    'value' => $contract['New_vehiclestatus'],
                );

                $this->data['new_CarLocation'] = array(
                    'name' => 'new_CarLocation', 'id' => 'new_CarLocation',
                    'type' => 'select', 'class' => 'form-control',
                    'value' => $contract['New_carlocation'],
                );


                $this->data['new_CustomsClearance'] = array(
                    'name' => 'new_CustomsClearance', 'id' => 'new_CustomsClearance',
                    'type' => 'select', 'class' => 'form-control',
                    'value' => $contract['New_customsclearance'],
                );


                //Dates 2
                //   new_ArrivalToDealer & new_ConfidentDelivery & new_PostponedDelivery & new_Delivered
                $this->data['new_ArrivalToDealer'] = array(
                    'name' => 'new_ArrivalToDealer', 'id' => 'new_ArrivalToDealer',
                    'type' => 'input', 'class' => 'form-control datepicker',
                    'value' => ($contract['New_arrivaltodealer'] != null) ? date("d.m.Y", $contract['New_arrivaltodealer']) : "",
                );

                //new_ConfidentDelivery
                $this->data['new_ConfidentDelivery'] = array(
                    'name' => 'new_ConfidentDelivery', 'id' => 'new_ConfidentDelivery',
                    'type' => 'input', 'class' => 'form-control datepicker',
                    'value' => ($contract['New_confidentdelivery'] != null) ? date("d.m.Y", $contract['New_confidentdelivery']) : "",
                );

                //new_PostponedDelivery
                $this->data['new_PostponedDelivery'] = array(
                    'name' => 'new_PostponedDelivery', 'id' => 'new_PostponedDelivery',
                    'type' => 'input', 'class' => 'form-control datepicker',
                    'value' => ($contract['New_postponeddelivery'] != null) ? date("d.m.Y", $contract['New_postponeddelivery']) : "",
                );

                //new_Delivered
                $this->data['new_Delivered'] = array(
                    'name' => 'new_Delivered', 'id' => 'new_Delivered',
                    'type' => 'input', 'class' => 'form-control datepicker',
                    'value' => ($contract['New_delivered'] != null) ? date("d.m.Y", $contract['New_delivered']) : "",
                );

                //New_web_userId 87965993-6ab3-e911-81a9-00155d1f050b
                $this->data['New_web_userId'] = array(
                    'name' => 'New_web_userId', 'id' => 'New_web_userId',
                    'type' => 'input', 'class' => 'form-control',
                    'value' => ($contract['New_web_userid'] != null) ? $contract['New_web_userid'] : "",
                );
                //new_Comments
                $this->data['new_Comments'] = array(
                    'name' => 'new_Comments', 'id' => 'new_Comments',
                    'type' => 'input', 'class' => 'form-control',
                    'value' => ($contract['New_comments'] != null) ? $contract['New_comments'] : "",
                );

                //new_CompetitorCriteria
                $this->data['new_CompetitorCriteria'] = array(
                    'name' => 'new_CompetitorCriteria', 'id' => 'new_CompetitorCriteria',
                    'type' => 'input', 'class' => 'form-control',
                    'value' => ($contract['New_competitorcriteria'] != null) ? $contract['New_competitorcriteria'] : "",
                );
                //new_CompetitorNames
                $this->data['new_CompetitorNames'] = array(
                    'name' => 'new_CompetitorNames', 'id' => 'new_CompetitorNames',
                    'type' => 'input', 'class' => 'form-control',
                    'value' => ($contract['New_competitornames'] != null) ? $contract['New_competitornames'] : "",
                );



                $this->db->select('*')->where('company', $user_company)->from("users");

                $managers = $this->db->get()->result_array();
                $managers_options = array();
                foreach ($managers as $manager) {
                    if ($manager['has_acces_tdrive'] && $manager['StateCode'] != 1) {
                        $managers_options[$manager['web_userid']] = $manager['first_name'] . " " . $manager['last_name'];
                    }
                }

                $this->data['managers_options'] = $managers_options;


                /* Breadcrumbs */
                $this->breadcrumbs->unshift(1, $this->lang->line('breadcrumbs_client_edit'), 'contract');
                $this->data['breadcrumb'] = $this->breadcrumbs->show();

                /* Load Template */
                $this->template->admin_render('contract/edit', $this->data);
            }
        } else {
            $this->session->set_flashdata('message', "У Вас недостатньо прав для цьої дії");
            redirect('contract');
        }


    }

    //Contract edit

    function createContractPost() {
        /*if ($contractId == null) {
            $this->session->set_flashdata('message', "Ошибка! Контракт не найден");
            redirect('contract');
        }*/
        $contractId = $this->input->post('contractId');
        $user_company = $this->ion_auth->user()->row()->company;

        if ($this->input->method(TRUE) == "POST" && ($this->ion_auth->is_admin() || $this->userdata['has_acces_newtdrive'] || $this->userdata['access_employees'])) {

            $cars_list_options = array();
            $cars_list = CRM::get_booking_cars();
            foreach ($cars_list['Result']['Data'] as $car) {
                $cars_list_options[$car['Su_carcomplId']] = $car['Su_name'];
            }

            $dealer_centers_options = CRM::get_dealer_centers_helper($this->ion_auth->user()->row()->company);

            $contract = CRM::get_contract($contractId);
            $contract = $contract['Result']['Data'];

            $crm_data = array(
                'contractId' => $this->input->post('contractId'),
                'contract_phone' => $this->input->post('contract_phone'),
                'FullName' => $this->input->post('FullName'),
                'new_VIN' => $this->input->post('new_VIN'),
                //'registration_tdriveId' => "1b31f3fa-6719-eb11-81c0-00155d1f050b",//TEST registration_tdriveId
                //'New_web_userId' => "87965993-6ab3-e911-81a9-00155d1f050b",//TEST registration_tdriveId
                'registration_tdriveId' => $this->input->post('registration_tdriveId'),
                'New_web_userId' => $this->input->post('New_web_userId'),
                'new_warehouse' => $this->input->post('new_warehouse'),
                'new_OrderNumber' => $this->input->post('new_OrderNumber'),
                'new_Model' => $this->input->post('new_Model'),
                'new_ContractDate' => ($this->input->post('new_ContractDate') == "") ? "" : strtotime($this->input->post('new_ContractDate'))+21600,
                'new_ArrivalToDealer' => ($this->input->post('new_ArrivalToDealer') == "") ? "" : strtotime($this->input->post('new_ArrivalToDealer')),
                'new_PostponedDelivery' => ($this->input->post('new_PostponedDelivery') == "") ? "" : strtotime($this->input->post('new_PostponedDelivery')),
                'new_GuaranteeLetter' => ($this->input->post('new_GuaranteeLetter') == "") ? "" : strtotime($this->input->post('new_GuaranteeLetter')),
                'new_PaymentRemittance' => ($this->input->post('new_PaymentRemittance') == "") ? "" : strtotime($this->input->post('new_PaymentRemittance')),
                'new_ConfidentDelivery' => ($this->input->post('new_ConfidentDelivery') == "") ? "" : strtotime($this->input->post('new_ConfidentDelivery')),
                'new_Delivered' => ($this->input->post('new_Delivered') == "") ? "" : strtotime($this->input->post('new_Delivered')),
                'new_ContractStatus' => $this->input->post('new_ContractStatus'),
                'new_CustomsClearance' => $this->input->post('new_CustomsClearance'),
                'new_VehicleStatus' => $this->input->post('new_VehicleStatus'),
                'new_CarLocation' => $this->input->post('new_CarLocation'),
                'new_CompetitorCriteria' => $this->input->post('new_CompetitorCriteria'),
                'new_CompetitorNames' => $this->input->post('new_CompetitorNames'),
                'new_Comments' => $this->input->post('new_Comments'),
                'New_order_id' => $contract['New_order_id'],

                'new_bankid' => $this->input->post('new_bankid'),
                'new_first_installment' => $this->input->post('new_first_installment'),
                'new_term' => $this->input->post('new_term'),
                'new_date_bank_application' => ($this->input->post('new_date_bank_application') == "") ? "" : strtotime($this->input->post('new_date_bank_application')),
                'new_credit_agricole_kredobank' => $this->input->post('new_credit_agricole_kredobank'),
                'new_another_bank_reason' => $this->input->post('new_another_bank_reason'),
                'new_history' => $this->input->post('new_history'),
                'contract_creditid' => $this->input->post('contract_creditid'),

                'new_insurance_company' => $this->input->post('new_insurance_company'),
                'new_rate' => $this->input->post('new_rate'),
                'new_franchise' => $this->input->post('new_franchise'),
                'new_credit_conditions' => $this->input->post('new_credit_conditions'),
                
                'parentmodels' => $this->input->post('parentmodels'),
                'New_pricelist_price' => $this->input->post('New_pricelist_price'),
                'New_invoice_price' => $this->input->post('New_invoice_price')
            );

            if ($contractId == null) {
                if ($this->input->post('new_ContractDate') == "" && empty(CRM::get_contract_by_registration_tdriveId($this->input->post('registration_tdriveId')))) {
                    //$this->session->set_flashdata('message', "Контракти вже існує");
                    $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => "success",
                        "message" => 'Контракти вже існує.',
                        "New_registration_tdriveId" => $crm_data['registration_tdriveId']
                    )));
                } else {
                    $crm_data['new_registration_id'] = $crm_data['registration_tdriveId'];
                    $New_offerId = CRM::post_contract_create($crm_data);

                    if ($New_offerId) {

                        $New_offerId = $New_offerId['Result']['Data'];

                        $crm_data['New_offerId'] = $New_offerId;
                        if ($this->input->post('new_date_bank_application') != "") {
	                        if ($crm_data['contract_creditid'] == "") {
	                            $crm_data['New_creditid'] = CRM::post_credit_create($crm_data);
	                        } else {
	                            $crm_data['New_creditid'] = CRM::put_credit_edit($crm_data);
	                        }
	                    }

                        $this->session->set_flashdata('message', "Контракт успішно доданий");
                        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => "success",
                            "message" => 'Контракти успішно додані.',
                            "New_registration_tdriveId" => $crm_data['registration_tdriveId'],
                            "New_offerId" => $New_offerId['Id'],
                            "New_creditid" => $crm_data['New_creditid']['Result']['Data']['Id']
                        )));

                        $this->session->set_flashdata('message', "Контракт успішно доданий");
                    }
                }
            } else {
                if (CRM::put_contract_edit($crm_data)) {

                    $crm_data['New_offerId'] = $New_offerId;

                    if ($this->input->post('new_date_bank_application') != "") {
	                    if ($crm_data['contract_creditid'] == "") {
	                        $crm_data['New_creditid'] = CRM::post_credit_create($crm_data);
	                    } else {
	                        $crm_data['New_creditid'] = CRM::put_credit_edit($crm_data);
	                    }
	                }


                    $this->session->set_flashdata('message', "Контракт успешно изменен");
                    $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => "success",
                        "message" => 'Контракт успещно изменен.',
                        "New_registration_tdriveId" => $crm_data['registration_tdriveId'],
                        "New_offerId" => $contractId,
                        "New_creditid" => $crm_data['New_creditid']['Result']['Data']['Id']
                    )));

                    $this->session->set_flashdata('message', "Контракт успішно змінений");

                }
            }
        } else {

            $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                'status' => "fail",
                "message" => 'Only POST request.'
            )));
        }





    }

    //delete Contract

    function deleteContractPost() {
        $offerId = $this->input->post('offerId');
        $user_company = $this->ion_auth->user()->row()->company;

        if ($offerId == null) {
            $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                'status' => "fail",
                "message" => 'Помилка! Контракту не знайдено.'
            )));
        }


        if ($this->input->method(TRUE) == "POST" && ($this->ion_auth->is_admin() || $this->userdata['has_acces_newtdrive'] || $this->userdata['access_employees'])) {
            $crm_data = array('offerId' => $offerId);
            $New_offerId = CRM::post_contract_delete($crm_data);
            $New_offerId = $New_offerId['Result']['Data'];

            if ($New_offerId == "Entity is deleted") {
                $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => "success",
                    "message" => 'Контракт успішно видалено.',
                    "New_registration_tdriveId" => $crm_data['registration_tdriveId'],
                    "New_offerId" => $New_offerId['Id']
                )));
            } else {
                $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => "fail",
                    "message" => 'Помилка! Не вдалось выдалити контракт.'
                )));
            }
        } else {
            $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                'status' => "fail",
                "message" => 'Only POST request.'
            )));
        }





    }

    /**
     * CREATE REQUEST AND NEW CONTRACT(clear)
     *
     * @return Response
     */
    public function createRequestAndContractPost()
    {


        if ($this->input->method(TRUE) == "POST") {

            $manager_webuser = $this->db->select('web_userid')->where('id', $this->input->post('testdrive_manager'))->get("users")->result_array();
            $manager_webuser = $manager_webuser[0]['web_userid'];

            $post_data = array(
                'carcomplId' => $this->input->post('car_model'),
                'dealer_center_id' => $this->input->post('dealer_center_id'),
                'car_id' => $this->input->post('car_model'),
                //'testdrive_date' => $this->input->post('testdrive_date'),
                'testdrive_manager' => $this->input->post('testdrive_manager'),
                'client_name' => $this->input->post('client_name'),
                'client_surname' => $this->input->post('client_surname'),
                'client_phone' => $this->input->post('client_phone'),
                'client_email' => $this->input->post('client_email'),
                'testdrive_addinfo' => $this->input->post('testdrive_addinfo'),
                'testdrive_status' => 100000001,
                'request_type' => 6,
                //'testdrive_status' => $this->input->post('testdrive_status'),
                'webuserid' => $manager_webuser
            );

            if (
                $post_data['carcomplId'] == "" ||
                $post_data['dealer_center_id'] == "" ||
                $post_data['car_id'] == "" ||
                //$post_data['testdrive_date'] == "" ||
                $post_data['testdrive_manager'] == "" ||
                $post_data['client_name'] == "" ||
                $post_data['client_phone'] == ""
            ) {
                $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => "fail",
                    "message" => 'Ошибка! Не заполнены обязательные поля!'
                )));
            } else {
                if ($testdrive_id = $this->testdrives_model->add_testdrive($post_data)) {

                    $this->session->set_flashdata('message', "Запит успішно доданий");


                    $contract_data = array('new_registration_id' => $testdrive_id);

                    $New_offerId = CRM::post_contract_create($contract_data);
                    $New_offerId = $New_offerId['Result']['Data'];

                    if ($New_offerId['Id'] != null) {

                        $this->session->set_flashdata('message', "Контрак успішно доданий");
                        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => "success",
                            "message" => 'Запит і Контракт успішно доданий.',
                            "New_registration_tdriveId" => $testdrive_id,
                            "New_offerId" => $New_offerId['Id']
                        )));
                    } else {
                        $this->session->set_flashdata('message', "При створені контракта виникла помилка");
                        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                            'status' => "fail",
                            "message" => 'Помилка створення контракта.'
                        )));
                    }
                } else {
                    $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                        'status' => "fail",
                        "message" => 'Помилка створення запиту.'
                    )));
                }
            }
        } else {

            $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                'status' => "fail",
                "message" => 'Only POST request.'
            )));
        }
    }


    /**
     * CONTRACT NEW History Comment Add REQUEST
     *
     * @return Response
     */
    public function new_historyCommentAdd()
    {

        if ($this->input->method(TRUE) == "POST") {
            $creditid = $this->input->post('creditid');

            $manager_webuser = $this->db->select('')->where('web_userid', $this->input->post('webuser'))->get("users")->result_array();
            $manager_webuser = $manager_webuser[0];
            
            $manager_first_name = $manager_webuser['first_name'];
            $manager_last_name = $manager_webuser['last_name'];


            $crm_credit = CRM::get_credit($creditid);
            $crm_credit = $crm_credit['Result']['Data'];

            $New_history = $crm_credit['New_history'];

            $CommentList = json_decode($New_history);

            $CommentList[] = array(
                'webuser' => $this->input->post('webuser'),
                'author' => $manager_first_name . " " . $manager_last_name,
                'comment' => $this->input->post('comment'),
                'date' => time()
            );

            $crm_credit['new_history'] = json_encode($CommentList, JSON_UNESCAPED_UNICODE);
            $editStatus = CRM::put_credit_history_edit($crm_credit);


            if (
                $editStatus["Result"]["Data"] == "Entity updated"
            ) {


                $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => "success",
                    "message" => 'Коментар був доданий!',
                    'CommentList' => $CommentList
                )));
            } else {
                $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                    'status' => "fail",
                    "message" => 'Ошибка! Не заполнены обязательные поля!'
                )));
            } 
        } else {

            $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array(
                'status' => "fail",
                "message" => 'Only POST request.'
            )));
        }
    }


    /**
     * Export all contracts to Excel
     * @param $type 'all' OR 'archive'
     * @return Response
     */
    public function export($type='all', $dealer_filter = null)
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        else {
            $draw = intval($this->input->get("draw"));
            $start = intval($this->input->get("start"));
            $length = intval($this->input->get("length"));


            $contract_date_start = strtotime($this->input->get("contract_date_start"));
            $contract_date_end = strtotime($this->input->get("contract_date_end"));
            

            //delivered_date
            $delivered_date_start = strtotime($this->input->get("delivered_date_start"));
            $delivered_date_end = strtotime($this->input->get("delivered_date_end"));


            $start_pagination = ($start/50)+1;
            $CountAllRequests = 0;

			// car_filter
			$car_name = $this->input->get("car_name");


            /*
             * Wait API methods
             */
            if ($this->userdata['access_employees']) {
                $contracts = CRM::get_contracts(null);
                $contracts = $contracts['Result']['Data'];
            } else {
                $contracts = CRM::get_contracts($this->ion_auth->user()->row()->company);
                $contracts = $contracts['Result']['Data'];
            }
            $contracts_list = [];
            //$i = 0;

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            // auto fit column to content
            // Set document properties
            $spreadsheet->getProperties()->setCreator('Andre Khanenko')
                ->setTitle('Експорт контрактів')
                ->setSubject('Експорт контрактів')
                ->setDescription('Експорт контрактів');
            // add style to the header
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'bottom' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => array('rgb' => '333333'),
                    ),
                ),
                'fill' => array(
                    'type'       => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation'   => 90,
                    'startcolor' => array('rgb' => '0d0d0d'),
                    'endColor'   => array('rgb' => 'f2f2f2'),
                ),
            );



            // add style to the header
            $styleAlignColumnsArray = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                )
            );

            $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);

            $spreadsheet->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($styleArray);
            // auto fit column to content
            foreach(range('A', 'Z') as $columnID) {
                $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }

            $spreadsheet->getActiveSheet()->getStyle('AA1:AA1')->applyFromArray($styleArray);
                $spreadsheet->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);


            // set the names of header cells
            $sheet->setCellValue('A1', 'Дилер');
            $sheet->setCellValue('B1', 'Клієнт (ПІБ)');
            $sheet->setCellValue('C1', 'Мобільний телефон');
            $sheet->setCellValue('D1', 'Електронна адреса');
            $sheet->setCellValue('E1', 'Стать');
            $sheet->setCellValue('F1', 'Місто проживання');
            $sheet->setCellValue('G1', 'Згода на використання персональних даних');
//            $sheet->setCellValue('D1', 'Дата народження');
            $sheet->setCellValue('H1', 'Склад/виробництво');
            $sheet->setCellValue('I1', 'Номер кузова');
            $sheet->setCellValue('J1', 'Номер замовлення у виробництво');
            $sheet->setCellValue('K1', 'Модель SAR');
            $sheet->setCellValue('L1', 'Комплектація');
            $sheet->setCellValue('M1', 'Модель');
            $sheet->setCellValue('N1', 'Дата контракту');
            $sheet->setCellValue('O1', 'Тип фінансування');
            //$sheet->setCellValue('L1', 'Дата гарантійного листа');
            //$sheet->setCellValue('M1', 'Дата угоди в банку');
            $sheet->setCellValue('P1', 'Статус контракту');
            $sheet->setCellValue('Q1', 'Митне очищення');
            $sheet->setCellValue('R1', 'Місцезнаходження авто');
            $sheet->setCellValue('S1', 'Дата прибуття до дилера');
            //$sheet->setCellValue('R1', '100% буде продано в поточному місяці');
            $sheet->setCellValue('T1', 'Запланована дата продажу в наступних місяцях');
            $sheet->setCellValue('U1', 'Фактична дата продажу');
            $sheet->setCellValue('V1', 'Продавець');
            $sheet->setCellValue('W1', 'Коментарі');
            $sheet->setCellValue('X1', 'Конкуренти, з якими клієнт порівнював');
            $sheet->setCellValue('Y1', 'Що було визначальним у виборі');
            $sheet->setCellValue('Z1', 'Ціна за прайсом, грн з ПДВ');
            $sheet->setCellValue('AA1', 'Ціна в рахунку-фактурі, грн з ПДВ');


            // Add some data
            $x = 2;
            //$contracts = array();

            $cars_list_options = CRM::get_booking_cars_helper('Su_carcomplId', true);

            if ($this->userdata['access_employees']) {
                $managers = CRM::get_booking_managers_helper();
            } else {
                $managers = CRM::get_booking_managers_helper($this->ion_auth->user()->row()->company);
            }

            $warehouse_picklist = CRM::picklist_options('new_warehouse');
            $ContractStatus_picklist = CRM::picklist_options('new_contractstatus');
            $VehicleStatus_picklist = CRM::picklist_options('new_vehiclestatus');



        $cars_by_parentmodel = CRM::get_booking_cars();
        $cars_by_parentmodel = $cars_by_parentmodel['Result']['Data'];



        $cars_by_parentmodels = array();
        foreach ($cars_by_parentmodel as $item_parentmodel) {
            $cars_by_parentmodels[$item_parentmodel['Su_carcomplId']] = $item_parentmodel['Su_name']; 
        }
//        var_dump($cars_by_parentmodels); die;

            foreach ($contracts as $contract) {
                //ALIGN CENTER
                $spreadsheet->getActiveSheet()->getStyle('A'.$x.':W'.$x)->applyFromArray($styleAlignColumnsArray);

                //ADDED FILTER BY PERIOD New_contractdate
                if ($contract_date_start != false && $contract_date_start > $contract['New_contractdate']) {
                    continue;
                }
                if ($contract_date_end != false && $contract_date_end < $contract['New_contractdate']) {
                    continue;
                }

                //ADDED FILTER BY SALE_DATE New_delivered
                if ($delivered_date_start != false && $delivered_date_start > $contract['New_delivered']) {
                    continue;
                }
                if ($delivered_date_end != false && $delivered_date_end < $contract['New_delivered']) {
                    continue;
                }
	/*
	* filter by SAR CAR NAME`
	*/			
				if ( !empty($car_name) ) {
					
					$sar_car_name = $cars_list_options[$contract['New_model_for_td']];
					if ($sar_car_name != $car_name) continue;
					
				}

                //TODO: TYPE SELECT FILTERS
                /*
                 отображается 2 списка:

“В роботі” (она открывается по-умолчанию)
записи, удовлетворяющие следующим критериям:
( ( New_vehiclestatus не равен 5 (відмова) && New_vehiclestatus не равен 6 (Продано) )

( New_delivered == null или (New_delivered.Year == Today.Year && New_delivered.Month == Today.Month) ) )
“Архів”, в которые будут попадать остальные записи */
                if ($type == 'full') { //“Вcі

                } elseif ($type == "archive") {

                    /*    echo "<pre>";
                        echo "<pre>";
                        var_dump(!(($contract['New_vehiclestatus'] == 5 || $contract['New_vehiclestatus'] == 6)
                            ||
                            ($contract['New_delivered'] != null &&
                                (date('n', $contract['New_delivered']) != date('n') || date('Y', $contract['New_delivered']) != date('Y'))  )));
                        var_dump(($contract['New_vehiclestatus'] != 5 && $contract['New_vehiclestatus'] != 6));
                        var_dump(($contract['New_delivered'] == null  ||
                            (date('n', $contract['New_delivered']) == date('n') && date('Y', $contract['New_delivered']) == date('Y')) ));
                        echo "</hr>";*/

                    if (
                        ($contract['New_vehiclestatus'] != 5 && $contract['New_vehiclestatus'] != 6)
                        &&
                        ($contract['New_delivered'] == null  ||
                            (date('n', $contract['New_delivered']) == date('n') && date('Y', $contract['New_delivered']) == date('Y')) )
                    ) {
                        continue;
                    }
                } else { //“В роботі
                    if (
                        ($contract['New_vehiclestatus'] == 5 || $contract['New_vehiclestatus'] == 6)
                        ||
                        ($contract['New_delivered'] != null &&
                            (date('n', $contract['New_delivered']) != date('n') || date('Y', $contract['New_delivered']) != date('Y'))  )
                    ) {
                        continue;
                    }
                }

                $dealer_filter = urldecode($dealer_filter);
                if ($dealer_filter != null && $contract['New_salon_name'] != $dealer_filter) {
                    continue;
                }

                /*if (
                    ($contract['New_vehiclestatus'] == 5 || $contract['New_vehiclestatus'] == 6)
                    ||
                    ($contract['New_delivered'] != null &&
                        (date('n', $contract['New_delivered']) != date('n') || date('Y', $contract['New_delivered']) != date('Y'))  )
                ) {
                    continue;
                }*/

                //Дилер
                $sheet->setCellValue('A'.$x, $contract['New_salon_name']);

                //Мобільний телефон
                //if ($contract['MobilePhone'] != null) { $contracts_list[$i][] = $contract['MobilePhone']; } else { $contracts_list[$i][] = ""; }

                $sheet->getStyle('C'.$x)->getNumberFormat()->setFormatCode('000000000000');
                $sheet->setCellValue('C'.$x, $contract['MobilePhone']);

                //Клієнт (ПІБ)
                //if ($contract['FullName'] != null) { $contracts_list[$i][] = $contract['FullName']; } else { $contracts_list[$i][] = ""; }
                $sheet->setCellValue('B'.$x, $contract['LastName'] . " " . $contract['FirstName'] . " " . $contract['MiddleName']);

                $sheet->setCellValue('D'.$x, $contract['EMailAddress1']);
                
                //TODO: Стать   Місто проживання    Згода на використання персональних даних
                if ($contract['New_sex']) {
		            $sheet->setCellValue('E'.$x, "Ж");
                } else {
		            $sheet->setCellValue('E'.$x, "Ч");
                }
                
                $sheet->setCellValue('F'.$x, $contract['Address1_city']);


                if ($contract['New_use_of_data']) {
		            $sheet->setCellValue('G'.$x, "Так");
                } else {
		            $sheet->setCellValue('G'.$x, "Ні");
		        }
                //$sheet->setCellValue('D'.$x, $contract['EMailAddress1']);

                //Склад/виробництво
                $sheet->setCellValue('H'.$x, @$warehouse_picklist[$contract['New_warehouse']]);
           /*     if($contract['New_warehouse'] == 1) {
                    $sheet->setCellValue('E'.$x, "Склад");

                }elseif($contract['New_warehouse'] == 2) {
                    $sheet->setCellValue('E'.$x, "Замовлення у виробництво");

                }*/

                //Номер кузова
                //if ($contract['Su_name'] != null) { $contracts_list[$i][] = $contract['Su_name']; } else { $contracts_list[$i][] = ""; }
                $sheet->setCellValue('I'.$x, $contract['New_vin']);

                //Номер замовлення у виробництво
                $sheet->setCellValue('J'.$x, $contract['New_order_name']);


                //Модель SAR
                $sheet->setCellValue('K'.$x,$cars_list_options[$contract['New_model_for_td']]);

                //New_modelversionid
                $sheet->setCellValue('L'.$x,$cars_by_parentmodels[$contract['New_modelversionid']]);

                //Модель new_Model
                //$vehicle = CRM::get_suvehicle_by_id($contract['New_vehicle_id']);
                //$vehicle = $vehicle['Result']['Data'];
                $sheet->setCellValue('M'.$x, $contract['New_model']);

                //Дата контракту new_ContractDate
                if ($contract['New_contractdate'] != null) { $sheet->setCellValue('N'.$x, date("d.m.Y",$contract['New_contractdate'])); }

                //Тип оплати  new_ContractStatus
                $sheet->setCellValue('O'.$x, $ContractStatus_picklist[$contract['New_contractstatus']]);



                //Дата гарантійного листа - new_GuaranteeLetter
                //if ($contract['New_guaranteeletter'] ) {
                //    $sheet->setCellValue('L'.$x, date("d.m.Y",$contract['New_guaranteeletter'])); }
                //$contracts_list[$i][] = $contract['06.05.2021'];

                //Дата угоди в банку - new_PaymentRemittance
                //if ($contract['New_paymentremittance'] ) {
                //    $sheet->setCellValue('M'.$x, date("d.m.Y",$contract['New_paymentremittance'])); }

                //Статус контракту
                $sheet->setCellValue('P'.$x, $VehicleStatus_picklist[$contract['New_vehiclestatus']]);
                /*if($contract['New_vehiclestatus'] == 1){
                    $sheet->setCellValue('M'.$x, 'Так');
                } elseif($contract['New_vehiclestatus'] == 2) {
                    $sheet->setCellValue('M'.$x, 'Ні');
                }*/

                //Митне очищення new_CustomsClearance 1 = Так  2 = Ні
                if ($contract['New_customsclearance'] == 1 ) {
                    $sheet->setCellValue('Q'.$x, 'Так');
                } else if ($contract['New_customsclearance'] == 2 ) {
                    $sheet->setCellValue('Q' . $x, 'Ні');
                }

                // Місцезнаходження авто - new_CarLocation
                /*1 = Дилер; 2 = Консигнація іншого дилера; 3 = Склад в Києві; 4 = Транзит дилерам; 5 = Міжнародний транзит; 6 = Замовлення у виробництв*/
                $CarLocationList = array(
                    '1' => "Дилер",
                    '2' => "Консигнація іншого дилера",
                    '3' => "Склад в Києві",
                    '4' => "Транзит дилерам",
                    '5' => "Міжнародний транзит",
                    '6' => "Замовлення у виробництв");
                $sheet->setCellValue('R'.$x, $CarLocationList[$contract['New_carlocation']]);

                //Дата прибуття до дилера - new_ArrivalToDealer
                if ($contract['New_arrivaltodealer'] ) { $sheet->setCellValue('S'.$x, date("d.m.Y",$contract['New_arrivaltodealer'])); }
                //$contracts_list[$i][] = $contract['06.05.2021'];

                // 100% буде продано в поточному місяці - new_ConfidentDelivery
                //if ($contract['New_confidentdelivery'] ) { $sheet->setCellValue('T'.$x, date("d.m.Y",$contract['New_confidentdelivery'])); }
                //$contracts_list[$i][] = $contract['06.05.2021'];

                //Запланована дата продажу в наступних місяцях - new_PostponedDelivery
                if ($contract['New_postponeddelivery'] ) { $sheet->setCellValue('T'.$x, date("d.m.Y",$contract['New_postponeddelivery'])); }
                //$contracts_list[$i][] = $contract['06.05.2021'];

                //Продано в поточному місяці - new_Delivered
                if ($contract['New_delivered'] ) { $sheet->setCellValue('U'.$x, date("d.m.Y",$contract['New_delivered'])); }
                //$contracts_list[$i][] = $contract['06.05.2021'];

                //Продавець - поле “Менеджер”
                if ($contract['New_web_user_id'] != null) {
                    //$web_user = CRM::get_webuser_by_id($contract['New_web_user_id']);
                    //$web_user = $web_user['Result']['Data'];

                    $sheet->setCellValue('V'.$x, $managers[$contract['New_web_user_id']]);
                }

                //Коментарі - new_Comments
                //$contracts_list[$i][] = $contract['New_comments'];
                $sheet->setCellValue('W'.$x, $contract['New_comments']);

                //Конкуренти, з якими клієнт порівнював
                //$contracts_list[$i][] = $contract['New_competitorcriteria'];
                $sheet->setCellValue('X'.$x, $contract['New_competitorcriteria']);

                //Що було визначальним у явиборі
                //$contracts_list[$i][] = $contract['New_competitornames'];


                //New_pricelist_price New_invoice_price
                $sheet->setCellValue('Z'.$x, $contract['New_pricelist_price']);
                $sheet->setCellValue('AA'.$x, $contract['New_invoice_price']);



                $x++;

            }

            // die;


            $writer = new Xlsx($spreadsheet);
            $writer->setIncludeCharts(true);

            $filename = 'contracts_export_'.date("d_m");

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output'); // download file
        }

    }

}