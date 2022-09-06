<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Support_service extends CI_Controller {
    const WORKTIME = [
        'new_new_instoreworktime',
        'new_driveworktime',
        'new_expressworktime',
        'new_glovoworktime',
        'new_rocketworktime',
    ];

    private $params = [];

    public function __construct()
    {
        parent::__construct();
        $this->lang->load(['error_support_service', 'success_support_service', 'support_service']);
    }

    public function index(){
        // $this->load->model(['SupportServiceModel', 'CRM_model']);

        // $restaurant_id = $this->CRM_model->changeWorktime($_SESSION['SystemUserId'], 'get_for_support_service');
        
        // $result = $this->SupportServiceModel->getNewChangeformExtensionBase([
        //     'restaurant_id' => $restaurant_id //  "2D078586-C606-EB11-81C0-00155D1F050B" 5F078586-C606-EB11-81C0-00155D1F050B
        // ]);

        $data['lang'] = $this->lang;

        $data['choose_support_service_form'] = [
            'choose' => $this->lang->line('ss_choose_support_service_form_choose'),
            'constantlies' => [
                [   
                    'id' => 'constantly',
                    'name' => 'constantly',
                    'text' => $this->lang->line('ss_choose_support_service_form_constantlies')[0]
                ],
                [   
                    'id' => 'temporarily',
                    'name' => 'constantly',
                    'text' => $this->lang->line('ss_choose_support_service_form_constantlies')[1]
                ]
            ]
        ];

        $data['form_one'] = [
            'title' => $this->lang->line('ss_form_one_title'),
            'constantly_forms' => [
                'in' => $this->lang->line('ss_form_one_in'),
                'to' => $this->lang->line('ss_form_one_to'),
                'elements' => [
                    [
                        'text' => $this->lang->line('ss_form_one_elements')[0],
                        'name' => 'new_new_instoreworktime',
                        'value' => $result[0]['new_new_instoreworktime'] ?? ''
                    ],
                    [
                        'text' => $this->lang->line('ss_form_one_elements')[1],
                        'name' => 'new_driveworktime',
                        'value' => $result[0]['new_driveworktime'] ?? ''
                    ],
                    [
                        'text' => $this->lang->line('ss_form_one_elements')[2],
                        'name' => 'new_expressworktime',
                        'value' => $result[0]['new_expressworktime'] ?? ''
                    ],
                    [
                        'text' => $this->lang->line('ss_form_one_elements')[3],
                        'name' => 'new_glovoworktime',
                        'value' => $result[0]['new_glovoworktime'] ?? ''
                    ],
                    [
                        'text' => $this->lang->line('ss_form_one_elements')[4],
                        'name' => 'new_rocketworktime',
                        'value' => $result[0]['new_rocketworktime'] ?? ''
                    ]
                ] 
            ],
            'comments' => [
                'name' => 'new_comment',
                'placeholder' => $this->lang->line('ss_form_comments'),
                'maxlength' => 5000,
                'value' => $result[0]['new_comment'] ?? ''
            ],
            'name' => [
                'name' => 'new_employeedata',
                'placeholder' => $this->lang->line('ss_form_name'),
                'maxlength' => 100,
                'value' => $result[0]['new_employeedata'] ?? ''
            ],
            'button' => $this->lang->line('ss_form_button')
        ];

        $data['form_two'] = [
            'title' => $this->lang->line('ss_form_two_title'),
            'checked' => $result[0]['new_schedulechangereason'] ?? '',
            'choose' => [
                [
                    'id' => 'ft_water_shut_off',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[0],
                    'value' => 100000000
                ],
                [
                    'id' => 'ft_power_outage',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[1],
                    'value' => 100000001
                ],
                [
                    'id' => 'ft_mining_operations',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[2],
                    'value' => 100000012
                ],
                [
                    'id' => 'ft_road_accidents',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[3],
                    'value' => 100000002
                ],
                [
                    'id' => 'ft_problems_it_equipment',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[4],
                    'value' => 100000003
                ],
                [
                    'id' => 'ft_sewer_accidents',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[5],
                    'value' => 100000004
                ],
                [
                    'id' => 'ft_fire_smoke',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[6],
                    'value' => 100000005
                ],
                [
                    'id' => 'ft_ventilation_shutdown_failure',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[7],
                    'value' => 100000006
                ],
                [
                    'id' => 'ft_emergency',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[8],
                    'value' => 100000007
                ],
                [
                    'id' => 'ft_no_heating',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[9],
                    'value' => 100000008
                ],
                [
                    'id' => 'ft_technical',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[10],
                    'value' => 100000009
                ],
                [
                    'id' => 'ft_quarantine_restrictions',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[11],
                    'value' => 100000010
                ],
                [
                    'id' => 'ft_other',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[12],
                    'value' => 100000011
                ],
                [
                    'id' => 'ft_net',
                    'name' => 'new_schedulechangereason',
                    'text' => $this->lang->line('ss_form_two_choose')[13],
                    'value' => 100000013
                ]
            ],
            'comments' => [
                'id' => 'ft_comments',
                'name' => 'new_comment',
                'placeholder' => $this->lang->line('ss_form_comments'),
                'maxlength' => 5000,
                'value' => $result[0]['new_comment'] ?? ''
            ],
            'name' => [
                'id' => 'ft_name',
                'name' => 'new_employeedata',
                'placeholder' => $this->lang->line('ss_form_name'),
                'maxlength' => 100,
                'value' => $result[0]['new_employeedata'] ?? ''
            ],
            'date_time' => [
                'id' => 'new_resumptionwork',
                'text' => $this->lang->line('ss_form_two_date_time'),
                'value' => $result[0]['new_resumptionwork'] ?? ''
            ],
            'button' => $this->lang->line('ss_form_button')
        ];

        $data['support_service_form_new_changetype'] = [
            'title' => $this->lang->line('ss_support_service_form_new_changetype_title'),
            'input' => [
                [
                    'id' => 'new_directorfullname',
                    'text' => $this->lang->line('ss_support_service_form_new_changetype_input')[0],
                    'maxlength' => 100,
                    'value' => $result[0]['new_directorfullname'] ?? ''
                ],
                [
                    'id' => 'new_directoremail',
                    'text' => $this->lang->line('ss_support_service_form_new_changetype_input')[1],
                    'maxlength' => 100,
                    'value' => $result[0]['new_directoremail'] ?? ''
                ],
                [
                    'id' => 'new_directorephone',
                    'text' => $this->lang->line('ss_support_service_form_new_changetype_input')[2],
                    'maxlength' => 100,
                    'value' => $result[0]['new_directorephone'] ?? ''
                ]
            ],
            'button' => $this->lang->line('ss_form_button')
        ];

        echo $this->breadcrumb().$this->load->view('support_service', $data, TRUE).$this->load->view('modal_support_service_change', '', TRUE).$this->load->view('madal_confirm', '', TRUE);
    }

	public function changeWorktime()
	{
        $error = null;
        $error_worktime = true;

        foreach (self::WORKTIME as $worktime) {
            if ( !empty( $_POST[$worktime.'_c'] ) && !empty( $_POST[$worktime.'_k'] ) ){
                $_POST[$worktime] = $_POST[$worktime.'_c'] . ' - ' . $_POST[$worktime.'_k'];
                $error_worktime = false;
            } else{
                $_POST[$worktime] = "";
            }
            unset( $_POST[$worktime.'_c'] );
            unset( $_POST[$worktime.'_k'] );
        }

        $result = [];

        if ($error_worktime)
            $error .= $this->lang->line('worktime')."<br>";

        // проверка на 5000 символов
        if (iconv_strlen($_POST['new_comment']) > 5000)
            $error .= $this->lang->line('comment')."<br>";

        // проверка на 100 символов и пустату
        if ( !empty( $_POST['new_employeedata'] ) ){
            if (iconv_strlen($_POST['new_employeedata']) > 100)
                $error .= $this->lang->line('employeedata')."<br>";
        } else{
            $error .= $this->lang->line('empty_employeedata')."<br>";
        }

        $this->params = [
            'error' => $error,
            'callback' => 'changeWorktime',
            'data' => $_POST
        ];

        $this->result();
	}

    public function changeWorktimeTemporarily()
    {
        unset($_POST['new_resumptionwork_date']);
        unset($_POST['new_resumptionwork_date_submit']);
        unset($_POST['new_resumptionwork_time']);

        $error = null;
        $result = [];

        if ( empty( $_POST['new_schedulechangereason'] ) ){
            $error .= $this->lang->line('schedulechangereason')."<br>";
        } else{
            if (
                $_POST['new_schedulechangereason'] == 100000002 ||
                $_POST['new_schedulechangereason'] == 100000010 ||
                $_POST['new_schedulechangereason'] == 1000000011 ||
                $_POST['new_schedulechangereason'] == 100000007
            ){
                if ( empty( $_POST['new_comment'] ) )
                    $error .= $this->lang->line('comment_reson')."<br>";
            }
        }

        if ( !empty( $_POST['new_resumptionwork'] ) ){
            try {
                $date = new DateTime($_POST['new_resumptionwork']);
                $_POST['new_resumptionwork'] = $date->format('d.m.Y H:i');
            } catch (Exception $e) {
                $error .= $this->lang->line('resumptionwork')."<br>";
            }
        }

        // проверка на 5000 символов
        if (iconv_strlen($_POST['new_comment']) > 5000)
            $error .= $this->lang->line('comment')."<br>";

        // проверка на 100 символов и пустоту
        if ( !empty( $_POST['new_employeedata'] ) ){
            if (iconv_strlen($_POST['new_employeedata']) > 100)
                $error .= $this->lang->line('employeedata')."<br>";
        } else{
            $error .= $this->lang->line('empty_employeedata')."<br>";
        }

        $this->params = [
            'error' => $error,
            'callback' => 'changeWorktime',
            'data' => $_POST
        ];

        $this->result();
    }

	public function changeDirector()
	{
        $error = null;
        $result = [];

        if ( !empty( $_POST['new_directoremail'] ) ){
            if ( iconv_strlen($_POST['new_directoremail']) > 100 )
                $error .= $this->lang->line('directoremail')."<br>";
        } else{
            $error .= $this->lang->line('directoremail')."<br>";
        }

        if ( !empty( $_POST['new_directorephone'] ) ){
            if ( iconv_strlen($_POST['new_directorephone']) > 100 )
                $error .= $this->lang->line('directorephone')."<br>";
        } else{
            $error .= $this->lang->line('directorephone')."<br>";
        }

        if ( !empty( $_POST['new_directorfullname'] ) ){
            if ( iconv_strlen($_POST['new_directorfullname']) > 100 )
                $error .= $this->lang->line('directorfullname')."<br>";
        } else{
            $error .= $this->lang->line('directorfullname')."<br>";
        }

        $this->params = [
            'error' => $error,
            'callback' => 'changeDirector',
            'data' => $_POST
        ];

        $this->result();
	}

    public function changeRestrant()
    {
        $error = null;
        $result = [];

        $this->params = [
            'error' => $error,
            'callback' => 'changeRestrant',
            'data' => $_POST
        ];

        $this->result();
    }

    private function result()
    {
        if ( empty( $this->params ) ){
            return [
                'status' => false,
                'message' => $this->lang->line('error_crm')
            ];
        }

        if ($this->params['error']){
            $result = [
                'status' => false,
                'message' => $this->params['error']
            ];
        } else{
            if ( $this->sendCrm() ){
                $result = [
                    'status' => true,
                    'message' => $this->lang->line('success')
                ];
            } else{
                $result = [
                    'status' => false,
                    'message' => $this->lang->line('error_crm')
                ];
            }
        }    

        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode( $result ));
    }

    private function sendCrm()
    {
        if ( empty( $this->params ) ) return false;
        $user = Users::setRole($this->session->login,$this->session->Role_Name,$this->session->SystemUserId);
        return $user->{$this->params['callback']}($this->params['data']);
    }

    private function breadcrumb()
    {
        $breadcrumb = '
            <section id="head_mein">
                <div class="container-fluid">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>Служба підтримки</h1>
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
