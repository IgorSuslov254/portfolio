<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CRM {

    public static $_organization = "citroen";
    public static $dealer_centers = array();
    //private static $CI = get_instance();

    private static $_api_url = 'https://webportal.crm.servicedesk.in.ua/api/';


    public function __construct()
    {
		$this->CI =& get_instance();
    }

    /*
    // --- GET SECTION ---
     * getting GET methods
    // --- GET SECTION ---
     */


        
    public static function get_booking_cars()
    {
        /*у нас есть метка в модели авто "Авто снят с производства". И если ее ставить, то модлеь должна отображаться на портале в карточке запроса по-прежнему, но в списке моделей для проставления доступности и коррективровки графиков их быть не должно. Но сейчас если я ставлю эту галочку, они не убираются сос писка
        странно что у нас не реализовано. Это для тех случаев (в ТЗ есть), если авто больше нет для ТД, но по нему на портале надо сохрпанить историю записей

То есть правильно понял снятая с производства модель не должна быть доступна в создании запроса, но должна сохранятся при редактировании?
        */
        self::$_organization;
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/cars');
        $CI->curl->http_header('Accept','application/json');
        $returnData = array();
        return json_decode($CI->curl->execute(), true);
        $data = json_decode($CI->curl->execute(), true);
        $returnData = $data['Result']['Data'];

        if($data['Count'] > 0) {
            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/cars/2');
            $CI->curl->http_header('Accept','application/json');

            $data = json_decode($CI->curl->execute(), true);

            $returnData = array_merge($returnData, $data['Result']['Data']);
        }

        return $returnData;
        //


    }
    /*
     * getting GET methods
     */
/*    public static function get_booking_cars_helper($key = 'Su_carcomplId', $include_out_of_production = true)
    {
        self::$_organization;
        $CI = &get_instance();

        $picklist = CRM::test_cars_picklist();
        $all_cars = CRM::get_booking_cars();

        $cars_list_options = array();

        $new_car_model = array();

        foreach ($picklist['Result']['Data'] as $pick) {

            if ($pick['AttributeName'] == 'new_car_model') {

                $new_car_model[$pick['AttributeValue']] = $pick['Value'];
            }
        }
        //sort($new_car_model);
        foreach ($all_cars['Result']['Data'] as $all_car) {
            if (!$include_out_of_production) {
                if (
                    isset($new_car_model[$all_car['New_car_model']]) &&
                    $all_car['New_new_model_cars_for_td'] == true &&
                    $all_car['New_out_of_production'] != true
                ) {
                    //if (isset($new_car_model[$all_car['New_car_model']]) && $all_car['New_new_model_cars_for_td'] == true && !in_array($new_car_model[$all_car['New_car_model']], $cars_list_options)) {
                    $cars_list_options[$all_car[$key]] = $new_car_model[$all_car['New_car_model']];
                }
            } else {
                if (isset($new_car_model[$all_car['New_car_model']]) && $all_car['New_new_model_cars_for_td'] == true) {
                    //if (isset($new_car_model[$all_car['New_car_model']]) && $all_car['New_new_model_cars_for_td'] == true && !in_array($new_car_model[$all_car['New_car_model']], $cars_list_options)) {
                    $cars_list_options[$all_car[$key]] = $new_car_model[$all_car['New_car_model']];
                }
            }
        }

        return $cars_list_options;
    }*/

    /*
     * getting GET methods
     */
    public static function get_booking_cars_helper($key = 'Su_carcomplId', $include_out_of_production = true)
    {
        self::$_organization;
        $CI = &get_instance();

        $picklist = CRM::test_cars_picklist();
        $all_cars = CRM::get_booking_cars();

/*
        if (!$CI->userdata['access_employees']) {
            $suvehicle_all = CRM::get_suvehicle_all($CI->user_company);
        } else {
            $suvehicle_all = CRM::get_suvehicle_all();
        }*/
        //$carcompl_suvehicle_helper = CRM::get_suvehicle_helper_keys_carcompl();

        $cars_list_options = array();
        $cars_list_temp = array();

        $new_car_model = array();

        foreach ($picklist['Result']['Data'] as $pick) {

            if ($pick['AttributeName'] == 'new_car_model') {

                $new_car_model[$pick['AttributeValue']] = $pick['Value'];
            }
        }
//vardump($new_car_model);
        //sort($new_car_model);
        foreach ($all_cars['Result']['Data'] as $all_car) {
            if (!$include_out_of_production) {
                if (
                    isset($new_car_model[$all_car['New_car_model']]) &&
                    $all_car['New_new_model_cars_for_td'] == true &&
                    $all_car['New_out_of_production'] != true &&
                    $all_car['New_td_id'] != null
                ) {
                    //$cars_list_options[$all_car['New_td_id']] = $new_car_model[$all_car['New_car_model']];
                    $cars_list_options[$all_car['New_td_id']] = $all_car['Su_name'];
                    $cars_list_temp[$all_car['New_td_id']] = $all_car[$key];
                }
            } else {

                if (
                    isset($new_car_model[$all_car['New_car_model']]) &&
                    $all_car['New_new_model_cars_for_td'] == true &&
                    $all_car['New_td_id'] != null
                ) {
                    //$cars_list_options[$all_car['New_td_id']] = $new_car_model[$all_car['New_car_model']];
                    $cars_list_options[$all_car['New_td_id']] = $all_car['Su_name'];
                    $cars_list_temp[$all_car['New_td_id']] = $all_car[$key];
                }
            }
        }
        //vardump($cars_list_options);

        ksort($cars_list_options);
        $cars_list_output = array();
        foreach ($cars_list_options as $list_key => $list_option) {
/*
            if(
                isset($carcompl_suvehicle_helper[$cars_list_temp[$list_key]]) &&
                count($carcompl_suvehicle_helper[$cars_list_temp[$list_key]]) > 1
            ) {
                for ($i = 0; $i < count($carcompl_suvehicle_helper[$cars_list_temp[$list_key]]); $i++) {
                    $cars_list_output[$cars_list_temp[$list_key] . "|" . $carcompl_suvehicle_helper[$cars_list_temp[$list_key]][$i]['Su_vehicleId']] = $list_option . "(" . ($i+1) . ")";
                }
            } else {
                $cars_list_output[$cars_list_temp[$list_key]] = $list_option;
            } */

            $cars_list_output[$cars_list_temp[$list_key]] = $list_option;
        }
        return $cars_list_output;
    }
    /*
     * getting GET methods
     */
    public static function get_booking_cars_helper_lcdv($key = 'New_lcdv')
    {
        self::$_organization;
        $CI = &get_instance();

        $picklist = CRM::test_cars_picklist();
        $all_cars = CRM::get_booking_cars();

        $cars_list_options = array();

        $new_car_model = array();

        foreach ($picklist['Result']['Data'] as $pick) {

            if ($pick['AttributeName'] == 'new_car_model') {

                $new_car_model[$pick['AttributeValue']] = $pick['Value'];
            }
        }
        //vardump($new_car_model);
        //sort($new_car_model);
        foreach ($all_cars['Result']['Data'] as $all_car) {
            if (isset($new_car_model[$all_car['New_car_model']]) && $all_car['New_new_model_cars_for_td'] == true) {
            //if (isset($new_car_model[$all_car['New_car_model']]) && $all_car['New_new_model_cars_for_td'] == true && !in_array($new_car_model[$all_car['New_car_model']], $cars_list_options)) {
                //$cars_list_options[$all_car[$key]] = $new_car_model[$all_car['New_car_model']];
                $cars_list_options[$all_car[$key]] = $all_car['Su_carcomplId'];
            }
        }

        return $cars_list_options;
    }

    public static function test_cars_picklist()
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/helper/picklistvalues/');
        $CI->curl->http_header('Accept','application/json');


        return json_decode($CI->curl->execute(), true);

    }



    /*
     * getting GET methods
     */
    public static function get_booking_cars_by_parentmodel($parentmodel)
    {
        self::$_organization;
        $CI = &get_instance();

        $all_cars = CRM::get_booking_cars();

//var_dump($parentmodel);
      
        $cars_list_output = array();
        foreach ($all_cars['Result']['Data'] as $all_car) {
            //var_dump($all_car['New_parentmodel']);
            if (
                $all_car['New_parentmodel'] == $parentmodel
            ) {
                $cars_list_output[] = $all_car;
            }
        }

        return $cars_list_output;
    }
   /* public static function picklist_options()
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/helper/picklistvalues/');
        $CI->curl->http_header('Accept','application/json');

        $CRM_DATA = json_decode($CI->curl->execute(), true);
        $CRM_DATA = $CRM_DATA['Result']['Data'];
        return $CRM_DATA;
    }*/

    public static function picklist_options($key = null, $need_keys = null, $EntityName = null)
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');

        if ($key != null) {
            $option_array = array();
            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/helper/picklistvalue/' . $key);
            $CI->curl->http_header('Accept','application/json');

            $CRM_DATA = json_decode($CI->curl->execute(), true);
            $CRM_DATA = $CRM_DATA['Result']['Data'];

            foreach ($CRM_DATA as $item) {
                if ($need_keys != null && is_array($need_keys)) {
                    if ($item['AttributeName'] == $key && in_array($item['AttributeValue'], $need_keys)) {
                        $option_array[$item['AttributeValue']] = $item['Value'];
                    }
                } elseif ($EntityName != null){
                    if ($item['AttributeName'] == $key && $item['EntityName'] == $EntityName) {
                        $option_array[$item['AttributeValue']] = $item['Value'];
                    }
                } else {
                    if(self::$_organization == 'peugeot' && $key == 'new_recordtype' && in_array($item['AttributeValue'], array(9,8,7,4,10))) {
                        continue;
                    }
                    if ($item['AttributeName'] == $key) {
                        $option_array[$item['AttributeValue']] = $item['Value'];
                    }
                }
            }

            return $option_array;
        } else {
            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/helper/picklistvalues/');
            $CI->curl->http_header('Accept','application/json');


            $CRM_DATA = json_decode($CI->curl->execute(), true);
            $CRM_DATA = $CRM_DATA['Result']['Data'];
            return $CRM_DATA;
        }

/*
        if ($key != null) {
            $option_array = array();
            foreach ($CRM_DATA as $item) {
                if ($need_keys != null && is_array($need_keys)) {
                    if ($item['AttributeName'] == $key && in_array($item['Value'], $need_keys)) {
                        $option_array[$item['AttributeValue']] = $item['Value'];
                    }
                } else {
                    if ($item['AttributeName'] == $key) {
                        $option_array[$item['AttributeValue']] = $item['Value'];
                    }
                }
            }
            return $option_array;
        } else {
            return $CRM_DATA;
        }*/
    }


    public static function get_booking_managers($dealerid = null, $brand = null)
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        if ($brand != null) {
	        if ($dealerid === null) {
	            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/' . $brand . '/webuser/users');
	        } else {
	            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/' . $brand . '/webuser/users?dealerid=' . $dealerid);
	        }
        } else {
	        if ($dealerid === null) {
	            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/webuser/users');
	        } else {
	            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/webuser/users?dealerid=' . $dealerid);
	        }
	    }
        $CI->curl->http_header('Accept','application/json');


        return json_decode($CI->curl->execute(), true);

    }

    public static function get_booking_managers_helper($dealerid = null)
    {
        $CI = &get_instance();
        if ($dealerid === null) {
            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/webuser/users');
        } else {
            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/webuser/users?dealerid=' . $dealerid);
        }
        $CI->curl->http_header('Accept','application/json');

        $managers_full = json_decode($CI->curl->execute(), true);
        $managers_full = $managers_full['Result']['Data'];

        $managers = array();
        foreach ($managers_full as $manager_item) {
            $managers[$manager_item['New_web_userId']] = $manager_item['New_lastname'] . " " . $manager_item['New_name'];
        }
        return $managers;
    }

    /* ACCOUNTS - Section in API */

    public static function get_dealer_centers()
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/accounts/dealers/');
        $CI->curl->http_header('Accept','application/json');


        return json_decode($CI->curl->execute(), true);

    }

    public static function get_dealer_account_id( $New_dealer = null )
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/accounts/account/'.$New_dealer);
        $CI->curl->http_header('Accept','application/json');


        return json_decode($CI->curl->execute(), true);

    }

    public static function get_dealer_centers_helper($company_id = null)
    {
        $dealer_centers = self::get_dealer_centers();
        $dealer_centers_data = $dealer_centers['Result']['Data'];
        $dealer_centers_options = array();


        foreach ($dealer_centers_data as $dealer_center) {
            if ($company_id != null) {
                if ($company_id == $dealer_center['AccountId']) {
                    $dealer_centers_options[$dealer_center['AccountId']] = $dealer_center['Name'];
                }
            } else {
                $dealer_centers_options[$dealer_center['AccountId']] = $dealer_center['Name'];
            }
        }
        log_message('info'," get_dealer_centers_helper: " . json_encode($dealer_centers_options));
        return $dealer_centers_options;
    }

    public static function get_dealer_centers_helper_rrdi($company_id = null)
    {
        $dealer_centers = self::get_dealer_centers();
        $dealer_centers_data = $dealer_centers['Result']['Data'];
        $dealer_centers_options = array();

        foreach ($dealer_centers_data as $dealer_center) {
            if ($company_id != null) {
                if ($company_id == $dealer_center['New_rrdi']) {
                    $dealer_centers_options[$dealer_center['New_rrdi']] = $dealer_center['AccountId'];
                }
            } else {
                $dealer_centers_options[$dealer_center['New_rrdi']] = $dealer_center['AccountId'];
            }
        }

        return $dealer_centers_options;
    }

    public static function get_dealer_centers_helper_rrdi_getname($company_id = null)
    {
        $dealer_centers = self::get_dealer_centers();
        $dealer_centers_data = $dealer_centers['Result']['Data'];
        $dealer_centers_options = array();


        foreach ($dealer_centers_data as $dealer_center) {
            if ($company_id != null) {
                if ($company_id == $dealer_center['New_rrdi']) {
                    $dealer_centers_options[$dealer_center['New_rrdi']] = $dealer_center['Name'];
                }
            } else {
                $dealer_centers_options[$dealer_center['New_rrdi']] = $dealer_center['Name'];
            }
        }

        return $dealer_centers_options;
    }

    public static function get_dealer_centers_helper_name_by_rrdi($company_id = null)
    {
        $dealer_centers = self::get_dealer_centers();
        $dealer_centers_data = $dealer_centers['Result']['Data'];
        $dealer_centers_options = array();


        foreach ($dealer_centers_data as $dealer_center) {
            if ($company_id != null) {
                if ($company_id == $dealer_center['AccountId']) {
                    $dealer_centers_options[$dealer_center['AccountId']] = $dealer_center['New_rrdi'];
                }
            } else {
                $dealer_centers_options[$dealer_center['AccountId']] = $dealer_center['New_rrdi'];
            }
        }

        return $dealer_centers_options;
    }

    public static function get_servicestations()
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/accounts/servicestations/');
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    //https://trello.com/c/YxQsk8GE
    public static function get_servicepartners()
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/accounts/servicepartners/');
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    //New_partner_bank(isPartnerBank) = null -> true/false
    public static function get_banks($CustomerTypeCode = null, $isPartnerBank = null)
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/accounts/banks/');
        $CI->curl->http_header('Accept','application/json');

        $banks_full = json_decode($CI->curl->execute(), true);
        $banks_full = $banks_full['Result']['Data'];

        $banks = array();
        foreach ($banks_full as $banks_item) {
            if ($CustomerTypeCode != null) {         
                if ($banks_item['CustomerTypeCode'] == $CustomerTypeCode) {
                	if ($isPartnerBank != null) {
	                	if ($banks_item['New_partner_bank'] == $isPartnerBank) {
	                    	$banks[$banks_item['AccountId']] = $banks_item['Name'];
	                	}
                	} else {
                	    $banks[$banks_item['AccountId']] = $banks_item['Name'];
                	}
                }
            } else {
                $banks[$banks_item['AccountId']] = $banks_item['Name'];
            }
        }
        return $banks;
    }
    /* ACCOUNTS - Section in API */


    public static function get_testdrives($dealerid = null, $id = null, $userid = null)
    {
        //$response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/';
        $response_link = "";
        if($dealerid != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/calendar/dealer/'.$dealerid;
        } elseif($id != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/'.$id;
        } elseif($userid != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/calendar/user/'.$userid;
        }
        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    }


    // --- REQUESTS SECTION ---
    public static function get_special_registrations($page = null)
    {
        $special_registrations = array();

        for ($i = 0; true; $i = $i + 10) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/special/registrations/' . $i;
            $CI = &get_instance();
            $CI->curl->create($response_link);
            $CI->curl->http_header('Accept', 'application/json');

            $data = json_decode($CI->curl->execute(), true);

            if (empty($data['Result']['Data'])) {
                break;
            }

            foreach ($data['Result']['Data'] as $row) {
                $special_registrations[] = $row;
            }
        }
        return $special_registrations;
    }

    /*
     * REGISTRATIONS OLD METHODS (GET)
     *
get_registrations_all($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', )
get_registrations_new($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
get_registrations_inwork($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
get_registrations_withouttd($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
get_registrations_stopwork($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
get_registrations_sales($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
get_registrations_pcu($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
    */
/*
     * REGISTRATIONS OLD METHODS (GET)
     *
     * public static function get_registrations_all($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', $pcu_filter_dealer = null)
    {
        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all?page=' . $page . '?dealerid=' . $dealerid; }
        }

        // Date Period
        if ($period_start != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'startDate='.$period_start;
        }
        if ($period_end != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'endDate='.$period_end;
        }

        //Dropdown dealer filter in PCU Requests
        if ($pcu_filter_dealer != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'dealerid='.$pcu_filter_dealer;
        }

        //Ordering DataTable in CRM API
        $order_column_names = array(
            0 => "AccountId",
            1 => "New_recordtype",
            2 => "LastName",
            3 => "FirstName",
            4 => "MobilePhone",
            5 => "EmailAddressLead",
            6 => "Su_carcomplId",
            7 => "New_process_status",
            8 => "New_campaign_data_final",
            9 => "New_web_userId",
            10 => "CreatedOn",
            11 => "New_confirmation_presence_td",
            12 => "New_channel",
        );
        //
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $response_link .= "&columnName=" . $order_column_names[$order_column] . "&sortType=" . strtoupper($order_type);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_registrations_new($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
    {
        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/new'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/new?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/new?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/new?page=' . $page . '?dealerid=' . $dealerid; }
        }

        // Date Period
        if ($period_start != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'startDate='.$period_start;
        }
        if ($period_end != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'endDate='.$period_end;
        }

        //Ordering DataTable in CRM API
        $order_column_names = array(
            0 => "AccountId",
            1 => "New_recordtype",
            2 => "LastName",
            3 => "FirstName",
            4 => "MobilePhone",
            5 => "EmailAddressLead",
            6 => "Su_carcomplId",
            7 => "New_process_status",
            8 => "New_campaign_data_final",
            9 => "New_web_userId",
            10 => "CreatedOn",
            11 => "New_confirmation_presence_td",
            12 => "New_channel",
        );
        //
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $response_link .= "&columnName=" . $order_column_names[$order_column] . "&sortType=" . strtoupper($order_type);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_registrations_inwork($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
    {
        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/inwork'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/inwork?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/inwork?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/inwork?page=' . $page . '?dealerid=' . $dealerid; }
        }

        // Date Period
        if ($period_start != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'startDate='.$period_start;
        }
        if ($period_end != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'endDate='.$period_end;
        }

        //Ordering DataTable in CRM API
        $order_column_names = array(
            0 => "AccountId",
            1 => "New_recordtype",
            2 => "LastName",
            3 => "FirstName",
            4 => "MobilePhone",
            5 => "EmailAddressLead",
            6 => "Su_carcomplId",
            7 => "New_process_status",
            8 => "New_campaign_data_final",
            9 => "New_web_userId",
            10 => "CreatedOn",
            11 => "New_confirmation_presence_td",
            12 => "New_channel",
        );
        //
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $response_link .= "&columnName=" . $order_column_names[$order_column] . "&sortType=" . strtoupper($order_type);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_registrations_withouttd($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
    {
        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/withouttd'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/withouttd?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/withouttd?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/withouttd?page=' . $page . '?dealerid=' . $dealerid; }
        }

        // Date Period
        if ($period_start != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'startDate='.$period_start;
        }
        if ($period_end != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'endDate='.$period_end;
        }

        //Ordering DataTable in CRM API
        $order_column_names = array(
            0 => "AccountId",
            1 => "New_recordtype",
            2 => "LastName",
            3 => "FirstName",
            4 => "MobilePhone",
            5 => "EmailAddressLead",
            6 => "Su_carcomplId",
            7 => "New_process_status",
            8 => "New_campaign_data_final",
            9 => "New_web_userId",
            10 => "CreatedOn",
            11 => "New_confirmation_presence_td",
            12 => "New_channel",
        );
        //
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $response_link .= "&columnName=" . $order_column_names[$order_column] . "&sortType=" . strtoupper($order_type);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_registrations_stopwork($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
    {
        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/stopwork'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/stopwork?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/stopwork?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/stopwork?page=' . $page . '?dealerid=' . $dealerid; }
        }

        // Date Period
        if ($period_start != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'startDate='.$period_start;
        }
        if ($period_end != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'endDate='.$period_end;
        }

        //Ordering DataTable in CRM API
        $order_column_names = array(
            0 => "AccountId",
            1 => "New_recordtype",
            2 => "LastName",
            3 => "FirstName",
            4 => "MobilePhone",
            5 => "EmailAddressLead",
            6 => "Su_carcomplId",
            7 => "New_process_status",
            8 => "New_campaign_data_final",
            9 => "New_web_userId",
            10 => "CreatedOn",
            11 => "New_confirmation_presence_td",
            12 => "New_channel",
        );
        //
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $response_link .= "&columnName=" . $order_column_names[$order_column] . "&sortType=" . strtoupper($order_type);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_registrations_sales($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
    {
        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/sales'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/sales?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/sales?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/sales?page=' . $page . '?dealerid=' . $dealerid; }
        }

        // Date Period
        if ($period_start != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'startDate='.$period_start;
        }
        if ($period_end != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'endDate='.$period_end;
        }

        //Ordering DataTable in CRM API
        $order_column_names = array(
            0 => "AccountId",
            1 => "New_recordtype",
            2 => "LastName",
            3 => "FirstName",
            4 => "MobilePhone",
            5 => "EmailAddressLead",
            6 => "Su_carcomplId",
            7 => "New_process_status",
            8 => "New_campaign_data_final",
            9 => "New_web_userId",
            10 => "CreatedOn",
            11 => "New_confirmation_presence_td",
            12 => "New_channel",
        );
        //
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $response_link .= "&columnName=" . $order_column_names[$order_column] . "&sortType=" . strtoupper($order_type);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_registrations_pcu($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc')
    {
        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/pcu'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/pcu?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/pcu?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/pcu?page=' . $page . '?dealerid=' . $dealerid; }
        }

        // Date Period
        if ($period_start != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'startDate='.$period_start;
        }
        if ($period_end != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'endDate='.$period_end;
        }

        //Ordering DataTable in CRM API
        $order_column_names = array(
            0 => "AccountId",
            1 => "New_recordtype",
            2 => "LastName",
            3 => "FirstName",
            4 => "MobilePhone",
            5 => "EmailAddressLead",
            6 => "Su_carcomplId",
            7 => "New_process_status",
            8 => "New_campaign_data_final",
            9 => "New_web_userId",
            10 => "CreatedOn",
            11 => "New_confirmation_presence_td",
            12 => "New_channel",
        );
        //
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $response_link .= "&columnName=" . $order_column_names[$order_column] . "&sortType=" . strtoupper($order_type);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
*/

    public static function get_registrations_all($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', $pcu_filter_dealer = null, $period_td_start = null, $period_td_end = null)
    {
        $CI = &get_instance();
        //Ordering DataTable in CRM API
        $order_column_names = array(
        //    0 => "AccountId", 1 => "New_recordtype", 2 => "LastName", 3 => "FirstName", 4 => "MobilePhone", 5 => "EmailAddressLead", 6 => "Su_carcomplId", 7 => "New_process_status", 8 => "New_campaign_data_final", 9 => "New_web_user", 10 => "CreatedOn", 11 => "New_confirmation_presence_td", 12 => "New_channel",
            0 => "AccountId", 2 => "New_recordtype", 3 => "LastName", 4 => "Su_carcomplId", 5 => "New_process_status", 6 => "New_campaign_data_final", 8 => "New_web_user", 9 => "CreatedOn", 7 => "New_confirmation_presence_td", 2 => "New_channel", 16 => "New_fb",
        );

        $columnName = 'CreatedOn';
        $sortType = 'DESC';
     //  if ($order_column != null && isset($order_column_names[$order_column])) {
     //      $columnName = $order_column_names[$order_column];
     //      $sortType = strtoupper($order_type);
     //  }

        $columnName = 'CreatedOn';
        $sortType = '';
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $columnName = $order_column_names[$order_column];
            $sortType = strtoupper($order_type);
        }



        $post_data = array(
            "New_recordtype" => [], "New_car_model" => [], "New_process_status" => [], "New_process_status" => [], "New_web_user" => [], "New_confirmation_presence_td" => null, "New_channel" => [],
            "SortColumn" => $columnName, "SortType" => $sortType
        );
        if ($period_start != null) {
            $post_data["StartDate"] = $period_start;
        }
        if ($period_end != null) {
            $post_data["EndDate"] = $period_end;
        }

        if ($period_td_start != null) {
            $post_data["CampaignDataFinalStartDate"] = $period_td_start;
        }
        if ($period_td_end != null) {
            $post_data["CampaignDataFinalEndDate"] = $period_td_end;
        }

        $channel_picklist = CRM::picklist_options('new_channel');
        $cars_list_options = CRM::picklist_options('new_car_model');
        $status_picklist = CRM::picklist_options('new_process_status');
        $recordtype_picklist = CRM::picklist_options('new_recordtype');
        $confirmation_presence_td_picklist = CRM::picklist_options('new_confirmation_presence_td');
        //$cars_list_options = CRM::get_booking_cars_helper('New_car_model');
        $all_managers = array();
        if ($CI->userdata['access_employees'] || $pcu_filter_dealer != null) {$CI->db->select('*')->from("users");            $all_managers = $CI->db->get()->result_array();        }
        elseif($CI->userdata['access_employees'] == false && $CI->user_company != null) {$CI->db->select('*')->where('company', $CI->user_company)->from("users");         $all_managers = $CI->db->get()->result_array();}

        $managers_options = array();
        foreach ($all_managers as $manager) { if ($manager['has_acces_tdrive'] && $manager['StateCode'] != 1) {            $managers_options[$manager['web_userid']] = $manager['first_name'] . " " . $manager['last_name']; }        }
        if ($CI->input->get("pcu_filter_car")) {
            foreach ($CI->input->get("pcu_filter_car") as $car_key => $car) {
                if ($pcu_filter_car_search = array_search($car, $cars_list_options)) {
                    $pcu_filter_car[] = $pcu_filter_car_search;
                }
            }
            $post_data['New_car_model'] = $pcu_filter_car;
        }
        $pcu_filter_status = array();

        if ($CI->input->get("pcu_filter_status")) {
            //foreach ($CI->input->get("pcu_filter_status") as $status_key => $status) {if ($pcu_filter_status_search = array_search($status, $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             }} $post_data['New_process_status'] = $pcu_filter_status;
            if ($pcu_filter_status_search = array_search($CI->input->get("pcu_filter_status"), $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             } $post_data['New_process_status'] = $pcu_filter_status;
        }


        if ($CI->input->get("pcu_filter_manager")) {
            foreach ($CI->input->get("pcu_filter_manager") as $manager_key => $manager) {if ($pcu_filter_manager_search = array_search($manager, $managers_options)) {                 $pcu_filter_manager[] = $pcu_filter_manager_search;             }}$post_data['New_web_user'] = $pcu_filter_manager;
        }
        if ($CI->input->get("pcu_filter_channel")) { foreach ($CI->input->get("pcu_filter_channel") as $channel) {if ($pcu_filter_channel_search = array_search($channel, $channel_picklist)) {$pcu_filter_channel[] = $pcu_filter_channel_search;}}
            $post_data['New_channel'] = $pcu_filter_channel;
        }
        if ($CI->input->get("pcu_filter_recordtype")) { foreach ($CI->input->get("pcu_filter_recordtype") as $recordtype) {if ($pcu_filter_recordtype_search = array_search($recordtype, $recordtype_picklist)) {$pcu_filter_recordtype[] = $pcu_filter_recordtype_search;}}
            $post_data['New_recordtype'] = $pcu_filter_recordtype;
        }

        if ($CI->input->get("pcu_filter_confirmation_presence_td")) {
            $post_data['New_confirmation_presence_td'] = $CI->input->get("pcu_filter_confirmation_presence_td");
        } else {
            $post_data['New_confirmation_presence_td'] = null;
        }



        /*if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all?page=' . $page . '?dealerid=' . $dealerid; }
        }
        */
        //$response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all';
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/all';
        if ($page !== null) { $post_data["Page"] = $page; }
        if ($dealerid != null) { $post_data["DealerId"] = $dealerid; }
        if ($pcu_filter_dealer != null) {  $post_data["DealerId"] = $pcu_filter_dealer;}
        if ($CI->input->get('search[value]') != null) {  $post_data["SearchString"] = $CI->input->get('search[value]');}

        //vardump($response_link);
        //vardump(json_encode($post_data));

        //Dropdown dealer filter in PCU Requests
//        if ($pcu_filter_dealer != null) { $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'dealerid='.$pcu_filter_dealer; }


        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');
        $CI->curl->post(json_encode($post_data));
        

        log_message('info', json_encode($post_data));

        $returnData = json_decode($CI->curl->execute(), true);

        return $returnData;
    }


    public static function get_registrations_new($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', $pcu_filter_dealer = null, $period_td_start = null, $period_td_end = null)
    {
        $CI = &get_instance();
        //Ordering DataTable in CRM API
        $order_column_names = array(
        //    0 => "AccountId", 1 => "New_recordtype", 2 => "LastName", 3 => "FirstName", 4 => "MobilePhone", 5 => "EmailAddressLead", 6 => "Su_carcomplId", 7 => "New_process_status", 8 => "New_campaign_data_final", 9 => "New_web_user", 10 => "CreatedOn", 11 => "New_confirmation_presence_td", 12 => "New_channel",
            0 => "AccountId", 2 => "New_recordtype", 3 => "LastName", 4 => "Su_carcomplId", 5 => "New_process_status", 6 => "New_campaign_data_final", 8 => "New_web_user", 9 => "CreatedOn", 7 => "New_confirmation_presence_td", 2 => "New_channel", 16 => "New_fb",
        );

        $columnName = 'CreatedOn';
        $sortType = '';
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $columnName = $order_column_names[$order_column];
            $sortType = strtoupper($order_type);
        }

        $post_data = array(
            "New_recordtype" => [], "New_car_model" => [], "New_process_status" => [], "New_web_user" => [], "New_confirmation_presence_td" => null, "New_channel" => [],
            "SortColumn" => $columnName, "SortType" => $sortType
        );

        if ($period_start != null) {
            $post_data["StartDate"] = $period_start;
        }
        if ($period_end != null) {
            $post_data["EndDate"] = $period_end;
        }
        
        if ($period_td_start != null) {
            $post_data["CampaignDataFinalStartDate"] = $period_td_start;
        }
        if ($period_td_end != null) {
            $post_data["CampaignDataFinalEndDate"] = $period_td_end;
        }


        $channel_picklist = CRM::picklist_options('new_channel');
        $cars_list_options = CRM::picklist_options('new_car_model');
        $status_picklist = CRM::picklist_options('new_process_status');

        $recordtype_picklist = CRM::picklist_options('new_recordtype');
        $confirmation_presence_td_picklist = CRM::picklist_options('new_confirmation_presence_td');

        $all_managers = array();
        if ($CI->userdata['access_employees'] || $pcu_filter_dealer != null) {$CI->db->select('*')->from("users");            $all_managers = $CI->db->get()->result_array();        }
        elseif($CI->userdata['access_employees'] == false && $CI->user_company != null) {$CI->db->select('*')->where('company', $CI->user_company)->from("users");         $all_managers = $CI->db->get()->result_array();}

        $managers_options = array();
        foreach ($all_managers as $manager) { if ($manager['has_acces_tdrive'] && $manager['StateCode'] != 1) {            $managers_options[$manager['web_userid']] = $manager['first_name'] . " " . $manager['last_name']; }        }
        if ($CI->input->get("pcu_filter_car")) {            foreach ($CI->input->get("pcu_filter_car") as $car_key => $car) {if ($pcu_filter_car_search = array_search($car, $cars_list_options)) { $pcu_filter_car[] = $pcu_filter_car_search; }}
            $post_data['New_car_model'] = $pcu_filter_car;
        }

        $pcu_filter_status = array();
        if ($CI->input->get("pcu_filter_status")) {
            //foreach ($CI->input->get("pcu_filter_status") as $status_key => $status) {if ($pcu_filter_status_search = array_search($status, $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             }} $post_data['New_process_status'] = $pcu_filter_status;
            if ($pcu_filter_status_search = array_search($CI->input->get("pcu_filter_status"), $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             } $post_data['New_process_status'] = $pcu_filter_status;
        }

        if ($CI->input->get("pcu_filter_manager")) {
            foreach ($CI->input->get("pcu_filter_manager") as $manager_key => $manager) {if ($pcu_filter_manager_search = array_search($manager, $managers_options)) {                 $pcu_filter_manager[] = $pcu_filter_manager_search;             }}$post_data['New_web_user'] = $pcu_filter_manager;
        }
        if ($CI->input->get("pcu_filter_channel")) { foreach ($CI->input->get("pcu_filter_channel") as $channel) {if ($pcu_filter_channel_search = array_search($channel, $channel_picklist)) {$pcu_filter_channel[] = $pcu_filter_channel_search;}}
            $post_data['New_channel'] = $pcu_filter_channel;
        }

        if ($CI->input->get("pcu_filter_recordtype")) { foreach ($CI->input->get("pcu_filter_recordtype") as $recordtype) {if ($pcu_filter_recordtype_search = array_search($recordtype, $recordtype_picklist)) {$pcu_filter_recordtype[] = $pcu_filter_recordtype_search;}}
            $post_data['New_recordtype'] = $pcu_filter_recordtype;
        }
        
        if ($CI->input->get("pcu_filter_confirmation_presence_td")) {
            $post_data['New_confirmation_presence_td'] = $CI->input->get("pcu_filter_confirmation_presence_td");
        } else {
            $post_data['New_confirmation_presence_td'] = null;
        }

/*
        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/new'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/new?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/new?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/new?page=' . $page . '?dealerid=' . $dealerid; }
        }*/

        //Dropdown dealer filter in PCU Requests
/*        if ($pcu_filter_dealer != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'dealerid='.$pcu_filter_dealer;
        }*/
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/new';
        if ($page !== null) { $post_data["Page"] = $page; }
        if ($dealerid != null) { $post_data["DealerId"] = $dealerid; }
        if ($pcu_filter_dealer != null) {  $post_data["DealerId"] = $pcu_filter_dealer;}
        if ($CI->input->get('search[value]') != null) {  $post_data["SearchString"] = $CI->input->get('search[value]');}


        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        var_dump(json_encode($post_data)); die;
        
        $CI->curl->post(json_encode($post_data));
        return json_decode($CI->curl->execute(), true);
    }
    public static function get_registrations_inwork($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', $pcu_filter_dealer = null, $period_td_start = null, $period_td_end = null)
    {
        $CI = &get_instance();
        //Ordering DataTable in CRM API
        $order_column_names = array(
        //    0 => "AccountId", 1 => "New_recordtype", 2 => "LastName", 3 => "FirstName", 4 => "MobilePhone", 5 => "EmailAddressLead", 6 => "Su_carcomplId", 7 => "New_process_status", 8 => "New_campaign_data_final", 9 => "New_web_user", 10 => "CreatedOn", 11 => "New_confirmation_presence_td", 12 => "New_channel",
            0 => "AccountId", 2 => "New_recordtype", 3 => "LastName", 4 => "Su_carcomplId", 5 => "New_process_status", 6 => "New_campaign_data_final", 8 => "New_web_user", 9 => "CreatedOn", 7 => "New_confirmation_presence_td", 2 => "New_channel", 16 => "New_fb",
        );

        $columnName = 'CreatedOn';
        $sortType = '';
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $columnName = $order_column_names[$order_column];
            $sortType = strtoupper($order_type);
        }
        $post_data = array(
            "New_recordtype" => [], "New_car_model" => [], "New_process_status" => [], "New_web_user" => [], "New_confirmation_presence_td" => null, "New_channel" => [],
            "SortColumn" => $columnName, "SortType" => $sortType
        );

        if ($period_start != null) {
            $post_data["StartDate"] = $period_start;
        }
        if ($period_end != null) {
            $post_data["EndDate"] = $period_end;
        }
        
        if ($period_td_start != null) {
            $post_data["CampaignDataFinalStartDate"] = $period_td_start;
        }
        if ($period_td_end != null) {
            $post_data["CampaignDataFinalEndDate"] = $period_td_end;
        }
        

        $channel_picklist = CRM::picklist_options('new_channel');
        $cars_list_options = CRM::picklist_options('new_car_model');
        $status_picklist = CRM::picklist_options('new_process_status');

        $recordtype_picklist = CRM::picklist_options('new_recordtype');
        $confirmation_presence_td_picklist = CRM::picklist_options('new_confirmation_presence_td');

        $all_managers = array();
        if ($CI->userdata['access_employees'] || $pcu_filter_dealer != null) {$CI->db->select('*')->from("users");            $all_managers = $CI->db->get()->result_array();        }
        elseif($CI->userdata['access_employees'] == false && $CI->user_company != null) {$CI->db->select('*')->where('company', $CI->user_company)->from("users");         $all_managers = $CI->db->get()->result_array();}

        $managers_options = array();
        foreach ($all_managers as $manager) { if ($manager['has_acces_tdrive'] && $manager['StateCode'] != 1) {            $managers_options[$manager['web_userid']] = $manager['first_name'] . " " . $manager['last_name']; }        }
        if ($CI->input->get("pcu_filter_car")) {            foreach ($CI->input->get("pcu_filter_car") as $car_key => $car) {if ($pcu_filter_car_search = array_search($car, $cars_list_options)) { $pcu_filter_car[] = $pcu_filter_car_search; }}
            $post_data['New_car_model'] = $pcu_filter_car;
        }

        $pcu_filter_status = array();
        if ($CI->input->get("pcu_filter_status")) {
            //foreach ($CI->input->get("pcu_filter_status") as $status_key => $status) {if ($pcu_filter_status_search = array_search($status, $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             }} $post_data['New_process_status'] = $pcu_filter_status;
            if ($pcu_filter_status_search = array_search($CI->input->get("pcu_filter_status"), $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             } $post_data['New_process_status'] = $pcu_filter_status;
        }

        if ($CI->input->get("pcu_filter_manager")) {
            foreach ($CI->input->get("pcu_filter_manager") as $manager_key => $manager) {if ($pcu_filter_manager_search = array_search($manager, $managers_options)) {                 $pcu_filter_manager[] = $pcu_filter_manager_search;             }}$post_data['New_web_user'] = $pcu_filter_manager;
        }
        if ($CI->input->get("pcu_filter_channel")) { foreach ($CI->input->get("pcu_filter_channel") as $channel) {if ($pcu_filter_channel_search = array_search($channel, $channel_picklist)) {$pcu_filter_channel[] = $pcu_filter_channel_search;}}
            $post_data['New_channel'] = $pcu_filter_channel;
        }

        if ($CI->input->get("pcu_filter_recordtype")) { foreach ($CI->input->get("pcu_filter_recordtype") as $recordtype) {if ($pcu_filter_recordtype_search = array_search($recordtype, $recordtype_picklist)) {$pcu_filter_recordtype[] = $pcu_filter_recordtype_search;}}
            $post_data['New_recordtype'] = $pcu_filter_recordtype;
        }
        
        if ($CI->input->get("pcu_filter_confirmation_presence_td")) {
            $post_data['New_confirmation_presence_td'] = $CI->input->get("pcu_filter_confirmation_presence_td");
        } else {
            $post_data['New_confirmation_presence_td'] = null;
        }


/*        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/inwork'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/inwork?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/inwork?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/inwork?page=' . $page . '?dealerid=' . $dealerid; }
        }

        //Dropdown dealer filter in PCU Requests
        if ($pcu_filter_dealer != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'dealerid='.$pcu_filter_dealer;
        }*/
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/inwork';
        if ($page !== null) { $post_data["Page"] = $page; }
        if ($dealerid != null) { $post_data["DealerId"] = $dealerid; }
        if ($pcu_filter_dealer != null) {  $post_data["DealerId"] = $pcu_filter_dealer;}
        if ($CI->input->get('search[value]') != null) {  $post_data["SearchString"] = $CI->input->get('search[value]');}


        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));
        return json_decode($CI->curl->execute(), true);
    }
    public static function get_registrations_withouttd($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', $pcu_filter_dealer = null, $period_td_start = null, $period_td_end = null)
    {
        $CI = &get_instance();
        //Ordering DataTable in CRM API
        $order_column_names = array(
        //    0 => "AccountId", 1 => "New_recordtype", 2 => "LastName", 3 => "FirstName", 4 => "MobilePhone", 5 => "EmailAddressLead", 6 => "Su_carcomplId", 7 => "New_process_status", 8 => "New_campaign_data_final", 9 => "New_web_user", 10 => "CreatedOn", 11 => "New_confirmation_presence_td", 12 => "New_channel",
            0 => "AccountId", 2 => "New_recordtype", 3 => "LastName", 4 => "Su_carcomplId", 5 => "New_process_status", 6 => "New_campaign_data_final", 8 => "New_web_user", 9 => "CreatedOn", 7 => "New_confirmation_presence_td", 2 => "New_channel", 16 => "New_fb",
        );

        $columnName = 'CreatedOn';
        $sortType = '';
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $columnName = $order_column_names[$order_column];
            $sortType = strtoupper($order_type);
        }

        $post_data = array(
            "New_recordtype" => [], "New_car_model" => [], "New_process_status" => [], "New_web_user" => [], "New_confirmation_presence_td" => null, "New_channel" => [],
            "SortColumn" => $columnName, "SortType" => $sortType
        );

        if ($period_start != null) {
            $post_data["StartDate"] = $period_start;
        }
        if ($period_end != null) {
            $post_data["EndDate"] = $period_end;
        }
        
        if ($period_td_start != null) {
            $post_data["CampaignDataFinalStartDate"] = $period_td_start;
        }
        if ($period_td_end != null) {
            $post_data["CampaignDataFinalEndDate"] = $period_td_end;
        }

        $channel_picklist = CRM::picklist_options('new_channel');
        $cars_list_options = CRM::picklist_options('new_car_model');
        $status_picklist = CRM::picklist_options('new_process_status');

        $recordtype_picklist = CRM::picklist_options('new_recordtype');
        $confirmation_presence_td_picklist = CRM::picklist_options('new_confirmation_presence_td');

        $all_managers = array();
        if ($CI->userdata['access_employees'] || $pcu_filter_dealer != null) {$CI->db->select('*')->from("users");            $all_managers = $CI->db->get()->result_array();        }
        elseif($CI->userdata['access_employees'] == false && $CI->user_company != null) {$CI->db->select('*')->where('company', $CI->user_company)->from("users");         $all_managers = $CI->db->get()->result_array();}

        $managers_options = array();
        foreach ($all_managers as $manager) { if ($manager['has_acces_tdrive'] && $manager['StateCode'] != 1) {            $managers_options[$manager['web_userid']] = $manager['first_name'] . " " . $manager['last_name']; }        }
        if ($CI->input->get("pcu_filter_car")) {            foreach ($CI->input->get("pcu_filter_car") as $car_key => $car) {if ($pcu_filter_car_search = array_search($car, $cars_list_options)) { $pcu_filter_car[] = $pcu_filter_car_search; }}
            $post_data['New_car_model'] = $pcu_filter_car;
        }

        $pcu_filter_status = array();
        if ($CI->input->get("pcu_filter_status")) {
            //foreach ($CI->input->get("pcu_filter_status") as $status_key => $status) {if ($pcu_filter_status_search = array_search($status, $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             }} $post_data['New_process_status'] = $pcu_filter_status;
            if ($pcu_filter_status_search = array_search($CI->input->get("pcu_filter_status"), $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             } $post_data['New_process_status'] = $pcu_filter_status;
        }

        if ($CI->input->get("pcu_filter_manager")) {
            foreach ($CI->input->get("pcu_filter_manager") as $manager_key => $manager) {if ($pcu_filter_manager_search = array_search($manager, $managers_options)) {                 $pcu_filter_manager[] = $pcu_filter_manager_search;             }}$post_data['New_web_user'] = $pcu_filter_manager;
        }
        if ($CI->input->get("pcu_filter_channel")) { foreach ($CI->input->get("pcu_filter_channel") as $channel) {if ($pcu_filter_channel_search = array_search($channel, $channel_picklist)) {$pcu_filter_channel[] = $pcu_filter_channel_search;}}
            $post_data['New_channel'] = $pcu_filter_channel;
        }

        if ($CI->input->get("pcu_filter_recordtype")) { foreach ($CI->input->get("pcu_filter_recordtype") as $recordtype) {if ($pcu_filter_recordtype_search = array_search($recordtype, $recordtype_picklist)) {$pcu_filter_recordtype[] = $pcu_filter_recordtype_search;}}
            $post_data['New_recordtype'] = $pcu_filter_recordtype;
        }
        
        if ($CI->input->get("pcu_filter_confirmation_presence_td")) {
            $post_data['New_confirmation_presence_td'] = $CI->input->get("pcu_filter_confirmation_presence_td");
        } else {
            $post_data['New_confirmation_presence_td'] = null;
        }


        /*if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/withouttd'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/withouttd?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/withouttd?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/withouttd?page=' . $page . '?dealerid=' . $dealerid; }
        }*/
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/withouttd';
        if ($page !== null) { $post_data["Page"] = $page; }
        if ($dealerid != null) { $post_data["DealerId"] = $dealerid; }
        if ($pcu_filter_dealer != null) {  $post_data["DealerId"] = $pcu_filter_dealer;}
        if ($CI->input->get('search[value]') != null) {  $post_data["SearchString"] = $CI->input->get('search[value]');}


        //Dropdown dealer filter in PCU Requests
       /* if ($pcu_filter_dealer != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'dealerid='.$pcu_filter_dealer;
        }*/

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));
        return json_decode($CI->curl->execute(), true);
    }
    public static function get_registrations_stopwork($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', $pcu_filter_dealer = null, $period_td_start = null, $period_td_end = null)
    {
        $CI = &get_instance();
        //Ordering DataTable in CRM API
        $order_column_names = array(
        //    0 => "AccountId", 1 => "New_recordtype", 2 => "LastName", 3 => "FirstName", 4 => "MobilePhone", 5 => "EmailAddressLead", 6 => "Su_carcomplId", 7 => "New_process_status", 8 => "New_campaign_data_final", 9 => "New_web_user", 10 => "CreatedOn", 11 => "New_confirmation_presence_td", 12 => "New_channel",
            0 => "AccountId", 2 => "New_recordtype", 3 => "LastName", 4 => "Su_carcomplId", 5 => "New_process_status", 6 => "New_campaign_data_final", 8 => "New_web_user", 9 => "CreatedOn", 7 => "New_confirmation_presence_td", 2 => "New_channel", 16 => "New_fb",
        );

        $columnName = 'CreatedOn';
        $sortType = '';
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $columnName = $order_column_names[$order_column];
            $sortType = strtoupper($order_type);
        }

        $post_data = array(
            "New_recordtype" => [], "New_car_model" => [], "New_process_status" => [], "New_web_user" => [], "New_confirmation_presence_td" => null, "New_channel" => [],
            "SortColumn" => $columnName, "SortType" => $sortType
        );

        if ($period_start != null) {
            $post_data["StartDate"] = $period_start;
        }
        if ($period_end != null) {
            $post_data["EndDate"] = $period_end;
        }
        
        if ($period_td_start != null) {
            $post_data["CampaignDataFinalStartDate"] = $period_td_start;
        }
        if ($period_td_end != null) {
            $post_data["CampaignDataFinalEndDate"] = $period_td_end;
        }


        $channel_picklist = CRM::picklist_options('new_channel');
        $cars_list_options = CRM::picklist_options('new_car_model');
        $status_picklist = CRM::picklist_options('new_process_status');

        $recordtype_picklist = CRM::picklist_options('new_recordtype');
        $confirmation_presence_td_picklist = CRM::picklist_options('new_confirmation_presence_td');

        $all_managers = array();
        if ($CI->userdata['access_employees'] || $pcu_filter_dealer != null) {$CI->db->select('*')->from("users");            $all_managers = $CI->db->get()->result_array();        }
        elseif($CI->userdata['access_employees'] == false && $CI->user_company != null) {$CI->db->select('*')->where('company', $CI->user_company)->from("users");         $all_managers = $CI->db->get()->result_array();}

        $managers_options = array();
        foreach ($all_managers as $manager) { if ($manager['has_acces_tdrive'] && $manager['StateCode'] != 1) {            $managers_options[$manager['web_userid']] = $manager['first_name'] . " " . $manager['last_name']; }        }
        if ($CI->input->get("pcu_filter_car")) {            foreach ($CI->input->get("pcu_filter_car") as $car_key => $car) {if ($pcu_filter_car_search = array_search($car, $cars_list_options)) { $pcu_filter_car[] = $pcu_filter_car_search; }}
            $post_data['New_car_model'] = $pcu_filter_car;
        }

        $pcu_filter_status = array();
        if ($CI->input->get("pcu_filter_status")) {
            //foreach ($CI->input->get("pcu_filter_status") as $status_key => $status) {if ($pcu_filter_status_search = array_search($status, $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             }} $post_data['New_process_status'] = $pcu_filter_status;
            if ($pcu_filter_status_search = array_search($CI->input->get("pcu_filter_status"), $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             } $post_data['New_process_status'] = $pcu_filter_status;
        }

        if ($CI->input->get("pcu_filter_manager")) {
            foreach ($CI->input->get("pcu_filter_manager") as $manager_key => $manager) {if ($pcu_filter_manager_search = array_search($manager, $managers_options)) {                 $pcu_filter_manager[] = $pcu_filter_manager_search;             }}$post_data['New_web_user'] = $pcu_filter_manager;
        }
        if ($CI->input->get("pcu_filter_channel")) { foreach ($CI->input->get("pcu_filter_channel") as $channel) {if ($pcu_filter_channel_search = array_search($channel, $channel_picklist)) {$pcu_filter_channel[] = $pcu_filter_channel_search;}}
            $post_data['New_channel'] = $pcu_filter_channel;
        }

        if ($CI->input->get("pcu_filter_recordtype")) { foreach ($CI->input->get("pcu_filter_recordtype") as $recordtype) {if ($pcu_filter_recordtype_search = array_search($recordtype, $recordtype_picklist)) {$pcu_filter_recordtype[] = $pcu_filter_recordtype_search;}}
            $post_data['New_recordtype'] = $pcu_filter_recordtype;
        }
        
        if ($CI->input->get("pcu_filter_confirmation_presence_td")) {
            $post_data['New_confirmation_presence_td'] = $CI->input->get("pcu_filter_confirmation_presence_td");
        } else {
            $post_data['New_confirmation_presence_td'] = null;
        }





/*        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/stopwork'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/stopwork?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/stopwork?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/stopwork?page=' . $page . '?dealerid=' . $dealerid; }
        }

        //Dropdown dealer filter in PCU Requests
        if ($pcu_filter_dealer != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'dealerid='.$pcu_filter_dealer;
        }*/
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/stopwork';
        if ($page !== null) { $post_data["Page"] = $page; }
        if ($dealerid != null) { $post_data["DealerId"] = $dealerid; }
        if ($pcu_filter_dealer != null) {  $post_data["DealerId"] = $pcu_filter_dealer;}
        if ($CI->input->get('search[value]') != null) {  $post_data["SearchString"] = $CI->input->get('search[value]');}


        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));
        return json_decode($CI->curl->execute(), true);
    }
    public static function get_registrations_sales($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', $pcu_filter_dealer = null, $period_td_start = null, $period_td_end = null)
    {
        $CI = &get_instance();
        //Ordering DataTable in CRM API
        $order_column_names = array(
        //    0 => "AccountId", 1 => "New_recordtype", 2 => "LastName", 3 => "FirstName", 4 => "MobilePhone", 5 => "EmailAddressLead", 6 => "Su_carcomplId", 7 => "New_process_status", 8 => "New_campaign_data_final", 9 => "New_web_user", 10 => "CreatedOn", 11 => "New_confirmation_presence_td", 12 => "New_channel",
            0 => "AccountId", 2 => "New_recordtype", 3 => "LastName", 4 => "Su_carcomplId", 5 => "New_process_status", 6 => "New_campaign_data_final", 8 => "New_web_user", 9 => "CreatedOn", 7 => "New_confirmation_presence_td", 2 => "New_channel", 16 => "New_fb",
        );

        $columnName = 'CreatedOn';
        $sortType = '';
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $columnName = $order_column_names[$order_column];
            $sortType = strtoupper($order_type);
        }

        $post_data = array(
            "New_recordtype" => [], "New_car_model" => [], "New_process_status" => [], "New_web_user" => [], "New_confirmation_presence_td" => null, "New_channel" => [],
            "SortColumn" => $columnName, "SortType" => $sortType
        );

        if ($period_start != null) {
            $post_data["StartDate"] = $period_start;
        }
        if ($period_end != null) {
            $post_data["EndDate"] = $period_end;
        }
        
        if ($period_td_start != null) {
            $post_data["CampaignDataFinalStartDate"] = $period_td_start;
        }
        if ($period_td_end != null) {
            $post_data["CampaignDataFinalEndDate"] = $period_td_end;
        }

        $channel_picklist = CRM::picklist_options('new_channel');
        $cars_list_options = CRM::picklist_options('new_car_model');
        $status_picklist = CRM::picklist_options('new_process_status');

        $recordtype_picklist = CRM::picklist_options('new_recordtype');
        $confirmation_presence_td_picklist = CRM::picklist_options('new_confirmation_presence_td');

        $all_managers = array();
        if ($CI->userdata['access_employees'] || $pcu_filter_dealer != null) {$CI->db->select('*')->from("users");            $all_managers = $CI->db->get()->result_array();        }
        elseif($CI->userdata['access_employees'] == false && $CI->user_company != null) {$CI->db->select('*')->where('company', $CI->user_company)->from("users");         $all_managers = $CI->db->get()->result_array();}

        $managers_options = array();
        foreach ($all_managers as $manager) { if ($manager['has_acces_tdrive'] && $manager['StateCode'] != 1) {            $managers_options[$manager['web_userid']] = $manager['first_name'] . " " . $manager['last_name']; }        }
        if ($CI->input->get("pcu_filter_car")) {            foreach ($CI->input->get("pcu_filter_car") as $car_key => $car) {if ($pcu_filter_car_search = array_search($car, $cars_list_options)) { $pcu_filter_car[] = $pcu_filter_car_search; }}
            $post_data['New_car_model'] = $pcu_filter_car;
        }

        $pcu_filter_status = array();
        if ($CI->input->get("pcu_filter_status")) {
            //foreach ($CI->input->get("pcu_filter_status") as $status_key => $status) {if ($pcu_filter_status_search = array_search($status, $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             }} $post_data['New_process_status'] = $pcu_filter_status;
            if ($pcu_filter_status_search = array_search($CI->input->get("pcu_filter_status"), $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             } $post_data['New_process_status'] = $pcu_filter_status;
        }

        if ($CI->input->get("pcu_filter_manager")) {
            foreach ($CI->input->get("pcu_filter_manager") as $manager_key => $manager) {if ($pcu_filter_manager_search = array_search($manager, $managers_options)) {                 $pcu_filter_manager[] = $pcu_filter_manager_search;             }}$post_data['New_web_user'] = $pcu_filter_manager;
        }
        if ($CI->input->get("pcu_filter_channel")) { foreach ($CI->input->get("pcu_filter_channel") as $channel) {if ($pcu_filter_channel_search = array_search($channel, $channel_picklist)) {$pcu_filter_channel[] = $pcu_filter_channel_search;}}
            $post_data['New_channel'] = $pcu_filter_channel;
        }

        if ($CI->input->get("pcu_filter_recordtype")) { foreach ($CI->input->get("pcu_filter_recordtype") as $recordtype) {if ($pcu_filter_recordtype_search = array_search($recordtype, $recordtype_picklist)) {$pcu_filter_recordtype[] = $pcu_filter_recordtype_search;}}
            $post_data['New_recordtype'] = $pcu_filter_recordtype;
        }
        
        if ($CI->input->get("pcu_filter_confirmation_presence_td")) {
            $post_data['New_confirmation_presence_td'] = $CI->input->get("pcu_filter_confirmation_presence_td");
        } else {
            $post_data['New_confirmation_presence_td'] = null;
        }

/*        if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/sales'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/sales?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/sales?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/sales?page=' . $page . '?dealerid=' . $dealerid; }
        }

        //Dropdown dealer filter in PCU Requests
        if ($pcu_filter_dealer != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'dealerid='.$pcu_filter_dealer;
        }*/
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/sales';
        if ($page !== null) { $post_data["Page"] = $page; }
        if ($dealerid != null) { $post_data["DealerId"] = $dealerid; }
        if ($pcu_filter_dealer != null) {  $post_data["DealerId"] = $pcu_filter_dealer;}
        if ($CI->input->get('search[value]') != null) {  $post_data["SearchString"] = $CI->input->get('search[value]');}

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));
        return json_decode($CI->curl->execute(), true);
    }
    public static function get_registrations_pcu($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', $pcu_filter_dealer = null, $period_td_start = null, $period_td_end = null)
    {
        $CI = &get_instance();
        //Ordering DataTable in CRM API
        $order_column_names = array(
        //    0 => "AccountId", 1 => "New_recordtype", 2 => "LastName", 3 => "FirstName", 4 => "MobilePhone", 5 => "EmailAddressLead", 6 => "Su_carcomplId", 7 => "New_process_status", 8 => "New_campaign_data_final", 9 => "New_web_user", 10 => "CreatedOn", 11 => "New_confirmation_presence_td", 12 => "New_channel",
            0 => "AccountId", 2 => "New_recordtype", 3 => "LastName", 4 => "Su_carcomplId", 5 => "New_process_status", 6 => "New_campaign_data_final", 8 => "New_web_user", 9 => "CreatedOn", 7 => "New_confirmation_presence_td", 2 => "New_channel", 16 => "New_fb",
        );

        $columnName = 'CreatedOn';
        $sortType = '';
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $columnName = $order_column_names[$order_column];
            $sortType = strtoupper($order_type);
        }

        $post_data = array(
            "New_recordtype" => [], "New_car_model" => [], "New_process_status" => [], "New_web_user" => [], "New_confirmation_presence_td" => null, "New_channel" => [],
            "SortColumn" => $columnName, "SortType" => $sortType
        );

        if ($period_start != null) {
            $post_data["StartDate"] = $period_start;
        }
        if ($period_end != null) {
            $post_data["EndDate"] = $period_end;
        }
        
        if ($period_td_start != null) {
            $post_data["CampaignDataFinalStartDate"] = $period_td_start;
        }
        if ($period_td_end != null) {
            $post_data["CampaignDataFinalEndDate"] = $period_td_end;
        }

        $channel_picklist = CRM::picklist_options('new_channel');
        $cars_list_options = CRM::picklist_options('new_car_model');
        $status_picklist = CRM::picklist_options('new_process_status');

        $recordtype_picklist = CRM::picklist_options('new_recordtype');
        $confirmation_presence_td_picklist = CRM::picklist_options('new_confirmation_presence_td');

        $all_managers = array();
        if ($CI->userdata['access_employees'] || $pcu_filter_dealer != null) {$CI->db->select('*')->from("users");            $all_managers = $CI->db->get()->result_array();        }
        elseif($CI->userdata['access_employees'] == false && $CI->user_company != null) {$CI->db->select('*')->where('company', $CI->user_company)->from("users");         $all_managers = $CI->db->get()->result_array();}

        $managers_options = array();
        foreach ($all_managers as $manager) { if ($manager['has_acces_tdrive'] && $manager['StateCode'] != 1) {            $managers_options[$manager['web_userid']] = $manager['first_name'] . " " . $manager['last_name']; }        }
        if ($CI->input->get("pcu_filter_car")) {            foreach ($CI->input->get("pcu_filter_car") as $car_key => $car) {  if ($pcu_filter_car_search = array_search($car, $cars_list_options)) { $pcu_filter_car[] = $pcu_filter_car_search; }}
            $post_data['New_car_model'] = $pcu_filter_car;
        }
        $pcu_filter_status = array();
        if ($CI->input->get("pcu_filter_status")) {
            //foreach ($CI->input->get("pcu_filter_status") as $status_key => $status) {if ($pcu_filter_status_search = array_search($status, $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             }} $post_data['New_process_status'] = $pcu_filter_status;
            if ($pcu_filter_status_search = array_search($CI->input->get("pcu_filter_status"), $status_picklist)) {                 $pcu_filter_status[] = $pcu_filter_status_search;             } $post_data['New_process_status'] = $pcu_filter_status;
        }

        if ($CI->input->get("pcu_filter_manager")) {
            foreach ($CI->input->get("pcu_filter_manager") as $manager_key => $manager) {if ($pcu_filter_manager_search = array_search($manager, $managers_options)) {                 $pcu_filter_manager[] = $pcu_filter_manager_search;             }}$post_data['New_web_user'] = $pcu_filter_manager;
        }
        if ($CI->input->get("pcu_filter_channel")) { foreach ($CI->input->get("pcu_filter_channel") as $channel) {if ($pcu_filter_channel_search = array_search($channel, $channel_picklist)) {$pcu_filter_channel[] = $pcu_filter_channel_search;}}
            $post_data['New_channel'] = $pcu_filter_channel;
        }

        if ($CI->input->get("pcu_filter_recordtype")) { foreach ($CI->input->get("pcu_filter_recordtype") as $recordtype) {if ($pcu_filter_recordtype_search = array_search($recordtype, $recordtype_picklist)) {$pcu_filter_recordtype[] = $pcu_filter_recordtype_search;}}
            $post_data['New_recordtype'] = $pcu_filter_recordtype;
        }

        if ($CI->input->get("pcu_filter_confirmation_presence_td")) {
            $post_data['New_confirmation_presence_td'] = $CI->input->get("pcu_filter_confirmation_presence_td");
        } else {
            $post_data['New_confirmation_presence_td'] = null;
        }

   /*     if ($page === null) {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/pcu'; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/pcu?dealerid=' . $dealerid; }
        } else {
            if ($dealerid == null) { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/pcu?page=' . $page; }
            else { $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/pcu?page=' . $page . '?dealerid=' . $dealerid; }
        }

        //Dropdown dealer filter in PCU Requests
        if ($pcu_filter_dealer != null) {
            $response_link .= (parse_url($response_link, PHP_URL_QUERY) ? '&' : '?') . 'dealerid='.$pcu_filter_dealer;
        }*/
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/pcu';
        if ($page !== null) { $post_data["Page"] = $page; }
        if ($dealerid != null) { $post_data["DealerId"] = $dealerid; }
        if ($pcu_filter_dealer != null) {  $post_data["DealerId"] = $pcu_filter_dealer;}
        if ($CI->input->get('search[value]') != null) {  $post_data["SearchString"] = $CI->input->get('search[value]');}

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));
        return json_decode($CI->curl->execute(), true);
    }


    public static function get_registrations_all_by_phone($phone)
    {
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/byphone/' . $phone;

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $returnData = json_decode($CI->curl->execute(), true);
        return $returnData;
    }

public static function get_registrations_history($idClient)
    {
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/registrations/history/' . $idClient;

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $returnData = json_decode($CI->curl->execute(), true);
        return $returnData;
    }

 public static function get_clients_by_phone($phone)
    {
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/client/phone/' . $phone;

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $returnData = json_decode($CI->curl->execute(), true);
        return $returnData;
    }






    // --- ACTUALIZATION SECTION ---
    public static function get_all_actualization($page = null)
    {
        if($page != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/actualization/all?page=' . $page;
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/actualization/all';
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_actualization($dealerid = null)
    {
        $response_link = "";
        if($dealerid != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/actualization/all/'.$dealerid;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    // --- PLANNING SECTION ---
    public static function get_planning_tasks_all($dealerid = null)
    {
        if ($dealerid == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/planning/task/all';
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/planning/task/all?dealerid=' . $dealerid;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_planning_tasks_overdue($dealerid = null)
    {
        if ($dealerid == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/planning/task/overdue';
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/planning/task/overdue?dealerid=' . $dealerid;
        }
        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_planning_tasks_mydaytasks($dealerid = null)
    {
        if ($dealerid == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/planning/task/all';
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/planning/task/all?dealerid=' . $dealerid;
        }
        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        $planning_tasks = json_decode($CI->curl->execute(), true);
        $planning_tasks = $planning_tasks['Result']['Data'];

        $t = date('d-m-Y');
        $current_day_start = strtotime($t);
        $current_day_end = $current_day_start + 86400;

        $return_tasks = array();
        foreach ($planning_tasks as $planning_task) {
            if ($planning_task['New_web_userId'] != $CI->user_webuser_id) {
                continue;
            }

            if (
                $planning_task['ScheduledStart'] >= $current_day_start &&
                $planning_task['ScheduledStart'] < $current_day_end
            ) {
                $return_tasks[] = $planning_task;
            }
        }

        return array('Result' => array( 'Data' => $return_tasks));
    }

    public static function get_planning_tasks_by_requestId($request_id = null)
    {
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/planning/task/registrations/' . $request_id;
        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_planning_task($activityid = null)
    {
        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/planning/task/'.$activityid);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    // --- CLIENTS SECTION ---
    public static function get_special_clients($page = null)
    {
        $special_clients = array();

        for ($i = 0; true; $i = $i + 10) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/special/clients/' . $i;
            $CI = &get_instance();
            $CI->curl->create($response_link);
            $CI->curl->http_header('Accept', 'application/json');

            $data = json_decode($CI->curl->execute(), true);

            if (empty($data['Result']['Data'])) {
                break;
            }

            foreach ($data['Result']['Data'] as $row) {
                $special_clients[] = $row;
            }
        }
        return $special_clients;
    }

    public static function get_clients($dealerid = null, $page = null, $period_start = null, $period_end = null,  $order_column = null, $order_type = 'desc', $period_sale_start = null, $period_sale_end = null)
    {

        $columnName = 'CreatedOn';
        $sortType = '';
        if ($order_column != null && isset($order_column_names[$order_column])) {
            $columnName = $order_column_names[$order_column];
            $sortType = strtoupper($order_type);
        }

        $period_query = "";
      /*  if ($period_start != null) {
            //$post_data["StartDate"] = $period_start;
            $period_query .= "&StartDate=".$period_start;
        }
        if ($period_end != null) {
            //$post_data["EndDate"] = $period_end;
            $period_query .= "&EndDate=".$period_end;
        }
*/
        if ($page !== null) {            
            if ($dealerid == null) {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/client/all?page=' . $page . $period_query;
            } else {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/client/all?page=' . $page . '?dealerid=' . $dealerid . $period_query;
            }
        } else {
            if ($dealerid == null) {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/client/all/' . $period_query;
            } else {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/client/all/?dealerid=' . $dealerid . $period_query;
            }
        }
        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_client($vin = null)
    {
        $response_link = "";
        if($vin != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/client/'.urlencode($vin);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_client_incidents($vin = null)
    {
        $response_link = "";
        if($vin != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/client/incident/'.urlencode($vin);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_client_registrations($vin = null)
    {
        $response_link = "";
        if($vin != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/client/registrations/vin/'.urlencode($vin);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_client_visits($vin = null)
    {
        $response_link = "";
        if($vin != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/client/visits/'.urlencode($vin);
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    // --- INCIDENTS SECTION ---
    public static function get_incidents_all($dealerid = null, $page = null)
    {
        if ($dealerid == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/all/'.$page;
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/all/'.$page.'?dealerid=' . $dealerid;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_incidents_closed($dealerid = null, $page = null)
    {
        if ($dealerid == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/closed/'.$page;
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/closed/'.$page.'?dealerid=' . $dealerid;
        }
        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_incidents_opened($dealerid = null, $page = null)
    {
        if ($dealerid == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/opened/'.$page;
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/opened/'.$page.'?dealerid=' . $dealerid;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_incidents_withoutdealer($page = null)
    {
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents/withoutdealer/'.$page;

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_incident_by_id($IncidentId = null)
    {
        $response_link = "";
        if($IncidentId != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents/'.$IncidentId;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_incident_answers( $IncidentId = null)
    {
        $response_link = "";
        if($IncidentId != null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents/answers/'.$IncidentId;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }


     // --- INCIDENTS ANSWERS SECTION ---
    public static function get_incidents_answers_all($dealerid = null, $page = null)
    {
        if ($dealerid == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/answers/all/'.$page;
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/answers/all/'.$page.'?dealerid=' . $dealerid;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_incidents_answers_unprocessed($dealerid = null, $page = null)
    {
        if ($dealerid == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/answers/raw/'.$page;
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/answers/raw/'.$page.'?dealerid=' . $dealerid;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_incidents_answers_my_unprocessed($web_userid, $dealerid = null, $page = null)
    {
        if ($dealerid == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/answers/raw/my/'.$web_userid;
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/incidents/answers/raw/'.$web_userid.'?dealerid=' . $dealerid;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    // --- GET WebUser by ID ---
    public static function get_webuser_by_id($webuserId = null)
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/'.$webuserId);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_email_send($web_user_id){
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/contacts/'.$web_user_id);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_period(){
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/client/visits/period');
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    } 


    // --- SUVEHICLE SECTION ---
    public static function get_suvehicle_all($dealer = null, $page = null, $columnName = null, $sortType = null)
    {
        if ($dealer == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/booking/suvehicle/all/';
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/booking/suvehicle/all/?page='.$page.'&dealer=' . $dealer;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_suvehicle_by_id($Su_vehicleId)
    {
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/booking/suvehicle/'.$Su_vehicleId;


        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }


    public static function get_suvehicle_by_carcompl($carcomplId)
    {
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/booking/suvehicle/carcompl/'.$carcomplId;


        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_suvehicle_helper_keys_carcompl()
    {

        $CI = &get_instance();
        if (!$CI->userdata['access_employees']) {
            $suvehicle_all = CRM::get_suvehicle_all($CI->user_company);
        } else {
            $suvehicle_all = CRM::get_suvehicle_all();
        }

        $suvehicle_sorted = array();
        foreach ($suvehicle_all['Result']['Data'] as $suvehicle) {
            //if(!isset($suvehicle_sorted[]))
            $suvehicle_sorted[$suvehicle['Su_carcomplvehicle']][] = $suvehicle;
        }
        return $suvehicle_sorted;
    }


    // --- CONTRACTS SECTION ---
    public static function get_contracts($dealer = null, $page = null, $columnName = null, $sortType = null)
    {
        if ($dealer == null) {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/offer/all/';
        } else {
            $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/offer/all/?page='.$page.'&dealer=' . $dealer;
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_contract($contractId)
    {
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/offer/'.$contractId;


        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
    public static function get_contract_by_registration_tdriveId($registration_tdriveId)
    {
        $contracts = CRM::get_contracts(null);
        $contracts = $contracts['Result']['Data'];

        $found_contracts = array();
        foreach ($contracts as $contract) {
            if ($contract['New_registration_tdriveId'] == $registration_tdriveId) {
                $found_contract[] = $contract;
            }
        }
        return $found_contracts;
    }



    // --- CREDITS SECTION ---
    public static function get_credits($dealer = null, $type = 'all')
    {
        if ($type == 'lizings') {
            if ($dealer == null) {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/credit/type/leasing';
            } else {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/credit/type/leasing/?page='.$page.'&dealerId=' . $dealer;
            }
        } else if ($type == 'credit') {
            if ($dealer == null) {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/credit/type/credit';
            } else {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/credit/type/credit/?page='.$page.'&dealerId=' . $dealer;
            }
        } else {
            if ($dealer == null) {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/credit/all';
            } else {
                $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/credit/all/?page='.$page.'&dealerId=' . $dealer;
            }
        }

        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        $data_credits_list = json_decode($CI->curl->execute(), true);
        foreach ($data_credits_list['Result']['Data'] as $key => $value) {
            if ($value['MobilePhone'] == null) {
                unset($data_credits_list['Result']['Data'][$key]);
            }
        }
        //print_r($CI->curl->execute());
        log_message('info'," RESPONSE: get_credits".json_encode($data_credits_list));

        return $data_credits_list; //json_decode($CI->curl->execute(), true);
    }

   public static function get_credit($creditId)
    {
        $response_link = 'https://webportal.crm.servicedesk.in.ua/api/' . self::$_organization . '/credit/'.$creditId;


        $CI = &get_instance();
        $CI->curl->create($response_link);
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
/*     public static function get_contract_by_registration_tdriveId($registration_tdriveId)
    {
        $contracts = CRM::get_contracts(null);
        $contracts = $contracts['Result']['Data'];

        $found_contracts = array();
        foreach ($contracts as $contract) {
            if ($contract['New_registration_tdriveId'] == $registration_tdriveId) {
                $found_contract[] = $contract;
            }
        }
        return $found_contracts;
    }

*/



    /* -----------------------------------------
     * Create POST methods
     * -----------------------------------------
     */
    public static function del_visit_sto($id_list){
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/crm/delete/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $EntityName = 'new_afsinfo';
        settype($EntityName, "string");

        $CI->curl->delete(json_encode(array(
            'EntityName' => $EntityName,
            'EntityId' => $id_list
        )));

        return json_decode($CI->curl->execute(), true);
    }
    public static function post_visit_sto($list, $line_this_time){
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/client/visits/loaddb/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $Lastname = substr($list[1],0,400);

        if(mb_strtolower($list[20]) == 'фл'){
            $Isfl = 1;
        }
        else{
            $Isfl = 0;
        }

        if(mb_strtolower($list[23]) == 'да'){
            $Iscontact = 1;
        }
        else{
            $Iscontact = 0;
        }

        if(mb_strtolower($list[3]) == 'm'){
            $Civility = 1;
        }
        else{
            $Civility = 2;
        }

        $name = explode(" ", $list[2]);
        $count = 0;
        $Middlename = '';
        foreach($name as $key => $name_value) {
            if($count == 0){
                $Firstname = $name_value;
            }
            else{
                $Middlename .= $name_value;
            }
            $count++;
        }

        settype($Lastname, "string");
        settype($Firstname, "string");
        settype($Middlename, "string");
        // settype($list[2], "string");
        settype($list[4], "string");
        settype($list[5], "string");
        settype($list[6], "string");
        settype($list[7], "string");
        settype($list[8], "string");
        $Cityphone = str_replace('+','',$list[9]);
        settype($Cityphone, "string");
        $Cellphone = str_replace('+','',$list[10]);
        settype($Cellphone, "string");
        settype($list[11], "string");
        settype($list[14], "string");
        settype($list[15], "string");
        settype($list[16], "string");
        settype($list[17], "string");
        settype($list[18], "string");
        $Firstvisitdate = strtotime($list[19]);
        settype($Firstvisitdate, "string");
        settype($Isfl, "integer");
        settype($list[21], "string");
        settype($list[22], "string");
        settype($Iscontact, "integer");
        $Typeofwork = substr($list[24],0,2000);
        settype($Typeofwork, "string");
        $Customerrecordsource = substr($list[25],0,400);
        settype($Customerrecordsource, "string");
        settype($line_this_time, "integer");
        settype($list[0], "string");
        $Period = date('dmY');
        settype($Period, "string");

        $CI->curl->post(json_encode(array(
            'Lastname' => $Lastname,
            'Firstname' => $Firstname,
            'Middlename' => $Middlename,
            'Civility' => $Civility,
            'Address' => $list[4],
            'Postcode' => $list[5],
            'City' => $list[6],
            'Region' => $list[7],
            'Masterreceiver' => $list[8],
            'Cityphone' => $Cityphone,
            'Cellphone' => $Cellphone,
            'Email' => $list[11],
            'Invoicedate' => strtotime($list[12]),
            'Returndate' => strtotime($list[13]),
            'Invoicenumber' => $list[14],
            'Vehiclemodel' => $list[15],
            'Vin' => $list[16],
            'New_km' => $list[17],
            'Driverlicensenumber' => $list[18],
            'Firstvisitdate' => $Firstvisitdate,
            'Isfl' => $Isfl,
            'Dealer' => $list[21],
            'Rrdi' => $list[22],
            'Iscontact' => $Iscontact,
            'Typeofwork' => $Typeofwork,
            'Customerrecordsource' => $Customerrecordsource,
            'Rowfile' => $line_this_time,
            'Rownumber' => $list[0],
            'Period' => $Period
        )));

        return json_decode($CI->curl->execute(), true);
    }

    public static function post_registrations_create($post_data)
    {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/registrations/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode(array(
            'New_web_user' => array(
                'New_name' => $post_data['New_name'],
                'New_lastname' => $post_data['New_lastname'],
                'New_mobilephone' => $post_data['New_mobilephone'],
                'New_email' => $post_data['New_email'],
                'New_login' => $post_data['New_login'],
                'New_password' => $post_data['New_password']
            )
        )));

        return json_decode($CI->curl->execute(), true);

    }

    public static function post_car_create($form_data)
    {
        $post_data['Su_carcomplId'] = "00000000-0000-0000-0000-000000000000";
        $post_data['New_car_model'] = null;
        $post_data['Su_name'] = $form_data['car_id'];

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/cars/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));

        return json_decode($CI->curl->execute(), true);
    }


    public static function post_feedback_create($form_data)
    {
        if (isset($form_data['New_web_userId']) && $form_data['New_web_userId'] != "") {
            $New_web_userId = $form_data['New_web_userId'];
        }

        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_web_user',
            'EntityName' => 'new_web_user',
            'EntityId' => $New_web_userId
        );

        $post_data['EntityName'] = 'new_feedback';
        //$post_data['New_name'] = $form_data['New_name'];
        //$post_data['New_phone'] = $form_data['New_phone'];
        //$post_data['New_email'] = $form_data['New_email'];
        //$post_data['New_description'] = $form_data['New_description'];

        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_name',
            'Value' => $form_data['name']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_phone',
            'Value' => $form_data['New_phone']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_email',
            'Value' => $form_data['New_email']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_description',
            'Value' => $form_data['New_description']
        );

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/feedbacks/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));


        $response = $CI->curl->execute();
        log_message('info', json_encode(array('Method' => __METHOD__,
            'Link' => 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/feedbacks/create/',
            'Params' => $post_data,
            'Response'=> $response, 'User'=> $CI->user_row), JSON_UNESCAPED_UNICODE));

        return json_decode($response, true);
        //return json_decode($CI->curl->execute(), true);
    }


    public static function post_testdrive_create($form_data)
    {
        if (isset($form_data['request_type']) && $form_data['request_type'] != "") {
            $post_data['New_recordtype'] = $form_data['request_type'];
        } else {
            $post_data['New_recordtype'] = 1;
        }
        $post_data['New_process_status'] = $form_data['testdrive_status'];

        if($form_data['testdrive_date'] != "") {
            $post_data['New_campaign_data_final'] = strtotime($form_data['testdrive_date']);
        } else {
            $post_data['New_campaign_data_final'] = null;
        }
        //$post_data['New_campaign_data_final'] = strtotime($form_data['testdrive_date']);

        if (isset($form_data['confirmation_presence_td']) && $form_data['confirmation_presence_td'] != "") {
            $post_data['New_confirmation_presence_td'] = $form_data['confirmation_presence_td'];
        } else {
            $post_data['New_confirmation_presence_td'] = null;
        }

        $post_data['New_comment'] = $form_data['testdrive_addinfo'];
        $post_data['New_reason_for_stop_work'] = null;
        $post_data['New_krugobzvona'] = null;
        $post_data['New_call_result'] = null;
        $post_data['New_actualization_result'] = null;

        $post_data['New_lead_id'] = array(
            'FirstName' => $form_data['client_name'],
            'LastName' => $form_data['client_surname'],
            'MobilePhone' => $form_data['client_phone'],
            'EMailAddress1' => $form_data['client_email'],
            'New_channel' => 3
        );


        if (isset($form_data['credit']) && $form_data['credit'] != "") {
            $post_data['New_credit'] = boolval($form_data['credit']);
        } else {
            $post_data['New_credit'] = null;
        }

        if (isset($form_data['new_set_td']) && $form_data['new_set_td'] != "") {
            $post_data['New_set_td'] = true;
            $put_data['New_set_td_date'] = time();
        } elseif ($form_data['testdrive_date'] != "") {
            $post_data['New_set_td'] = true;
            $put_data['New_set_td_date'] = time();
        }
        if (isset($form_data['new_completed_td']) && $form_data['new_completed_td'] != "") {
            $post_data['New_completed_td'] = true;
            $put_data['New_completed_td_date'] = time();
        } elseif ($form_data['confirmation_presence_td'] == 1) {
            $post_data['New_completed_td'] = true;
            $put_data['New_completed_td_date'] = time();
        }
        if (isset($form_data['new_sale_agreement']) && $form_data['new_sale_agreement'] != "") {
            $post_data['New_sale_agreement'] = true;
            $put_data['New_sale_agreement_date'] = time();
        }
        if (isset($form_data['new_run_contract']) && $form_data['new_run_contract'] != "") {
            $post_data['New_run_contract'] = true;
            $put_data['New_run_contract_date'] = time();
        }
        if (isset($form_data['new_exercise_planned_car']) && $form_data['new_exercise_planned_car'] != "") {
            $post_data['New_exercise_planned_car'] = true;
            $put_data['New_exercise_planned_car_date'] = time();
        }


        $post_data['New_model_for_td']['Su_carcomplId'] = $form_data['car_id'];
        $post_data['New_salon']['AccountId'] = $form_data['dealer_center_id'];


        if (isset($form_data['webuserid']) && $form_data['webuserid'] != "") {
            $post_data['New_web_user']['New_web_userId'] = $form_data['webuserid'];
        } else {
            $post_data['New_web_user']['New_web_userId'] = $form_data['testdrive_manager'];
        }
        //$post_data['New_web_user']['New_web_userId'] = $form_data['testdrive_manager'];

        ////vardump(array($post_data, json_encode($post_data), $form_data));

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));

        $response = $CI->curl->execute();
        log_message('info', json_encode(array('Method' => __METHOD__,
            'Link' => 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/create/',
            'Params' => $post_data,
            'Response'=> $response, 'User'=> $CI->user_row), JSON_UNESCAPED_UNICODE));

        return json_decode($response, true);
    }

    public static function post_actualization_create($form_data)
    {
        $post_data['New_recordtype'] = $form_data['recordtype'];
        $post_data['New_process_status'] = $form_data['testdrive_status'];

        if ($form_data['testdrive_date'] != "") {
            $post_data['New_campaign_data_final'] = strtotime($form_data['testdrive_date']);
        } else {
            $post_data['New_campaign_data_final'] = null;
        }


        if (isset($form_data['confirmation_presence_td']) && $form_data['confirmation_presence_td'] != "") {
            $post_data['New_confirmation_presence_td'] = $form_data['confirmation_presence_td'];
        } else {
            $post_data['New_confirmation_presence_td'] = null;
        }

        $post_data['New_comment'] = $form_data['comment'];
        $post_data['New_reason_for_stop_work'] = null;

        $post_data['New_krugobzvona'] = $form_data['krugobzvona'];
        $post_data['New_call_result'] = $form_data['call_result'];
        $post_data['New_actualization_result'] = $form_data['actualization_result'];
        $post_data['New_other_failure'] = $form_data['other_failure'];

        if ($form_data['time_to_callback'] != "") {
            $post_data['New_time_to_callback'] = strtotime($form_data['time_to_callback']);
        } else {
            $post_data['New_time_to_callback'] = null;
        }

        if ($form_data['return_to_diller'] != "") {
            $post_data['New_return_to_diller'] = true;
        } else {
            $post_data['New_return_to_diller'] = false;
        }

        $post_data['New_lead_id'] = array(
            'FirstName' => $form_data['client_name'],
            'LastName' => $form_data['client_surname'],
            'MobilePhone' => $form_data['client_phone'],
            'New_channel' => $form_data['channel']
        );

        $post_data['New_model_for_td']['Su_carcomplId'] = $form_data['car_id'];
        $post_data['New_salon']['AccountId'] = $form_data['dealer_center_id'];


        $post_data['New_web_user']['New_web_userId'] = $form_data['web_userid'];

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));


        $response = $CI->curl->execute();
        log_message('info', json_encode(array('Method' => __METHOD__,
            'Link' => 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/create/',
            'Params' => $post_data,
            'Response'=> $response, 'User'=> $CI->user_row), JSON_UNESCAPED_UNICODE));

        return json_decode($response, true);
//        return json_decode($CI->curl->execute(), true);
    }

    /*
     * WebUser Create
     * /api/{organization}/webuser/create
     */
    public static function post_webuser_create($form_data)
    {
        /*{
  "EntityName": "new_web_user",
  "Id": "00000000-0000-0000-0000-000000000000",
  "Picklists": [
    {
      "AttributeName": "new_position",
      "Value": 0
    }
  ],
  "Lookups": [
    {
      "AttributeName": "new_dealer",
      "EntityName": "account",
      "EntityId": "00000000-0000-0000-0000-000000000000"
    },
    {
      "AttributeName": "new_dealer_afs",
      "EntityName": "account",
      "EntityId": "00000000-0000-0000-0000-000000000000"
    },
    {
      "AttributeName": "new_parent_id",
      "EntityName": "new_web_user",
      "EntityId": "00000000-0000-0000-0000-000000000000"
    }
  ],
  "DateTimes": [
    {
      "AttributeName": "New_birthday",
      "Value": 0
    }
  ],
  "OtherAttributes": [
    {
      "AttributeName": "new_name",
      "Value": ""
    },
    {
      "AttributeName": "new_lastname",
      "Value": ""
    },
    {
      "AttributeName": "new_mobilephone",
      "Value": ""
    },
    {
      "AttributeName": "new_email",
      "Value": ""
    },
    {
      "AttributeName": "new_login",
      "Value": ""
    },
    {
      "AttributeName": "new_password",
      "Value": ""
    },
    {
      "AttributeName": "new_has_acces_newtdrive",
      "Value": false
    },
    {
      "AttributeName": "new_head_sales",
      "Value": false
    },
    {
      "AttributeName": "new_has_acces_tdrive",
      "Value": false
    },
    {
      "AttributeName": "new_has_acces_inc",
      "Value": false
    },
    {
      "AttributeName": "new_responsible_for_quality",
      "Value": false
    },
    {
      "AttributeName": "new_access_sales",
      "Value": false
    },
    {
      "AttributeName": "new_access_check",
      "Value": false
    },
    {
      "AttributeName": "new_access_employees",
      "Value": false
    },
    {
      "AttributeName": "new_text",
      "Value": ""
    },
    {
      "AttributeName": "new_ext",
      "Value": ""
    },
    {
      "AttributeName": "new_dept",
      "Value": ""
    },
    {
      "AttributeName": "new_appointment",
      "Value": ""
    },
    {
      "AttributeName": "new_work_phone",
      "Value": ""
    }
  ]
}
*/
        $post_data['EntityName'] = 'new_web_user';

        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_name',
            'Value' => $form_data['name']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'New_lastname',
            'Value' => $form_data['surname']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'New_mobilephone',
            'Value' => $form_data['phone']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'New_email',
            'Value' => $form_data['email']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_login',
            'Value' => $form_data['phone']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_password',
            'Value' => "".$form_data['password']
        );
        /*
         * //  $post_data['New_name'] = ;
        $post_data['New_lastname'] = $form_data["surname"];
        $post_data['New_mobilephone'] = $form_data["phone"];
        $post_data['New_email'] = $form_data["email"];
        $post_data['New_login'] = $form_data["phone"];
        $post_data['New_password'] = $form_data["password"];*/


        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_dealer',
            'EntityName' => 'account',
            'EntityId' => $form_data['company']
        );

        $New_has_acces_newtdrive = false;
        $New_head_sales = false;
        $New_has_acces_tdrive = false;
        $New_has_acces_inc = false;
        $New_responsible_for_quality = false;
        $New_access_sales = false;
        $New_access_employees = false;

        $New_access_check = true;

        foreach ($form_data['position'] as $position) {
            if ($position == 4) { // Сотрудник автосалона
                $New_has_acces_newtdrive = true;
                $New_has_acces_tdrive = true;
            } elseif ($position == 5) { //Начальник отдела продаж автосалона
                $New_has_acces_newtdrive = true;
                $New_has_acces_tdrive = true;
                $New_head_sales = true;
            } elseif ($position == 6) { //Ответственный за качество
                $New_has_acces_inc = true;
                $New_responsible_for_quality = true;
            }
        }

        if ($form_data["head_sales"]) {
            $New_head_sales = true;
        }
        if ($form_data["has_acces_newtdrive"]) {
            $New_has_acces_newtdrive = true;
        }
        if ($form_data["has_acces_tdrive"]) {
            $New_has_acces_tdrive = true;
        }
        if ($form_data["responsible_for_quality"]) {
            $New_responsible_for_quality = true;
        }

        $post_data['OtherAttributes'][] = array(
            "AttributeName" => "new_has_acces_newtdrive",
            "Value" => $New_has_acces_newtdrive
        );
        $post_data['OtherAttributes'][] = array(
            "AttributeName" => "new_head_sales",
            "Value" => $New_head_sales
        );
        $post_data['OtherAttributes'][] = array(
            "AttributeName" => "new_has_acces_tdrive",
            "Value" => $New_has_acces_tdrive
        );
        $post_data['OtherAttributes'][] = array(
            "AttributeName" => "new_has_acces_inc",
            "Value" => $New_has_acces_inc
        );
        $post_data['OtherAttributes'][] = array(
            "AttributeName" => "new_responsible_for_quality",
            "Value" => $New_responsible_for_quality
        );
        $post_data['OtherAttributes'][] = array(
            "AttributeName" => "new_access_sales",
            "Value" => $New_access_sales
        );
        $post_data['OtherAttributes'][] = array(
            "AttributeName" => "new_access_check",
            "Value" => $New_access_check
        );
        $post_data['OtherAttributes'][] = array(
            "AttributeName" => "new_access_employees",
            "Value" => $New_access_employees
        );


        if ($form_data["new_position"]) {
            $post_data["Picklists"][] = array(
                'AttributeName' => "new_position",
                "Value" => $form_data["new_position"]
            );
        }


        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));
        $response = json_decode($CI->curl->execute(), true);
        log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/create/' . " Options: " . json_encode($post_data) . ' Response: '  . json_encode($response) . ' User: ' . json_encode($CI->user_row));

        return $response;
        //return json_decode($CI->curl->execute(), true);
    }

    /*
     * credit Create
     * /api/{organization}/credit/create
     */
    public static function post_credit_create($form_data)
    {
        /*
        {
      "EntityName": "new_credit",
      "Id": "00000000-0000-0000-0000-000000000000",
      "Picklists": [],
      "Lookups": [
        {
          "AttributeName": "new_bankid",
          "EntityName": "account",
          "EntityId": "00000000-0000-0000-0000-000000000000"
        }
      ],
      "DateTimes": [
        {
          "AttributeName": "new_date_bank_application",
          "Value": 0
        },
        {
          "AttributeName": "new_date_guaranteeletter",
          "Value": 0
        },
        {
          "AttributeName": "new_date_bank_transaction",
          "Value": 0
        }
      ],
      "OtherAttributes": [
        {
          "AttributeName": "new_first_installment",
          "Value": 0
        },
        {
          "AttributeName": "new_term",
          "Value": 0
        },
        {
          "AttributeName": "new_another_bank_reason",
          "Value": ""
        },
        {
          "AttributeName": "new_credit_agricole_kredobank",
          "Value": false
        },
        {
          "AttributeName": "new_history",
          "Value": ""
        }
      ]
    }
*/
        $post_data['EntityName'] = 'new_credit';


        $new_type = null;
        if ($form_data['new_ContractStatus'] == 5) {
            $new_type = 100000002;
        } elseif ($form_data['new_ContractStatus'] == 4) {
            $new_type = 100000001;
        }

        if ($new_type) {
            $post_data['Picklists'][] = array(
                'AttributeName' => 'new_type',
                'Value' => intval($new_type)
            );
        }

        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_first_installment',
            'Value' => intval($form_data['new_first_installment'])
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_term',
            'Value' => intval($form_data['new_term'])
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_another_bank_reason',
            'Value' => $form_data['new_another_bank_reason']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_credit_agricole_kredobank',
            'Value' => boolval($form_data['new_credit_agricole_kredobank'])
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_history',
            'Value' => $form_data['new_history']
        );

        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_bankid',
            'EntityName' => 'account',
            'EntityId' => $form_data['new_bankid']
        );

        $post_data["DateTimes"][] = array(
            'AttributeName' => "new_date_bank_application",
            "Value" => $form_data["new_date_bank_application"]
        );

        $post_data["DateTimes"][] = array(
            'AttributeName' => "new_date_guaranteeletter",
            "Value" => $form_data["new_GuaranteeLetter"]
        );

        $post_data["DateTimes"][] = array(
            'AttributeName' => "new_date_bank_transaction",
            "Value" => $form_data["new_PaymentRemittance"]
        );

        

	    //Страхування и Умови кредитування
        if ($form_data['new_insurance_company']) {
	        $post_data['Lookups'][] = array(
	            'AttributeName' => 'new_insurance_company',
	            'EntityName' => 'account',
	            'EntityId' => $form_data['new_insurance_company']
	        );
        }
        if ($form_data['new_rate']) {

            $fmt = numfmt_create( 'de_DE', NumberFormatter::DECIMAL );

	        $post_data['OtherAttributes'][] = array(
	            'AttributeName' => 'new_rate',
	            'Value' => numfmt_parse($fmt, $form_data['new_rate'])
	        );
        }

        if ($form_data['new_franchise']) {
            $post_data['Picklists'][] = array(
                'AttributeName' => 'new_franchise',
                'Value' => intval($form_data['new_franchise'])
            );
        }
        if ($form_data['new_credit_conditions']) {
            $post_data['Picklists'][] = array(
                'AttributeName' => 'new_credit_conditions',
                'Value' => intval($form_data['new_credit_conditions'])
            );
        }



        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/credit/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));
        $response = json_decode($CI->curl->execute(), true);
        log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/credit/create/' . " Options: " . json_encode($post_data) . ' Response: '  . json_encode($response) . ' User: ' . json_encode($CI->user_row));

        return $response;
        //return json_decode($CI->curl->execute(), true);
    }

    /*
     * WebUser authentication
     * POST /api/{organization}/webuser/authentication
     */
    public static function post_webuser_auth($phone, $password)
    {
        $post_data['New_mobilephone'] = $phone;
        $post_data['New_password'] = $password;
        $CI = &get_instance();
        //$CI->curl->_setHeader('');

        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/authentication/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');


     /*    $CI->curl->put('{
  "New_web_userId": "4ccc55f8-f8c3-e911-81aa-00155d1f050b",
  "New_name": "Отд. продаж",
  "New_lastname": "Начальник",
  "New_mobilephone": "380667868090",
  "New_email": "oi@800.com.ua",
  "New_login": "380667868090",
  "New_password": "12345",
  "New_has_acces_newtdrive": true,
  "New_head_sales": true,
  "New_has_acces_tdrive": true,
  "New_has_acces_inc": false,
  "New_responsible_for_quality": false,
  "new_access_sales": false,
  "New_access_check": false,
  "New_dealer": {
    "AccountId": "fdeffdd3-f6c3-e911-81aa-00155d1f050b"
  }
}
');*/
        $CI->curl->post(json_encode($post_data));
        $webuser_auth = json_decode($CI->curl->execute(), true);
        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/authentication/' . " Options: " . json_encode($post_data) . " Options: " . json_encode($webuser_auth));

        self::sync_webuser_data($webuser_auth);

        return $webuser_auth;
    }
    /*
     * Synchronization WebUser data at authentication
     * POST /api/{organization}/webuser/authentication
     */
    public static function sync_webuser_data($webuser_auth)
    {
        $webuser = $webuser_auth['Result']['Data'];

        if ($webuser == null) {
            return false;
        }

        $CI = &get_instance();
        $portal_user_query = $CI->db->where('phone', $webuser['New_mobilephone'])->get('users');
        $portal_user = $portal_user_query->row();


        if ($portal_user_query->num_rows()) {
            //Update user data
            $CI->db->where('phone', $webuser['New_mobilephone']);
            $CI->db->set('web_userid', $webuser['New_web_userId']);
            $CI->db->set('first_name', $webuser['New_name']);
            $CI->db->set('last_name', $webuser['New_lastname']);
            $CI->db->set('company', $webuser['New_dealer']);
            $CI->db->set('email', $webuser['New_email']);

            $CI->db->set('has_acces_newtdrive', $webuser['New_has_acces_newtdrive']);
            $CI->db->set('head_sales', $webuser['New_head_sales']);
            $CI->db->set('has_acces_tdrive', $webuser['New_has_acces_tdrive']);
            $CI->db->set('has_acces_inc', $webuser['New_has_acces_inc']);
            $CI->db->set('responsible_for_quality', $webuser['New_responsible_for_quality']);
            $CI->db->set('access_sales', $webuser['New_access_sales']);
            $CI->db->set('access_employees', $webuser['New_access_employees']);
            $CI->db->set('StateCode', $webuser['StateCode']);
            $CI->db->set('rrdi', $webuser['New_rrdi']);


            if (!$CI->ion_auth->bcrypt->verify($webuser['New_password'], $portal_user->password)) {
                $new = $CI->ion_auth->hash_password($webuser['New_password'], null);
                $CI->db->set('password', $new);
            }
            $result = $CI->db->update('users');
        } else {
            //Register new user
            $result = $CI->ion_auth->register(
                $webuser['New_mobilephone'],
                $webuser['New_password'],
                $webuser['New_email'],
                array(
                    'first_name' => $webuser['New_name'],
                    'last_name' => $webuser['New_lastname'],
                    'phone' => $webuser['New_mobilephone'],
                    'company' => $webuser["New_dealer"],
                    'position' => "4",
                    'web_userid' => $webuser['New_web_userId'],
                    'has_acces_newtdrive' => $webuser['New_has_acces_newtdrive'],
                    'head_sales' => $webuser['New_head_sales'],
                    'has_acces_tdrive' => $webuser['New_has_acces_tdrive'],
                    'has_acces_inc' => $webuser['New_has_acces_inc'],
                    'responsible_for_quality' => $webuser['New_responsible_for_quality'],
                    'access_sales' => $webuser['New_access_sales'],
                    'access_employees' => $webuser['New_access_employees'],
                    'StateCode' => $webuser['StateCode'],
                    'rrdi' => $webuser['New_rrdi']
                ),
                array(3)
            );

            //$CI->ion_auth->login($webuser['New_mobilephone'], $webuser['New_password'], false);
        }

    }


    /*
     * Synchronization WebUser data at authentication
     * POST /api/{organization}/webuser/authentication
     */
    public static function sync_webuser_data_by_phone($phone)
    {//$webuser_auth_id


        $CI = &get_instance();
        $portal_user_query = $CI->db->where('phone', $phone)->get('users');
        $portal_user = $portal_user_query->row();


        if ($portal_user_query->num_rows()) {
            $webuser_auth = self::get_webuser_by_id($portal_user->web_userid);
            $webuser = $webuser_auth['Result']['Data'];

            if ($webuser == null) {
                return false;
            }


            //Update user data
            $CI->db->where('phone', $webuser['New_mobilephone']);
            $CI->db->set('web_userid', $webuser['New_web_userId']);
            $CI->db->set('first_name', $webuser['New_name']);
            $CI->db->set('last_name', $webuser['New_lastname']);
            $CI->db->set('company', $webuser['New_dealer']);
            $CI->db->set('email', $webuser['New_email']);

            $CI->db->set('has_acces_newtdrive', $webuser['New_has_acces_newtdrive']);
            $CI->db->set('head_sales', $webuser['New_head_sales']);
            $CI->db->set('has_acces_tdrive', $webuser['New_has_acces_tdrive']);
            $CI->db->set('has_acces_inc', $webuser['New_has_acces_inc']);
            $CI->db->set('responsible_for_quality', $webuser['New_responsible_for_quality']);
            $CI->db->set('access_sales', $webuser['New_access_sales']);
            $CI->db->set('access_employees', $webuser['New_access_employees']);
            $CI->db->set('StateCode', $webuser['StateCode']);
            $CI->db->set('rrdi', $webuser['New_rrdi']);


            if (!$CI->ion_auth->bcrypt->verify($webuser['New_password'], $portal_user->password)) {
                $new = $CI->ion_auth->hash_password($webuser['New_password'], null);
                $CI->db->set('password', $new);
            }
            $result = $CI->db->update('users');
        }
    }
    /*
     * CRON Synchronization ALL WebUser data BY webuser_id
     */
    public static function sync_webuser_data_by_webuser_id($webuser)
    {//$webuser_auth_id
        $CI = &get_instance();
        $portal_user_query = $CI->db->where('web_userid', $webuser['New_web_userId'])->get('users');
        $portal_user = $portal_user_query->row();




 		/*if ($webuser['New_web_userId'] != "04688456-3cd5-e911-81ad-00155d1f050b") {
 			return;
 		} */
    	echo $webuser['New_web_userId']; 
    	echo $portal_user['phone']; 
    	echo $portal_user_query->num_rows();
    	echo "<br> ";

        if ($portal_user_query->num_rows()) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
            //$webuser_auth = self::get_webuser_by_id($portal_user->web_userid);
            //$webuser = $webuser_auth['Result']['Data'];

            //if ($webuser == null) {
            //    return false;
            //}
            //Update user data
            $CI->db->where('web_userid', $webuser['New_web_userId']);
            $CI->db->set('phone', $webuser['New_mobilephone']);
            $CI->db->set('first_name', $webuser['New_name']);
            $CI->db->set('last_name', $webuser['New_lastname']);
            $CI->db->set('company', $webuser['New_dealer']);
            $CI->db->set('email', $webuser['New_email']);

            $CI->db->set('has_acces_newtdrive', $webuser['New_has_acces_newtdrive']);
            $CI->db->set('head_sales', $webuser['New_head_sales']);
            $CI->db->set('has_acces_tdrive', $webuser['New_has_acces_tdrive']);
            $CI->db->set('has_acces_inc', $webuser['New_has_acces_inc']);
            $CI->db->set('responsible_for_quality', $webuser['New_responsible_for_quality']);
            $CI->db->set('access_sales', $webuser['New_access_sales']);
            $CI->db->set('access_employees', $webuser['New_access_employees']);
            $CI->db->set('StateCode', $webuser['StateCode']);
            $CI->db->set('rrdi', $webuser['New_rrdi']);


            if (!$CI->ion_auth->bcrypt->verify($webuser['New_password'], $portal_user->password)) {
                $new = $CI->ion_auth->hash_password($webuser['New_password'], null);
                $CI->db->set('password', $new);
            }
            $result = $CI->db->update('users');
        }
    }
    /*
     * Request planning Create
     * POST /api/{organization}/planning/task/create
     */
    public static function post_request_planning_create($form_data)
    {
        $post_data['ScheduledStart'] = strtotime($form_data['task_action_date']);
        $post_data['New_activity_type'] = $form_data["task_action_type"];
        $post_data['Description'] = $form_data["task_action_addinfo"];
        $post_data['New_status'] = $form_data["task_action_status"];
        $post_data['New_user_webportal']['New_web_userId'] = $form_data["task_action_manager"];
        $post_data['New_registration_test_drive']['New_registration_tdriveId'] = $form_data["registration_tdriveId"];

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/planning/task/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));
        log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/planning/task/create/' . " Options: " . json_encode($post_data), 'Response'  . json_encode($response) . 'User' . json_encode($CI->user_row));

        return json_decode($CI->curl->execute(), true);
    }

    // --- INCIDENTS SECTION ---

    /*
     * Incident answer Create
     * POST /api/{organization}/incidents/answer/create
     */
    public static function post_incident_answer_create($form_data)
    {
        $post_data['New_answer'] = $form_data['answer_text'];
        $post_data['New_incident']['IncidentId'] = $form_data["IncidentId"];
        $post_data['new_who_leave_answer']['IncidentId'] = $form_data["IncidentId"];

	    $post_data['New_who_leave_answer']['New_web_userId'] = $form_data['New_who_leave_answer']['New_web_userId'];
		$post_data['New_who_leave_answer']['New_name'] = $form_data['New_who_leave_answer']['New_name'];
		$post_data['New_who_leave_answer']['New_lastname'] = $form_data['New_who_leave_answer']['New_lastname'];


        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents/answers/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

//var_dump(json_encode($post_data)); die;
        $CI->curl->post(json_encode($post_data));

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents/answer/create/' . " Options: " . json_encode($post_data));

        return json_decode($CI->curl->execute(), true);
    }




    // --- SUVEHICLE SECTION ---
    /*
     * SUVEHICLE Create
     * POST /api/{organization}//create
     */
    public static function post_suvehicle_create($form_data)
    {

        $post_data = array();
        $post_data['Id'] = $form_data['Su_vehicleId'];
        $post_data['EntityName'] = 'su_vehicle';

        //new_typeofcar
        $post_data['Picklists'][] = array('AttributeName' => 'new_typeofcar', 'Value' => $form_data["new_typeofcar"]);

        //$post_data['Picklists']['AttributeName'] = 'new_typeofcar';
        //$post_data['Picklists']['Value'] = $form_data["new_typeofcar"];

        //su_accountvehicle2 AND su_carcomplvehicle
        $post_data['Lookups'][] = array(
            'AttributeName' => 'su_accountvehicle2',
            'EntityName' => 'account',
            'EntityId' => $form_data['su_accountvehicle2'],
        );
        $post_data['Lookups'][] = array(
            'AttributeName' => 'su_carcomplvehicle',
            'EntityName' => 'su_carcompl',
            'EntityId' => $form_data['su_carcomplvehicle'],
        );

        //DateTimes: new_test_entry AND su_carcomplvehicle
        $post_data['DateTimes'][] = array(
            'AttributeName' => 'new_test_entry',
            'Value' => $form_data['new_test_entry'],
        );
        $post_data['DateTimes'][] = array(
            'AttributeName' => 'new_test_withdrawal',
            'Value' => $form_data['new_test_withdrawal'],
        );

        //OtherAttributes: new_description AND su_name
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_description',
            'Value' => $form_data['new_description'],
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'su_name',
            'Value' => $form_data['su_name'],
        );



        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/suvehicle/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents/answer/create/' . " Options: " . json_encode($post_data));

        return json_decode($CI->curl->execute(), true);
    }


    // --- CONTRACT SECTION ---
    /*
     * CONTRACT Create
     * POST /api/{organization}//create
     */
    public static function post_contract_create($form_data)
    {
        /*
               * {
"EntityName": "new_offer",
"Picklists": [],
"Lookups": [
{
"AttributeName": "new_registration_id",
"EntityName": "new_registration_tdrive",
"EntityId": "c308fca8-dc1d-eb11-81c0-00155d1f050b"
},

],
"DateTimes": [],
"OtherAttributes": [  ]
}
*/
        $post_data = array();
        $post_data['EntityName'] = 'new_offer';

        //new_warehouse
        $post_data['Picklists'] = array();

        //new_warehouse
        if ($form_data["new_warehouse"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_warehouse', 'Value' => $form_data["new_warehouse"]);
        }
        if ($form_data["new_ContractStatus"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_contractstatus', 'Value' => $form_data["new_ContractStatus"]);
        }
        if ($form_data["new_VehicleStatus"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_vehiclestatus', 'Value' => $form_data["new_VehicleStatus"]);
        }
        if ($form_data["new_CustomsClearance"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_customsclearance', 'Value' => $form_data["new_CustomsClearance"]);
        }
        if ($form_data["new_CarLocation"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_carlocation', 'Value' => $form_data["new_CarLocation"]);
        }


        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_registration_id',
            'EntityName' => 'new_registration_tdrive',
            'EntityId' => $form_data['new_registration_id'],
        );

        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_modelversionid',
            'EntityName' => 'su_carcompl',
            'EntityId' => $form_data['parentmodels'],
        );

        //new_warehouse

        if ($form_data['new_OrderNumber'] != "") {
            $order_create_data['EntityName'] = 'new_order';
            $order_create_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_name',
                'Value' => $form_data['new_OrderNumber']
            );
            $CI = &get_instance();
            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/offer/create/');
            $CI->curl->http_header('Accept','application/json');
            $CI->curl->http_header('Content-Type','application/json');

            $CI->curl->post(json_encode($order_create_data));
            $order_create_result = json_decode($CI->curl->execute(), true);
            if($order_create_result['Result']['Data']['Id'] != "") {
                $post_data['Lookups'][] = array(
                    'AttributeName' => 'new_order_id',
                    'EntityName' => 'new_order',
                    'EntityId' => $order_create_result['Result']['Data']['Id']
                );
            }
        }

        $post_data['DateTimes'][] = array( );

        $post_data['OtherAttributes'][] = array();


        if($form_data['new_ContractDate'] != null) {
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_contractdate',
                'Value' => $form_data['new_ContractDate'],
            );
        }

        if($form_data['new_GuaranteeLetter'] != null) {
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_guaranteeletter',
                'Value' => $form_data['new_GuaranteeLetter'],
            );
        }


        if($form_data['new_PaymentRemittance'] != null) {
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_paymentremittance',
                'Value' => $form_data['new_PaymentRemittance'],
            );
        }


        if($form_data['new_ArrivalToDealer'] != null) {
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_arrivaltodealer',
                'Value' => $form_data['new_ArrivalToDealer'],
            );
        }


        if($form_data['new_ConfidentDelivery'] != null) {
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_confidentdelivery',
                'Value' => $form_data['new_ConfidentDelivery'],
            );
        }


        if($form_data['new_PostponedDelivery'] != null) {
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_postponeddelivery',
                'Value' => $form_data['new_PostponedDelivery'],
            );
        }


        if($form_data['new_Delivered'] != null) {
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_delivered',
                'Value' => $form_data['new_Delivered'],
            );
        }


        if($form_data['New_pricelist_price'] != null) {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_pricelist_price',
                'Value' => intval($form_data['New_pricelist_price']),
                'Value' => intval($form_data['New_invoice_price']),
            );
        }

        if($form_data['New_invoice_price'] != null) {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_invoice_price',
                'Value' => intval($form_data['New_pricelist_price']),
                'Value' => intval($form_data['New_invoice_price']),
            );
        }

        //OtherAttributes:
        /*
 * new__vin
new_ordernumber
new_model
new_competitorcriteria
new_competitornames
new_comments
 */
        if ($form_data['new_VIN'] != "") {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new__vin',
                'Value' => $form_data['new_VIN'],
            );
        }
        if ($form_data['new_OrderNumber'] != "") {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_ordernumber',
                'Value' => $form_data['new_OrderNumber'],
            );
        }
        if ($form_data['new_Model'] != "") {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_model',
                'Value' => $form_data['new_Model'],
            );
        }
        if ($form_data['new_CompetitorCriteria'] != "") {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_competitorcriteria',
                'Value' => $form_data['new_CompetitorCriteria'],
            );
        }
        if ($form_data['new_CompetitorNames'] != "") {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_competitornames',
                'Value' => $form_data['new_CompetitorNames'],
            );
        }
        if ($form_data['new_Comments'] != "") {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_comments',
                'Value' => $form_data['new_Comments'],
            );
        }


        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/offer/create/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));

        log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/offer/create/' . " Options: " . json_encode($post_data));

        return json_decode($CI->curl->execute(), true);
    }



/*
 * Edit PUT methods
 */
    public static function put_testdrive_edit($form_data)
    {
        $put_data['New_registration_tdriveId'] = $form_data['registration_tdriveId'];

        $crm_data = self::get_testdrives(null, $form_data['registration_tdriveId']);
        $crm_data = $crm_data['Result']['Data'];


        //Check CRM values
        if ($form_data['testdrive_status'] != $crm_data['New_process_status']) {
            $put_data['New_process_status'] = $form_data['testdrive_status'];
        }

        if(strtotime($form_data['testdrive_date']) != $crm_data['New_campaign_data_final']) {
            $put_data['New_campaign_data_final'] = strtotime($form_data['testdrive_date']);
        }

        if (isset($form_data['request_type']) && $form_data['request_type'] != "" && $form_data['request_type'] != $crm_data['New_recordtype']) {
            $put_data['New_recordtype'] = $form_data['request_type'];
        }

        if (isset($form_data['confirmation_presence_td']) && $form_data['confirmation_presence_td'] != "" && $form_data['confirmation_presence_td'] != $crm_data['New_confirmation_presence_td']) {
            $put_data['New_confirmation_presence_td'] = $form_data['confirmation_presence_td'];
        }

        if (isset($form_data['reason_for_stop_work']) && $form_data['reason_for_stop_work'] != "" && $form_data['reason_for_stop_work'] != $crm_data['New_reason_for_stop_work']) {
            $put_data['New_reason_for_stop_work'] = $form_data['reason_for_stop_work'];
        }

        if (isset($form_data['testdrive_addinfo']) && $form_data['testdrive_addinfo'] != $crm_data['New_comment']) {
            $put_data['New_comment'] = $form_data['testdrive_addinfo'];
        }

        if (isset($form_data['sale_vin_code']) && $form_data['sale_vin_code'] != "") {
            $put_data['New_vinkod'] = $form_data['sale_vin_code'];
        } else {
            $put_data['New_vinkod'] = null;
        }


        if (isset($form_data['why_did_not_come_for_td']) && $form_data['why_did_not_come_for_td'] != "") {
            $put_data['New_why_did_not_come_for_td'] = $form_data['why_did_not_come_for_td'];
        } else {
            $put_data['New_why_did_not_come_for_td'] = null;
        }

        if (isset($form_data['New_creditid']) && $form_data['New_creditid'] != "") {
            $put_data['New_creditid'] = $form_data['New_creditid'];
        } else {
            $put_data['New_creditid'] = null;
        }

        if (isset($form_data['New_offerid']) && $form_data['New_offerid'] != "") {
            $put_data['New_offerid'] = $form_data['New_offerid'];
        } else {
            $put_data['New_offerid'] = null;
        }



/*
        if (isset($form_data['New_krugobzvona']) && $form_data['New_krugobzvona'] != "") {
            $put_data['New_krugobzvona'] = $form_data['New_krugobzvona'];
        } else {
            $put_data['New_krugobzvona'] = null;
        }

        if (isset($form_data['actualization_result']) && $form_data['actualization_result'] != "") {
            $put_data['New_actualization_result'] = $form_data['actualization_result'];
        } elseif (isset($form_data['New_actualization_result']) && $form_data['New_actualization_result'] != "") {
            $put_data['New_actualization_result'] = $form_data['New_actualization_result'];
        } else {
            $put_data['New_actualization_result'] = null;
        }

        if (isset($form_data['New_call_result']) && $form_data['New_call_result'] != "") {
            $put_data['New_call_result'] = $form_data['New_call_result'];
        } else {
            $put_data['New_call_result'] = null;
        }
        /* $put_data['New_call_result'] = null;
         $put_data['New_actualization_result'] = null;*/

        if (isset($form_data['other_failure']) && $form_data['other_failure'] != "") {
            $put_data['New_other_failure'] = $form_data['other_failure'];
        } elseif (isset($form_data['New_other_failure']) && $form_data['New_other_failure'] != "") {
            $put_data['New_other_failure'] = $form_data['New_other_failure'];
        } else {
            $put_data['New_other_failure'] = null;
        }

        if (isset($form_data['time_to_callback']) && $form_data['time_to_callback'] != "") {
            $put_data['New_time_to_callback'] = strtotime($form_data['time_to_callback']);
        } elseif (isset($form_data['New_time_to_callback']) && $form_data['New_time_to_callback'] != "") {
            $put_data['New_time_to_callback'] = $form_data['New_time_to_callback'];
        } else {
            $put_data['New_time_to_callback'] = null;
        }

        if (isset($form_data['return_to_diller']) && $form_data['return_to_diller'] != "") {
            $put_data['New_return_to_diller'] = true;
        } elseif (isset($form_data['New_return_to_diller']) && $form_data['New_return_to_diller'] != "") {
            $put_data['New_return_to_diller'] = true;
        } else {
            $put_data['New_return_to_diller'] = false;
        }


        if ($form_data['New_lead_id']) {
            $put_data['New_lead_id'] = array(
                'LeadId' => $form_data['New_lead_id'],
                'FirstName' => $form_data['client_name'],
                'LastName' => $form_data['client_surname'],
                'MiddleName' => $form_data['client_middle'],
                'MobilePhone' => $form_data['client_phone'],
                'EMailAddress1' => $form_data['client_email'],
                'New_channel' => (isset($crm_data['New_channel']) && $crm_data['New_channel'] != null) ? $crm_data['New_channel'] : 3
            );
        } else {
            $put_data['New_lead_id'] = array(
                'LeadId' => $form_data['New_lead_id'],
                'FirstName' => $form_data['client_name'],
                'MiddleName' => $form_data['client_middle'],
                'LastName' => $form_data['client_surname'],
                'MobilePhone' => $form_data['client_phone'],
                'EMailAddress1' => $form_data['client_email'],
                //'New_channel' => 3
            );
        }
        

        if (isset($form_data['client_birthday']) && $form_data['client_birthday'] != "" && strtotime($form_data['client_birthday'])) {
            $put_data['New_lead_id']['Su_birthday'] = strtotime($form_data['client_birthday']);
        } else {
            $put_data['New_lead_id']['Su_birthday'] = null;
        }


        if (isset($form_data['client_new_sex'])) {
            $put_data['New_lead_id']['New_sex'] = intval($form_data['client_new_sex']);
        } else {
            $put_data['New_lead_id']['New_sex'] = null;
        }

        if (isset($form_data['client_address1_city'])) {
            $put_data['New_lead_id']['Address1_city'] = $form_data['client_address1_city'];
        } else {
            $put_data['New_lead_id']['Address1_city'] = null;
        }

        if (isset($form_data['client_new_use_of_data'])) {
            $put_data['New_lead_id']['New_use_of_data'] = $form_data['client_new_use_of_data'];
        } else {
            $put_data['New_lead_id']['New_use_of_data'] = null;
        }


        if (isset($form_data['credit']) && $form_data['credit'] != "") {
            $put_data['New_credit'] = boolval($form_data['credit']);
        } else {
            $put_data['New_credit'] = null;
        }

        // new fields
        if (isset($form_data['new_bank']) && $form_data['new_bank'] != "") {
            $put_data['New_bank'] = $form_data['new_bank'];
        } else {
            $put_data['New_bank'] = null;
        }

        if (isset($form_data['new_first_installment']) && $form_data['new_first_installment'] != "") {
            $put_data['New_first_installment'] = $form_data['new_first_installment'];
        } else {
            $put_data['New_first_installment'] = null;
        }

        if (isset($form_data['new_repayment_type']) && $form_data['new_repayment_type'] != "") {
            $put_data['New_repayment_type'] = $form_data['new_repayment_type'];
        } else {
            $put_data['New_repayment_type'] = null;
        }

        if (isset($form_data['new_term']) && $form_data['new_term'] != "") {
            $put_data['New_term'] = $form_data['new_term'];
        } else {
            $put_data['New_term'] = null;
        }

        if (isset($form_data['new_modelversion']) && $form_data['new_modelversion'] != "") {
            $put_data['New_modelversion'] = $form_data['new_modelversion'];
        } else {
            $put_data['New_modelversion'] = null;
        }

        // 

        if (isset($form_data['new_set_td']) && $form_data['new_set_td'] != "") {
            $put_data['New_set_td'] = true;
        } elseif ($form_data['testdrive_date'] != "") {
            $put_data['New_set_td'] = true;
        } else {
            $put_data['New_set_td'] = false;
            $put_data['New_set_td_date'] = false;

        }
        if (isset($form_data['new_completed_td']) && $form_data['new_completed_td'] != "") {
            $put_data['New_completed_td'] = true;
            //$put_data['New_confirmation_presence_td'] = 1;
        } elseif ($form_data['confirmation_presence_td'] == 1) {
            $put_data['New_completed_td'] = true;
        } else {
            $put_data['New_completed_td'] = false;
            $put_data['New_completed_td_date'] = false;
        }
        if (isset($form_data['new_sale_agreement']) && $form_data['new_sale_agreement'] != "") {
            $put_data['New_sale_agreement'] = true;
        } else {
            $put_data['New_sale_agreement'] = false;
            $put_data['New_sale_agreement_date'] = false;
        }
        if (isset($form_data['new_run_contract']) && $form_data['new_run_contract'] != "") {
            $put_data['New_run_contract'] = true;
        } else {
            $put_data['New_run_contract'] = false;
            $put_data['New_run_contract_date'] = false;
        }
        if (isset($form_data['new_exercise_planned_car']) && $form_data['new_exercise_planned_car'] != "") {
            $put_data['New_exercise_planned_car'] = true;
        } else {
            $put_data['New_exercise_planned_car'] = false;
            $put_data['New_exercise_planned_car_date'] = false;
        }

        //Step date setting rules
        if($put_data['New_set_td'] && !$put_data['New_completed_td'] &&  !$put_data['New_sale_agreement'] &&  !$put_data['New_run_contract'] &&  !$put_data['New_exercise_planned_car'] ) {
            $put_data['New_set_td_date'] = time();
        }
        if($put_data['New_completed_td'] &&  !$put_data['New_sale_agreement'] &&  !$put_data['New_run_contract'] &&  !$put_data['New_exercise_planned_car'] ) {
            $put_data['New_completed_td_date'] = time();
        }
        if($put_data['New_sale_agreement'] &&  !$put_data['New_run_contract'] &&  !$put_data['New_exercise_planned_car'] ) {
            $put_data['New_sale_agreement_date'] = time();
        }
        if($put_data['New_run_contract'] &&  !$put_data['New_exercise_planned_car'] ) {
            $put_data['New_run_contract_date'] = time();
        }
        if($put_data['New_exercise_planned_car'] ) {
            $put_data['New_exercise_planned_car_date'] = time();
        }


        $put_data['New_model_for_td']['Su_carcomplId'] = $form_data['car_id'];
        $put_data['New_salon']['AccountId'] = $form_data['dealer_center_id'];

        if (isset($form_data['webuserid']) && $form_data['webuserid'] != "") {
            $put_data['New_web_user']['New_web_userId'] = $form_data['webuserid'];
        } else {
            $put_data['New_web_user']['New_web_userId'] = $form_data['testdrive_manager'];
        }

        if ( isset($form_data['New_processing_speed']) && $form_data['New_processing_speed'] !== null) {
            $put_data['New_processing_speed'] = $form_data['New_processing_speed'];
        }


        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/update/' . " Options: " . json_encode($put_data));

//        //vardump(array($form_data, json_encode($put_data),$put_data, $crm_data));
        //vardump(json_encode($put_data));
//var_dump(json_encode($put_data)); die;
        $CI->curl->put(json_encode($put_data));
        $response = $CI->curl->execute();
        log_message('info', json_encode(array('Method' => __METHOD__, 'Link' => 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/registrations/update/', 'Params' => $put_data, 'Response'=> $response, 'User'=> $CI->user_row), JSON_UNESCAPED_UNICODE));

        return json_decode($response, true);
    }

/*
 * Edit PUT methods
 */
    public static function put_testdrive_edit_change_tdate($form_data)
    {
        $put_data['New_registration_tdriveId'] = $form_data['registration_tdriveId'];

        $crm_data = self::get_testdrives(null, $form_data['registration_tdriveId']);
        $crm_data = $crm_data['Result']['Data'];


        if(strtotime($form_data['testdrive_date']) != $crm_data['New_campaign_data_final']) {
            $put_data['New_campaign_data_final'] = strtotime($form_data['testdrive_date']);
        }

        $put_data['New_lead_id']['LeadId'] = $crm_data['LeadId'];
        $put_data['New_model_for_td']['Su_carcomplId'] = $crm_data['Su_carcomplId'];
        $put_data['New_salon']['AccountId'] = $crm_data['AccountId'];
        $put_data['New_web_user']['New_web_userId'] = $crm_data['New_web_user'];

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/update/' . " Options: " . json_encode($put_data));

        $CI->curl->put(json_encode($put_data));
        return json_decode($CI->curl->execute(), true);
    }

    public static function put_testdrive_edit_change_step_status($registration_tdriveId, $step_name, $step_status)
    {
        $put_data['New_registration_tdriveId'] = $registration_tdriveId;

        $crm_data = self::get_testdrives(null, $registration_tdriveId);
        $crm_data = $crm_data['Result']['Data'];


        $step_name = str_replace('_inrow', '', $step_name);

        $set_step_status = true;
        $current_status = $crm_data[$step_name];
/*
        if ($current_status == true) {
            $set_step_status = false;
        } elseif ($current_status == false || $current_status == null) {
            $set_step_status = true;
        }*/

        if ($step_status == "true") {
            $set_step_status = true;
        } elseif ($step_status == "false") {
            $set_step_status = false;
        }

        $put_data[$step_name] = $set_step_status;

        if ($put_data["new_completed_td"] == true) {
            $put_data['New_confirmation_presence_td'] = 1;
        }

        if ($put_data["new_sale_agreement"] == true) {
            if ($crm_data['New_credit'] == null) {
                return false;
            }
        }

        //Дата тест-драйва еще не наступила.
        //FIXME: Status
        if ($step_name == "new_completed_td") {
            if ($crm_data['New_campaign_data_final'] == "" || $crm_data['New_campaign_data_final'] > time()) {
                return 2;
            }
        }




        $put_data['New_lead_id']['LeadId'] = $crm_data['LeadId'];
        $put_data['New_model_for_td']['Su_carcomplId'] = $crm_data['Su_carcomplId'];
        $put_data['New_salon']['AccountId'] = $crm_data['AccountId'];
        $put_data['New_web_user']['New_web_userId'] = $crm_data['New_web_user'];

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/update/' . " Options: " . json_encode($put_data));

        $CI->curl->put(json_encode($put_data));
        return json_decode($CI->curl->execute(), true);
    }


    public static function put_testdrive_edit_change_fb($registration_tdriveId, $New_fb = false)
    {
        /*{
  "New_web_user": { "New_web_userId": "a8492bbb-c4f5-e911-81ad-00155d1f050b" },
  "New_lead_id": { "LeadId": "ea5c21c9-4d58-ec11-81e6-00155d1f050b" },
  "New_model_for_td": { "Su_carcomplId": "1756052e-7f03-e411-a1d6-00e081beb934" },
  "New_salon": { "AccountId": "a79bcc45-1813-e811-aa8c-00155d1f0716"},
  "New_registration_tdriveId": "ed5c21c9-4d58-ec11-81e6-00155d1f050b",
  "New_fb": true }*/

        $put_data['New_registration_tdriveId'] = $registration_tdriveId;

        $crm_data = self::get_testdrives(null, $registration_tdriveId);
        $crm_data = $crm_data['Result']['Data'];

        $put_data['New_fb'] = $New_fb;

        $put_data['New_lead_id']['LeadId'] = $crm_data['LeadId'];
        $put_data['New_model_for_td']['Su_carcomplId'] = $crm_data['Su_carcomplId'];
        $put_data['New_salon']['AccountId'] = $crm_data['AccountId'];
        $put_data['New_web_user']['New_web_userId'] = $crm_data['New_web_user'];

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/update/' . " Options: " . json_encode($put_data));

        $CI->curl->put(json_encode($put_data));
        return json_decode($CI->curl->execute(), true);
    }



    public static function put_actualization_edit($form_data)
    {
        $put_data['New_registration_tdriveId'] = $form_data['registration_tdriveId'];

        $crm_data = self::get_testdrives(null, $form_data['registration_tdriveId']);
        $crm_data = $crm_data['Result']['Data'];

        if ($form_data['testdrive_status'] != $crm_data['New_process_status']) {
            $put_data['New_process_status'] = $form_data['testdrive_status'];
        }

        if(strtotime($form_data['testdrive_date']) != $crm_data['New_campaign_data_final']) {
            $put_data['New_campaign_data_final'] = strtotime($form_data['testdrive_date']);
        }

        if (isset($form_data['recordtype']) && $form_data['recordtype'] != "" && $form_data['recordtype'] != $crm_data['New_recordtype']) {
            $put_data['New_recordtype'] = $form_data['recordtype'];
        }

        if (isset($form_data['confirmation_presence_td']) && $form_data['confirmation_presence_td'] != "" && $form_data['confirmation_presence_td'] != $crm_data['New_confirmation_presence_td']) {
            $put_data['New_confirmation_presence_td'] = $form_data['confirmation_presence_td'];
        }

        if (isset($form_data['reason_for_stop_work']) && $form_data['reason_for_stop_work'] != "" && $form_data['reason_for_stop_work'] != $crm_data['New_reason_for_stop_work']) {
            $put_data['New_reason_for_stop_work'] = $form_data['reason_for_stop_work'];
        }

        if (isset($form_data['comment']) && $form_data['comment'] != $crm_data['New_comment']) {
            $put_data['New_comment'] = $form_data['comment'];
        }

        // Actualization params rewrite fix
        if (isset($form_data['krugobzvona']) && $form_data['krugobzvona'] != "" && $form_data['krugobzvona'] != $crm_data['New_krugobzvona']) {
            $put_data['New_krugobzvona'] = $form_data['krugobzvona'];
        }

        if (isset($form_data['call_result']) && $form_data['call_result'] != "" && $form_data['call_result'] != $crm_data['New_call_result']) {
            $put_data['New_call_result'] = $form_data['call_result'];
        }

        if (isset($form_data['actualization_result']) && $form_data['actualization_result'] != "" && $form_data['actualization_result'] != $crm_data['New_actualization_result']) {
            $put_data['New_actualization_result'] = $form_data['actualization_result'];
        }
        if (isset($form_data['reason_not_relevant']) && $form_data['reason_not_relevant'] != "" && $form_data['reason_not_relevant'] != $crm_data['New_reason_not_relevant']) {
            $put_data['New_reason_not_relevant'] = $form_data['reason_not_relevant'];
        }

        if (isset($form_data['other_failure']) && $form_data['other_failure'] != "" && $form_data['other_failure'] != $crm_data['New_other_failure']) {
            $put_data['New_other_failure'] = $form_data['other_failure'];
        }

        if (isset($form_data['time_to_callback']) && $form_data['time_to_callback'] != "" && $form_data['time_to_callback'] != $crm_data['New_time_to_callback']) {
            $put_data['New_time_to_callback'] = strtotime($form_data['time_to_callback']);
        }



        //$put_data['New_process_status'] = $form_data['testdrive_status'];
        //$put_data['New_campaign_data_final'] = strtotime($form_data['testdrive_date']);
        //$put_data['New_campaign_data_final'] = $form_data['testdrive_date'];

/*        if (isset($form_data['confirmation_presence_td']) && $form_data['confirmation_presence_td'] != "" && in_array($form_data['confirmation_presence_td'], array(1,2))) {
            $put_data['New_confirmation_presence_td'] = $form_data['confirmation_presence_td'];
        } else {
            $put_data['New_confirmation_presence_td'] = null;
        }*/
/*
        $put_data['New_comment'] = $form_data['comment'];
        $put_data['New_reason_for_stop_work'] = null;*/


        if (isset($form_data['return_to_diller']) && $form_data['return_to_diller'] != "") {
            $put_data['New_return_to_diller'] = true;
        } else {
            $put_data['New_return_to_diller'] = false;
        }

        $put_data['New_lead_id'] = array(
            'LeadId' => ($form_data['lead_id'] != null) ? $form_data['lead_id'] : $crm_data['LeadId'],
            'FirstName' => $form_data['client_name'],
            'LastName' => $form_data['client_surname'],
            'MobilePhone' => $form_data['client_phone'],
            'EMailAddress1' => $form_data['client_email'],
            'New_channel' => (isset($crm_data['New_channel']) && $crm_data['New_channel'] != null) ? $crm_data['New_channel'] : 3
        );
        $put_data['New_model_for_td']['Su_carcomplId'] = $form_data['car_id'];

        /*
        if (is_array($form_data['New_lead_id'])) {
            $put_data['New_lead_id'] = array(
                'LeadId' => $form_data['LeadId'],
                'FirstName' => $form_data['client_name'],
                'LastName' => $form_data['client_surname'],
                'MobilePhone' => $form_data['client_phone'],
                'EMailAddress1' => $form_data['client_email'],
                'New_channel' => (isset($crm_data['New_channel']) && $crm_data['New_channel'] != null) ? $crm_data['New_channel'] : 3
            );
        } else {
            $put_data['New_lead_id'] = array(
                'LeadId' => $form_data['New_lead_id'],
                'FirstName' => $form_data['client_name'],
                'LastName' => $form_data['client_surname'],
                'MobilePhone' => $form_data['client_phone'],
                'EMailAddress1' => $form_data['client_email'],
                'New_channel' => 3
            );
        }
*/

        $put_data['New_salon']['AccountId'] = $form_data['dealer_center_id'];
        $put_data['New_dealer']['AccountId'] = $form_data['dealer_center_id'];

        if (isset($form_data['testdrive_manager']) && $form_data['testdrive_manager'] != "") {
            $put_data['New_web_user']['New_web_userId'] = $form_data['testdrive_manager'];
        } elseif (isset($form_data['webuserid']) && $form_data['webuserid'] != "") {
            $put_data['New_web_user']['New_web_userId'] = $form_data['webuserid'];
        } elseif (isset($form_data['web_userid']) && $form_data['web_userid'] != "") {
            $put_data['New_web_user']['New_web_userId'] = $form_data['web_userid'];
        } else {
            $put_data['New_web_user']['New_web_userId'] = $form_data['testdrive_manager'];
        }
/*        echo "<pre>";
        var_dump($form_data);
        var_dump($put_data['New_web_user']['New_web_userId']);
        var_dump(isset($form_data['testdrive_manager']) && $form_data['testdrive_manager'] != "");
        var_dump(isset($form_data['webuserid']) && $form_data['webuserid'] != "");
        var_dump(isset($form_data['web_userid']) && $form_data['web_userid'] != "");
        echo "</pre>";
        die;*/

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        //$CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/registrations/update/');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/actualization/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->put(json_encode($put_data));
//aa746b42-7903-e411-a1d6-00e081beb934

        /*{
   "New_registration_tdriveId":"512e25ea-2e05-ea11-81ae-00155d1f050b",
   "New_recordtype":"3",
   "New_confirmation_presence_td":null,
   "New_comment":"3wdsa",
   "New_reason_for_stop_work":null,
   "New_krugobzvona":"100000001",
   "New_call_result":"100000002",
   "New_actualization_result":null,
   "New_other_failure":null,
   "New_time_to_callback":null,
   "New_return_to_diller":false,
   "New_lead_id":{
      "LeadId":"4e2e25ea-2e05-ea11-81ae-00155d1f050b",
      "FirstName":"\u0410\u043d\u0434\u0440\u0435\u0439",
      "LastName":"\u0415\u0432\u0442\u0443\u0448\u0435\u043d\u043a\u043e",
      "MobilePhone":"380683971346",
      "EMailAddress1":"andrei_ika@i.ua",
      "New_channel":20
   },
   "New_model_for_td":{
      "Su_carcomplId":"1756052e-7f03-e411-a1d6-00e081beb{
   "New_registration_tdriveId":"512e25ea-2e05-ea11-81ae-00155d1f050b",
   "New_recordtype":"3",
   "New_confirmation_presence_td":null,
   "New_comment":"3wdsa",
   "New_reason_for_stop_work":null,
   "New_krugobzvona":"100000001",
   "New_call_result":"100000002",
   "New_actualization_result":null,
   "New_other_failure":null,
   "New_time_to_callback":null,
   "New_return_to_diller":false,
   "New_lead_id":{
      "LeadId":"4e2e25ea-2e05-ea11-81ae-00155d1f050b",
      "FirstName":"\u0410\u043d\u0434\u0440\u0435\u0439",
      "LastName":"\u0415\u0432\u0442\u0443\u0448\u0435\u043d\u043a\u043e",
      "MobilePhone":"380683971346",
      "EMailAddress1":"andrei_ika@i.ua",
      "New_channel":20
   },
   "New_model_for_td":{
      "Su_carcomplId":"1756052e-7f03-e411-a1d6-00e081beb934"
   },
   "New_salon":{
      "AccountId":"aa746b42-7903-e411-a1d6-00e081beb934"
   },
   "New_dealer":{
      "AccountId":"aa746b42-7903-e411-a1d6-00e081beb934"
   },
   "New_web_user":{
      "New_web_userId":null
   },
   "New_process_status":"100000000",
   "New_campaign_data_final":null
}934"
   },
   "New_salon":{
      "AccountId":"aa746b42-7903-e411-a1d6-00e081beb934"
   },
   "New_dealer":{
      "AccountId":"aa746b42-7903-e411-a1d6-00e081beb934"
   },
   "New_web_user":{
      "New_web_userId":null
   },
   "New_process_status":"100000000",
   "New_campaign_data_final":null
}*/
/*        if ($_SERVER['REMOTE_ADDR'] == "37.52.197.162") {
            echo "<pre>";
            var_dump(json_encode($put_data));
            echo "</pre>";
            die;
        }*/

        $response = $CI->curl->execute();
        log_message('info', json_encode(array('Method' => __METHOD__, 'Link' => 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/actualization/update/', 'Params' => $put_data, 'Response'=> $response), JSON_UNESCAPED_UNICODE));


        $CI = &get_instance();
        $crm_response = json_decode($response, true);
        
        if ($crm_response['Status'] == "OK" && $crm_response["Result"]["Data"] == "Entity was updated"){
            return true;
        } else {
            $CI->session->set_flashdata('message', "К сожалению, не удалось сохранить запись. Пожалуйста, повторите попытку");
            return false;
        }
        //return json_decode($response, true);
    }

   public static function put_planning_edit($form_data)
    {

        $crm_data = self::get_planning_task($form_data['ActivityId']);
        $crm_data = $crm_data['Result']['Data'];

        $put_data['ActivityId'] = $form_data['ActivityId'];

        if ($form_data['activity_type'] != $crm_data['New_activity_type']) {
            $put_data['New_activity_type'] = $form_data['activity_type'];
        }

        if ($form_data['task_status'] != $crm_data['New_status']) {
            $put_data['New_status'] = $form_data['task_status'];
        }

        if ($form_data['task_date'] != $crm_data['ScheduledStart']) {
            $put_data['ScheduledStart'] = $form_data['task_date'];
        }

        if ($form_data['task_addinfo'] != $crm_data['Description']) {
            $put_data['Description'] = $form_data['task_addinfo'];
        }

        $put_data['New_user_webportal']['New_web_userId'] = $form_data['task_manager'];
        $put_data['New_registration_test_drive']['New_registration_tdriveId'] = $form_data['registration_tdriveId'];



        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/planning/task/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->put(json_encode($put_data));

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/planning/task/update/' . " Options: " . json_encode($put_data));

        return json_decode($CI->curl->execute(), true);
    }


   public static function put_planning_edit_status_ok($ActivityId)
    {
        $crm_data = self::get_planning_task($ActivityId);
        $crm_data = $crm_data['Result']['Data'];

        $put_data['ActivityId'] = $ActivityId;
        $put_data['New_status'] = 100000000;

        $put_data['New_user_webportal']['New_web_userId'] = $crm_data['New_web_userId'];
        $put_data['New_registration_test_drive']['New_registration_tdriveId'] = $crm_data['New_registration_tdriveId'];

        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/planning/task/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');


        $CI->curl->put(json_encode($put_data));
        return json_decode($CI->curl->execute(), true);
    }


   public static function helper_setnull($EntityName, $FieldName, $EntityId)
    {
        $put_data['EntityName'] = $EntityName;
        $put_data['FieldName'] = $FieldName;
        $put_data['EntityId'] = $EntityId;

        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/helper/setnull/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

  /*      if ($FieldName == 'new_reason_for_stop_work') {
            echo "<pre>";
            var_dump(json_encode($put_data));
            var_dump('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/helper/setnull/');
            echo "</pre>";
            die;
        }*/
        $CI->curl->put(json_encode($put_data));

        return json_decode($CI->curl->execute(), true);
    }



    /*
   * WebUser Update
   * /api/{organization}/webuser/update
   */
    public static function put_webuser_update($form_data)
    {
//TODO: Get WebUser by id and send support data / Error: writing empty string

        $put_data["Id"] = $form_data['New_web_userId'];
        $put_data['EntityName'] = 'new_web_user';


        $crm_webuser = self::get_webuser_by_id($form_data['New_web_userId']);
        $crm_webuser = $crm_webuser['Result']['Data'];

        if (isset($form_data['company'])) {
            $New_dealer = $form_data['company'];
        } else {
            $New_dealer = $crm_webuser['New_dealer'];
        }

        $New_mobilephone = $form_data['New_mobilephone'];
        if (isset($form_data['New_password']) && $form_data['New_password'] != "") {
            $New_password = $form_data['New_password'];
        } else {
            $New_password = $crm_webuser['New_password'];
        }


        if (isset($form_data['first_name'])) {
            $New_name = $form_data['first_name'];
        } else {
            $New_name = $crm_webuser['New_name'];
        }
        if (isset($form_data['last_name'])) {
            $New_lastname = $form_data['last_name'];
        } else {
            $New_lastname = $crm_webuser['New_lastname'];
        }


        //$ew_email = $crm_webuser['New_email'];
        if ($form_data['New_email']) {
            $New_email = $form_data['New_email'];
        } else {
            $New_email = $crm_webuser['New_email'];
        }
        $New_login = $form_data['New_mobilephone'];


        if (isset($form_data['has_acces_newtdrive']) && $form_data['has_acces_newtdrive'] == '1') {
            $New_has_acces_newtdrive = true;
        } elseif (isset($form_data['has_acces_newtdrive']) && $form_data['has_acces_newtdrive'] == null) {
            $New_has_acces_newtdrive = false;
        } else {
            $New_has_acces_newtdrive = $crm_webuser['New_has_acces_newtdrive'];
        }
        if (isset($form_data['head_sales']) && $form_data['head_sales'] == '1') {
            $New_head_sales = true;
        } elseif (isset($form_data['head_sales']) && $form_data['head_sales'] == null) {
            $New_head_sales = false;
        } else {
            $New_head_sales = $crm_webuser['New_head_sales'];
        }


        if (isset($form_data['has_acces_tdrive']) && $form_data['has_acces_tdrive'] == '1') {
            $New_has_acces_tdrive = true;
        } elseif (isset($form_data['has_acces_tdrive']) && $form_data['has_acces_tdrive'] == null) {
            $New_has_acces_tdrive = false;
        } else {
            $New_has_acces_tdrive = $crm_webuser['New_has_acces_tdrive'];
        }
        if (isset($form_data['has_acces_inc']) && $form_data['has_acces_inc'] == '1') {
            $New_has_acces_inc = true;
        } elseif (isset($form_data['has_acces_inc']) && $form_data['has_acces_inc'] == null) {
            $New_has_acces_inc = false;
        } else {
            $New_has_acces_inc = $crm_webuser['New_has_acces_inc'];
        }
        if (isset($form_data['responsible_for_quality']) && $form_data['responsible_for_quality'] == '1') {
            $New_responsible_for_quality = true;
        } elseif (isset($form_data['responsible_for_quality']) && $form_data['responsible_for_quality'] == null) {
            $New_responsible_for_quality = false;
        } else {
            $New_responsible_for_quality = $crm_webuser['New_responsible_for_quality'];
        }
        if (isset($form_data['access_sales']) && $form_data['access_sales'] == '1') {
            $New_access_sales = true;
        } elseif (isset($form_data['access_sales']) && $form_data['access_sales'] == null) {
            $New_access_sales = false;
        } else {
            $New_access_sales = $crm_webuser['New_access_sales'];
        }
        if (isset($form_data['access_employees']) && $form_data['access_employees'] == '1') {
            $New_access_employees = true;
        } elseif (isset($form_data['access_employees']) && $form_data['access_employees'] == null) {
            $New_access_employees = false;
        } else {
            $New_access_employees = $crm_webuser['New_access_employees'];
        }

        $New_access_check = $crm_webuser['New_access_check'];

        $New_position = $form_data['new_position'];


        $put_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_name',
            'Value' => $New_name
        );
        $put_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_lastname',
            'Value' => $New_lastname
        );
        $put_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_mobilephone',
            'Value' => $New_mobilephone
        );
        $put_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_email',
            'Value' => $New_email
        );
        $put_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_login',
            'Value' => $New_login
        );
        $put_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_password',
            'Value' => $New_password
        );


        $put_data['Lookups'][] = array(
            'AttributeName' => 'new_dealer',
            'EntityName' => 'account',
            'EntityId' => $New_dealer
        );

        $put_data['OtherAttributes'][] = array(
            "AttributeName" => "new_has_acces_newtdrive",
            "Value" => $New_has_acces_newtdrive
        );
        $put_data['OtherAttributes'][] = array(
            "AttributeName" => "new_head_sales",
            "Value" => $New_head_sales
        );
        $put_data['OtherAttributes'][] = array(
            "AttributeName" => "new_has_acces_tdrive",
            "Value" => $New_has_acces_tdrive
        );
        $put_data['OtherAttributes'][] = array(
            "AttributeName" => "new_has_acces_inc",
            "Value" => $New_has_acces_inc
        );
        $put_data['OtherAttributes'][] = array(
            "AttributeName" => "new_responsible_for_quality",
            "Value" => $New_responsible_for_quality
        );
        $put_data['OtherAttributes'][] = array(
            "AttributeName" => "new_access_sales",
            "Value" => $New_access_sales
        );
        $put_data['OtherAttributes'][] = array(
            "AttributeName" => "new_access_check",
            "Value" => $New_access_check
        );
        $put_data['OtherAttributes'][] = array(
            "AttributeName" => "new_access_employees",
            "Value" => $New_access_employees
        );


        if ($form_data["new_position"]) {
            $put_data["Picklists"][] = array(
                'AttributeName' => "new_position",
                "Value" => $New_position
            );
        }

        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/update/' . " Options: " . json_encode($put_data));

        $CI->curl->put(json_encode($put_data));

        $response = json_decode($CI->curl->execute(), true);
        log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/update/' . " Options: " . json_encode($put_data) . ' Response: '  . json_encode($response) . ' User: ' . json_encode($CI->user_row));
        return $response;
        //return json_decode($CI->curl->execute(), true);
    }

    // --- INCIDENTS SECTION ---
    public static function put_incidents_edit($form_data, $crm_incident)
    {
        $put_data["IncidentId"] = $form_data["IncidentId"] ;
        //$put_data["CreatedOn"] = $form_data["CreatedOn"] ;
        $put_data["Description"] = $form_data["Description"] ;
        $put_data["New_FileNum"] = $form_data["New_FileNum"] ;
        $put_data["Ticketnumber"] = $form_data["Ticketnumber"] ;

        //CarModel
        $put_data["Su_vehicleincident"]["Su_vehicleId"] = $crm_incident["Su_vehicleincident"]["Su_vehicleId"];
        $put_data["Su_vehicleincident"]["New_description"] = $form_data["СarModel"];

        //CustomerId
        $put_data["CustomerId"]["ContactId"] = $crm_incident["CustomerId"]["ContactId"];
        //$put_data["CustomerId"]["EMailAddress1"] = $form_data["CustomerEMailAddress1"];
        //put_data["CustomerId"]["MobilePhone"] = $form_data["CustomerMobilePhone"];

        $put_data["New_accountincidentid"]["AccountId"] = $form_data["user_company"];

        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->put(json_encode($put_data));

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents/update/' . " Options: " . json_encode($put_data));

        return json_decode($CI->curl->execute(), true);
    }


    /*
     * incident Resolution
     * PUT /api/{organization}/incidents/incidentresolution/close
     */
    public static function put_incidentresolution_close($IncidentId, $StatusCode = 5)
    {
        $put_data["IncidentId"] = $IncidentId ;
        $put_data["Subject"] = "Да";
        $put_data["StatusCode"] = $StatusCode;

        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents/incidentresolution/close/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->put(json_encode($put_data));
        return json_decode($CI->curl->execute(), true);
    }

    /*
     * incident Resolution
     * PUT /api/{organization}/incidents/incidentresolution/close
     */
    public static function put_incident_answer_change_status($IncidentId, $IncidentAnswerId, $Status, $Description)
    {
        $put_data["New_incident"]['IncidentId'] = $IncidentId;
        $put_data["New_incident_answerId"] = $IncidentAnswerId;
        $put_data["New_status"] = $Status;

        if ($Description != "") {
            $put_data["New_comments"] = $Description;    
        } else {
            //$put_data["New_comments"] = "";
        }

        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/incidents//answers/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        //var_dump(json_encode($put_data)); die;
        $CI->curl->put(json_encode($put_data));
        return json_decode($CI->curl->execute(), true);
    }


    // --- SUVEHICLE SECTION ---
    /*
     * SUVEHICLE Create
     * POST /api/{organization}//create
     */
    public static function put_suvehicle_edit($form_data)
    {

        /*PUT /api/{organization}/booking/suvehicle/update
{
  "EntityName": "su_vehicle",
  "Id": "74c3076c-96ae-eb11-81d1-00155d1f050b",
  "Picklists": [
    {
      "AttributeName": "new_typeofcar",
      "Value": 100000000
    }
  ],
  "Lookups": [
    {
      "AttributeName": "su_accountvehicle2",
      "EntityName": "account",
      "EntityId": "a6ba10c3-bb1c-ea11-81b3-00155d1f050b"
    },
	{
      "AttributeName": "su_carcomplvehicle",
      "EntityName": "su_carcompl",
      "EntityId": "b3ef3fbc-6890-e211-a3af-005056c00008"
    }
  ],
  "DateTimes": [
    {
      "AttributeName": "new_test_entry",
      "Value": 1620324303
    },
	{
      "AttributeName": "new_test_withdrawal",
      "Value": 1620324303
    }
  ],
  "OtherAttributes": [
    {
      "AttributeName": "new_description",
      "Value": "Какая-то галиматья 1234"
    },
	{
      "AttributeName": "su_name",
      "Value": "-----------------"
    }
  ]
}*/
        $post_data = array();
        $post_data['Id'] = $form_data['Su_vehicleId'];
        $post_data['EntityName'] = 'su_vehicle';

        //new_typeofcar
        $post_data['Picklists'][] = array('AttributeName' => 'new_typeofcar', 'Value' => $form_data["new_typeofcar"]);

        //su_accountvehicle2 AND su_carcomplvehicle
        $post_data['Lookups'][] = array(
            'AttributeName' => 'su_accountvehicle2',
            'EntityName' => 'account',
            'EntityId' => $form_data['su_accountvehicle2'],
        );
        $post_data['Lookups'][] = array(
            'AttributeName' => 'su_carcomplvehicle',
            'EntityName' => 'su_carcompl',
            'EntityId' => $form_data['su_carcomplvehicle'],
        );

        //DateTimes: new_test_entry AND su_carcomplvehicle
        $post_data['DateTimes'][] = array(
            'AttributeName' => 'new_test_entry',
            'Value' => $form_data['new_test_entry'],
        );
        $post_data['DateTimes'][] = array(
            'AttributeName' => 'new_test_withdrawal',
            'Value' => $form_data['new_test_withdrawal'],
        );

        //OtherAttributes: new_description AND su_name
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_description',
            'Value' => $form_data['new_description'],
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'su_name',
            'Value' => $form_data['su_name'],
        );


        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/suvehicle/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->put(json_encode($post_data));

        //log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/booking/suvehicle/update/' . " Options: " . json_encode($post_data));

        return json_decode($CI->curl->execute(), true);
    }

    /*
    // --- SUVEHICLE SECTION ---
     *
     * PUT /api/{organization}//helper/setstate/
     */
    public static function suvehicle_delete($SuvehicleId, $StatusCode = 2, $StateCode = 1)
    {
        $post_data['EntityId'] = $SuvehicleId;
        $post_data['EntityName'] = 'su_vehicle';


        $post_data["StatusCode"] = $StatusCode;
        $post_data["StateCode"] = $StateCode;

        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/helper/setstate/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->put(json_encode($post_data));
        return json_decode($CI->curl->execute(), true);
    }


    // --- CONTRACT SECTION ---
    /*
     * CONTRACT UPDATE
     * POST /api/{organization}/offer/update
     */
    public static function put_contract_edit($form_data)
    {

     /*
      * PUT /api/{organization}/offer/update
Update new_offer
{
  "EntityName": "new_offer",
  "Id": "9f2ac048-42b6-eb11-81d1-00155d1f050b",
  "Picklists": [
    {
      "AttributeName": "new_warehouse",
      "Value": 1
    },
	{
      "AttributeName": "new_contractstatus",
      "Value": 1
    },
	{
      "AttributeName": "new_vehiclestatus",
      "Value": 1
    },
	{
      "AttributeName": "new_customsclearance",
      "Value": 1
    },
	{
      "AttributeName": "new_carlocation",
      "Value": 1
    }
  ],
  "Lookups": [
    {
      "AttributeName": "new_registration_id",
      "EntityName": "new_registration_tdrive",
      "EntityId": "0F701A0A-3CB6-EB11-81D1-00155D1F050B"
    },
	{
      "AttributeName": "new_vehicle_id",
      "EntityName": "su_vehicle",
      "EntityId": "211CBEAF-7B87-E911-81A2-00155D1F050B"
    },
	{
      "AttributeName": "new_order_id",
      "EntityName": "new_order",
      "EntityId": "8626A57B-98B4-EB11-81D1-00155D1F050B"
    }
  ],
  "DateTimes": [
    {
      "AttributeName": "new_contractdate",
      "Value": 1621167560
    },
	{
      "AttributeName": "new_guaranteeletter",
      "Value": 1621167560
    },
	{
      "AttributeName": "new_paymentremittance",
      "Value": 1621167560
    },
	{
      "AttributeName": "new_arrivaltodealer",
      "Value": 1621167560
    },
	{
      "AttributeName": "new_confidentdelivery",
      "Value": 1621167560
    },
	{
      "AttributeName": "new_postponeddelivery",
      "Value": 1621167560
    },
	{
      "AttributeName": "new_delivered",
      "Value": 1621167560
    }
  ],
  "OtherAttributes": [
    {
      "AttributeName": "new__vin",
      "Value": "111111112222"
    },
	{
      "AttributeName": "new_ordernumber",
      "Value": "222"
    },
	{
      "AttributeName": "new_model",
      "Value": "222222222"
    },
	{
      "AttributeName": "new_competitorcriteria",
      "Value": "22222222222"
    },
	{
      "AttributeName": "new_competitornames",
      "Value": "222222222"
    },
	{
      "AttributeName": "new_comments",
      "Value": "222222222"
    }
  ]}
     */
        $post_data = array();
        $post_data['Id'] = $form_data['contractId'];
        $post_data['EntityName'] = 'new_offer';

        //new_warehouse
        if ($form_data["new_warehouse"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_warehouse', 'Value' => $form_data["new_warehouse"]);
        }
        if ($form_data["new_ContractStatus"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_contractstatus', 'Value' => $form_data["new_ContractStatus"]);
        }
        if ($form_data["new_VehicleStatus"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_vehiclestatus', 'Value' => $form_data["new_VehicleStatus"]);
        }
        if ($form_data["new_CustomsClearance"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_customsclearance', 'Value' => $form_data["new_CustomsClearance"]);
        }
        if ($form_data["new_CarLocation"] != null) {
            $post_data['Picklists'][] = array('AttributeName' => 'new_carlocation', 'Value' => $form_data["new_CarLocation"]);
        }

        //su_accountvehicle2 AND su_carcomplvehicle
        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_registration_id',
            'EntityName' => 'new_registration_tdrive',
            'EntityId' => $form_data['registration_tdriveId'],
        );

        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_modelversionid',
            'EntityName' => 'su_carcompl',
            'EntityId' => $form_data['parentmodels'],
        );
 /*       $post_data['Lookups'][] = array(
            'AttributeName' => 'new_vehicle_id',
            'EntityName' => 'su_vehicle',
            'EntityId' => $form_data['new_vehicle_id'],
        );*/
/*
        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_name',
            'EntityName' => 'new_order',
            'EntityId' => $form_data['new_OrderNumber'],
        );*/
     /*   var_dump($form_data);
        die;*/
        if ($form_data['New_order_id'] != "") {
            $order_update_data['EntityId'] = $form_data['New_order_id'];
            $order_update_data['EntityName'] = 'new_order';
            $order_update_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_name',
                'Value' => $form_data['new_OrderNumber']
            );
            $CI = &get_instance();
            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/offer/update/');
            $CI->curl->http_header('Accept','application/json');
            $CI->curl->http_header('Content-Type','application/json');

            
            $CI->curl->put(json_encode($order_update_data));
            $order_create_result = json_decode($CI->curl->execute(), true);

            $post_data['Lookups'][] = array(
                'AttributeName' => 'new_order_id',
                'EntityName' => 'new_order',
                'EntityId' => $form_data['New_order_id']
            );
        } elseif ($form_data['New_order_id'] == "" && $form_data['new_OrderNumber'] != "") {
            $order_update_data['EntityName'] = 'new_order';
            $order_update_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_name',
                'Value' => $form_data['new_OrderNumber']
            );
            $CI = &get_instance();
            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/offer/create/');
            $CI->curl->http_header('Accept','application/json');
            $CI->curl->http_header('Content-Type','application/json');


            $CI->curl->post(json_encode($order_update_data));
            $order_update_data = json_decode($CI->curl->execute(), true);
            if($order_update_data['Result']['Data']['Id'] != "") {
                $post_data['Lookups'][] = array(
                    'AttributeName' => 'new_order_id',
                    'EntityName' => 'new_order',
                    'EntityId' => $order_update_data['Result']['Data']['Id']
                );
            }
        } elseif ($form_data['New_order_id'] == "" && $form_data['new_OrderNumber'] == "") {
            $order_update_data['EntityName'] = 'new_order';
            $order_update_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_name',
                'Value' => $form_data['new_OrderNumber']
            );
            $CI = &get_instance();
            $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/offer/create/');
            $CI->curl->http_header('Accept','application/json');
            $CI->curl->http_header('Content-Type','application/json');

            $CI->curl->post(json_encode($order_update_data));
            $order_update_data = json_decode($CI->curl->execute(), true);
            if($order_update_data['Result']['Data']['Id'] != "") {
                $post_data['Lookups'][] = array(
                    'AttributeName' => 'new_order_id',
                    'EntityName' => 'new_order',
                    'EntityId' => $order_update_data['Result']['Data']['Id']
                );
            }
        }

        //DateTimes
        /*new_contractdate
new_guaranteeletter
new_paymentremittance
new_arrivaltodealer
new_confidentdelivery
new_postponeddelivery
new_delivered*/


        
        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_modelversionid',
            'EntityName' => 'su_carcompl',
            'EntityId' => $form_data['parentmodels'],
        );

        if($form_data['new_ContractDate'] == null) {
        $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_contractdate',
                'Value' => null,
            );
        } else{
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_contractdate',
                'Value' => $form_data['new_ContractDate'],
            );
        }

        if($form_data['new_GuaranteeLetter'] == null) {
        $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_guaranteeletter',
                'Value' => null,
            );
        } else{
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_guaranteeletter',
                'Value' => $form_data['new_GuaranteeLetter'],
            );
        }


        if($form_data['new_PaymentRemittance'] == null) {
        $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_paymentremittance',
                'Value' => null,
            );
        } else{
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_paymentremittance',
                'Value' => $form_data['new_PaymentRemittance'],
            );
        }


        if($form_data['new_ArrivalToDealer'] == null) {
        $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_arrivaltodealer',
                'Value' => null,
            );
        } else{
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_arrivaltodealer',
                'Value' => $form_data['new_ArrivalToDealer'],
            );
        }


        if($form_data['new_ConfidentDelivery'] == null) {
        $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_confidentdelivery',
                'Value' => null,
            );
        } else{
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_confidentdelivery',
                'Value' => $form_data['new_ConfidentDelivery'],
            );
        }


        if($form_data['new_PostponedDelivery'] == null) {
        $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_postponeddelivery',
                'Value' => null,
            );
        } else{
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_postponeddelivery',
                'Value' => $form_data['new_PostponedDelivery'],
            );
        }


        if($form_data['new_Delivered'] == null) {
        $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_delivered',
                'Value' => null,
            );
        } else{
            $post_data['DateTimes'][] = array(
                'AttributeName' => 'new_delivered',
                'Value' => $form_data['new_Delivered'],
            );
        }


        if($form_data['New_pricelist_price'] != null) {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_pricelist_price',
                'Value' => intval($form_data['New_pricelist_price']),
            );
        }

        if($form_data['New_invoice_price'] != null) {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_invoice_price',
                'Value' => intval($form_data['New_invoice_price']),
            );
        }

        //OtherAttributes:
        /*
 * new__vin
new_ordernumber
new_model
new_competitorcriteria
new_competitornames
new_comments
 */
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new__vin',
            'Value' => $form_data['new_VIN'],
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_ordernumber',
            'Value' => $form_data['new_OrderNumber'],
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_model',
            'Value' => $form_data['new_Model'],
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_competitorcriteria',
            'Value' => $form_data['new_CompetitorCriteria'],
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_competitornames',
            'Value' => $form_data['new_CompetitorNames'],
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_comments',
            'Value' => $form_data['new_Comments'],
        );


        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/offer/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->put(json_encode($post_data));

        log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/offer/update/' . " Options: " . json_encode($post_data));

        return json_decode($CI->curl->execute(), true);
    }


    // --- CREDIT SECTION ---
    /*
     * CONTRACT UPDATE
     * POST /api/{organization}/offer/update
     */
    public static function put_credit_edit($form_data)
    {

        $post_data = array();
        $post_data['Id'] = $form_data['contract_creditid'];
        $post_data['EntityName'] = 'new_credit';


        $new_type = null;
        if ($form_data['new_ContractStatus'] == 5) {
            $new_type = 100000002;
        } elseif ($form_data['new_ContractStatus'] == 4) {
            $new_type = 100000001;
        }

        if ($new_type) {
            $post_data['Picklists'][] = array(
                'AttributeName' => 'new_type',
                'Value' => intval($new_type)
            );
        }

        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_first_installment',
            'Value' => intval($form_data['new_first_installment'])
        );
        
        if ($form_data['new_term'] == "") {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_term',
                'Value' => null
            );    
        } else {
            $post_data['OtherAttributes'][] = array(
                'AttributeName' => 'new_term',
                'Value' => intval($form_data['new_term'])
            );    
        }
        
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_another_bank_reason',
            'Value' => $form_data['new_another_bank_reason']
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_credit_agricole_kredobank',
            'Value' => boolval($form_data['new_credit_agricole_kredobank'])
        );
        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_history',
            'Value' => $form_data['new_history']
        );

        $post_data['Lookups'][] = array(
            'AttributeName' => 'new_bankid',
            'EntityName' => 'account',
            'EntityId' => $form_data['new_bankid']
        );

        

        $post_data["DateTimes"][] = array(
            'AttributeName' => "new_date_bank_application",
            "Value" => $form_data["new_date_bank_application"]
        );

        $post_data["DateTimes"][] = array(
            'AttributeName' => "new_date_guaranteeletter",
            "Value" => $form_data["new_GuaranteeLetter"]
        );

        $post_data["DateTimes"][] = array(
            'AttributeName' => "new_date_bank_transaction",
            "Value" => $form_data["new_PaymentRemittance"]
        );

	    //Страхування и Умови кредитування
        if ($form_data['new_insurance_company']) {
	        $post_data['Lookups'][] = array(
	            'AttributeName' => 'new_insurance_company',
	            'EntityName' => 'account',
	            'EntityId' => $form_data['new_insurance_company']
	        );
        }
        if ($form_data['new_rate']) {
            $fmt = numfmt_create( 'de_DE', NumberFormatter::DECIMAL );

	        $post_data['OtherAttributes'][] = array(
	            'AttributeName' => 'new_rate',
	            'Value' => numfmt_parse($fmt, $form_data['new_rate'])
	        );
        }
        if ($form_data['new_franchise']) {
            $post_data['Picklists'][] = array(
                'AttributeName' => 'new_franchise',
                'Value' => intval($form_data['new_franchise'])
            );
        }
        if ($form_data['new_credit_conditions']) {
            $post_data['Picklists'][] = array(
                'AttributeName' => 'new_credit_conditions',
                'Value' => intval($form_data['new_credit_conditions'])
            );
        }



        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/credit/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->put(json_encode($post_data));
        $response = json_decode($CI->curl->execute(), true);
    
        //vardump(array(json_encode($post_data), $response));
        log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/credit/update/' . " Options: " . json_encode($post_data) . ' Response: '  . json_encode($response) . ' User: ' . json_encode($CI->user_row));

        return $response;
        //return json_decode($CI->curl->execute(), true);
    
    }
    /*
     * CREDIT COMMENTS UPDATE
     * POST /api/{organization}/offer/update
     */
    public static function put_credit_history_edit($form_data)
    {

        $post_data = array();
        $post_data['Id'] = $form_data['New_creditId'];
        $post_data['EntityName'] = 'new_credit';


        $post_data['OtherAttributes'][] = array(
            'AttributeName' => 'new_history',
            'Value' => $form_data['new_history']
        );



        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/credit/update/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->put(json_encode($post_data));
        $response = json_decode($CI->curl->execute(), true);
    
        //vardump(array(json_encode($post_data), $response));
        log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/credit/update/' . " Options: " . json_encode($post_data) . ' Response: '  . json_encode($response) . ' User: ' . json_encode($CI->user_row));

        return $response;
        //return json_decode($CI->curl->execute(), true);
    
    }



    // --- CONTRACT SECTION ---
    /*
     * CONTRACT Delete
     * POST /api/{organization}//delete
     */
    public static function post_contract_delete($form_data) {
        $post_data = array();
        $post_data['EntityName'] = 'new_offer';
        $post_data['EntityId'] = $form_data['offerId'];


        $CI = &get_instance();
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/crm/delete/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->delete( json_encode( $post_data ) );
        log_message('info'," RESPONSE: " . 'https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/crm/delete/' . " Options: " . json_encode($post_data));
        return json_decode($CI->curl->execute(), true);
    }



    /*
     * Check exist
     * */
    public static function webuser_exist($phone, $email = null)
    {
        if ($phone == null) {
            return false;
        }
        $post_data['New_mobilephone'] = $phone;

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/webuser/exists/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->post(json_encode($post_data));
        $res = json_decode($CI->curl->execute(), true);
        return $res['Result']['Data'];
    }

    // @author aws
    // 2021-05-27
    // 
    public static function get_showroom_traffic( $options = array() ) {
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $url = self::$_api_url.self::$_organization.'/sar/showroom/all?month='.$month.'&year='.$year;

        if ( isset($options['dealer']) ) $url .= '&dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        //echo "<script>console.log(`". json_encode(self::$_organization) ."`)</script>";

        return json_decode($CI->curl->execute(), true);
    }

    public static function get_showroom_traffic_details( $options = array() ) {

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        // echo $month. ' '.$year;
        $id = '';
        if (isset( $options['id'] ) ) $id = $options['id'];

        $url = self::$_api_url.self::$_organization.'/sar/showroom/details/parent/'.$id;
        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    } // get_showroom_traffic_details
	
	// @version 2021-08-17
	public static function get_showroom_traffic_details_v2( $options = array() ) {

        $CI = &get_instance();
        
		$month = date('m');
        $year = date('Y');
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];
		
        $url = self::$_api_url.self::$_organization.'/sar/showroom/all/new/'.$year.'/'.$month;
        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    } // get_showroom_traffic_details_v2

    public static function get_lead_details( $options = array() ) {

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        // echo $month. ' '.$year;
        $id = '';
        if (isset( $options['id'] ) ) $id = $options['id'];

        $url = self::$_api_url.self::$_organization.'/sar/lead/details/parent/'.$id;
        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    } // get_lead_details


      public static function get_leads( $options = array() ) {

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $url = self::$_api_url.self::$_organization.'/sar/lead/all?month='.$month.'&year='.$year;

        if ( isset($options['dealer']) ) $url .= '&dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        //echo "<script>console.log(`". json_encode(self::$_organization) ."`)</script>";

        return json_decode($CI->curl->execute(), true);
    } // get_showroom_traffic_details

    public static function get_lead_details_v2( $options = array() ) {

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $url = self::$_api_url.self::$_organization.'/sar/lead/all/details/?month='.$month.'&year='.$year;

        if ( isset($options['dealer']) ) $url .= '&dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        //echo "<script>console.log(`". json_encode(self::$_organization) ."`)</script>";

        return json_decode($CI->curl->execute(), true);
    } // get_showroom_traffic_details


    public static function get_cars( $options = array() ) {

        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        // echo $month. ' '.$year;

        $url = self::$_api_url.self::$_organization.'/booking/cars';
        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    } // get_cars

    public static function get_testdrive( $options = array() ) {
        
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $CI = &get_instance();
        $url = self::$_api_url.self::$_organization.'/sar/registrations/sum/'.$year.'/'.$month;
        if ( isset($options['dealer']) ) $url .= '?dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);  

    } // get_testdrive

    public static function get_offer( $options = array() ) {
        
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $CI = &get_instance();
        $url = self::$_api_url.self::$_organization.'/offer/sum/'.$year.'/'.$month;
        //?year='.$year.'&month='.$month;

        //echo $url;
        if ( isset($options['dealer']) ) $url .= '?dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);  

    } // get_offer

    public static function get_offer_opel( $options = array() ) {
        
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $CI = &get_instance();
        $url = self::$_api_url.self::$_organization.'/offer/sum/opel/'.$year.'/'.$month;
        //?year='.$year.'&month='.$month;

        //echo $url;
        if ( isset($options['dealer']) ) $url .= '?dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);  

    } // get_offer_opel
	
    public static function get_sale( $options = array() ) {

        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $CI = &get_instance();
        $url = self::$_api_url.self::$_organization.'/client/cars/sum/'.$year.'/'.$month;
        //?year='.$year.'&month='.$month;

        //echo $url;
        if ( isset($options['dealer']) ) $url .= '?dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    } // get_sale

	
    public static function sar_update( $options = array() ) {

        $CI = &get_instance();

        $url = self::$_api_url.self::$_organization.'/sar/update';
        
        $CI->curl->create( $url );
        $CI->curl->put( json_encode($options['post_data']) );
        $CI->curl->http_header('Content-Type','application/json');
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    } // sar_update

    public static function sar_create( $options = array() ) {

        $CI = &get_instance();

        $url = self::$_api_url.self::$_organization.'/sar/create';
        
        $CI->curl->create( $url );
        $CI->curl->post( json_encode($options['post_data']) );
        $CI->curl->http_header('Content-Type','application/json');
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    } // sar_create

    public static function sar_delete( $options = array() ){
        $CI = &get_instance();
        //$CI->curl->_setHeader('');
        $url = self::$_api_url.self::$_organization.'/crm/delete/';
        $CI->curl->create('https://webportal.crm.servicedesk.in.ua/api/'.self::$_organization.'/crm/delete/');
        $CI->curl->http_header('Accept','application/json');
        $CI->curl->http_header('Content-Type','application/json');

        $CI->curl->delete( json_encode( $options['post_data']) );
/*
        $CI->curl->delete(json_encode(array(
            'EntityName' => $EntityName,
            'EntityId' => $id_list
        )));*/

        return json_decode($CI->curl->execute(), true);
    }
    /**
     * @param options.organization required
     */
    public static function sar_pcu_users( $options = array() ) {
        $CI = &get_instance();
        $url = self::$_api_url.$options['organization'].'/webuser/active';
        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }

    /**
     * @param options.organization required
     */
    public static function sar_dealers_users( $options = array() ) {
        $CI = &get_instance();
        $url = self::$_api_url.$options['organization'].'/webuser/users/formailing';
        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
	// *******************************************************************************
	// Telegram report methods
	// *******************************************************************************
	
    /**
     * @param options.organization require
     */

    public static function get_offer_day( $options = array() ) {
        
        $month = date('m');
		$day = date('d');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];
		if (isset( $options['day'] ) ) $day = $options['day'];

        $CI = &get_instance();
        $url = self::$_api_url.self::$_organization.'/offer/sum/'.$year.'/'.$month.'/'.$day;

        //echo $url;
        if ( isset($options['dealer']) ) $url .= '?dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);  

    } // get_offer_day
	
	// *******************************************************************************
	// DSR methods
	// *******************************************************************************

    public static function get_dsr_actual( $options = array() ) {
        
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $CI = &get_instance();
        $url = self::$_api_url.self::$_organization.'/dsr/actual/'.$year.'/'.$month;
        //?year='.$year.'&month='.$month;

        //echo $url;
        if ( isset($options['dealer']) ) $url .= '?dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);  

    } // get_dsr_actual
	
    public static function get_dsr_history( $options = array() ) {
        
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $CI = &get_instance();
        $url = self::$_api_url.self::$_organization.'/dsr/history/'.$year.'/'.$month;
        //?year='.$year.'&month='.$month;

        //echo $url;
        if ( isset($options['dealer']) ) $url .= '?dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);  

    } // get_dsr_history
	
	public static function get_dsr_counts( $options = array() ) {
        
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $CI = &get_instance();
        $url = self::$_api_url.self::$_organization.'/dsr/counts/'.$year.'/'.$month;
        //?year='.$year.'&month='.$month;

        //echo $url;
        if ( isset($options['dealer']) ) $url .= '?dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);  

    } // get_dsr_counts

    public static function get_dsr_counts_without_vip($options = array())
    {
        $month = date('m');
        $year = date('Y');
        // echo $month. ' '.$year;
        if (isset( $options['month'] ) ) $month = $options['month'];
        if (isset( $options['year'] ) ) $year = $options['year'];

        $CI = &get_instance();
        $url = self::$_api_url.self::$_organization.'/dsr/counts/withoutvip/'.$year.'/'.$month;
        //?year='.$year.'&month='.$month;

        //echo $url;
        if ( isset($options['dealer']) ) $url .= '?dealer=' . $options['dealer'];

        $CI->curl->create( $url );
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);
    }
	
	public static function dsr_create( $options = array() ) {

        $CI = &get_instance();

        $url = self::$_api_url.self::$_organization.'/dsr/create';
        
        $CI->curl->create( $url );
        $CI->curl->post( json_encode($options['post_data']) );
        $CI->curl->http_header('Content-Type','application/json');
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    } // dsr_create
	
	public static function dsr_update( $options = array() ) {

        $CI = &get_instance();

        $url = self::$_api_url.self::$_organization.'/dsr/update';
        
        $CI->curl->create( $url );
        $CI->curl->put( json_encode($options['post_data']) );
        $CI->curl->http_header('Content-Type','application/json');
        $CI->curl->http_header('Accept','application/json');

        return json_decode($CI->curl->execute(), true);

    } // dsr_update
	
}