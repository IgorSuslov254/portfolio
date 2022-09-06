<?php
class Calls
{
  private $CI;

  public function __construct()
  {
   $this->CI =& get_instance();
   //$this->CI->load->model('Clients_model');
  }

  public function getCallsList($user,$date_begin=-1,$date_end=-1)
  {
    $date_begin==-1 ? $date_begin= date('Ym01'):1;                                                                 //промежуток (по умолчанию за текущий месяц)
    $date_end==-1 ? $date_end = date('Ymd 23:59:59'):1;
    $calls = $user->getCalls($date_begin,$date_end);
    foreach ($calls as $key => $value)
    {
      $calls[$key]['createdon'] = date('d.m.Y H:i',strtotime($calls[$key]['createdon'])+$this->time_zone() );
    }
    $calls = $this->hrefOfCalls($calls);
    return $calls;
  }

  public function getCallsOfClient($idClient)
  {
    $this->CI->load->model('Calls_model');
    $calls = $this->CI->Calls_model->getCallsOfClient($idClient);
    foreach ($calls as $key => $value)
    {
      $calls[$key]['createdon'] = date('d.m.Y H:i',strtotime($calls[$key]['createdon'])+$this->time_zone() );
    }
    $calls = $this->hrefOfCalls($calls);
    return $calls;
  }

  private function hrefOfCalls($callsList)
  {
    foreach ($callsList as $key => $value)
    {
      if (!empty($callsList[$key]['new_callrecording']))
      {
        $hrefs = explode(';',$callsList[$key]['new_callrecording']);
        foreach ($hrefs as $key1 => $value1)
        {
          $num = $key1+1;
          $hrefs[$key1] = '<a href="'.$hrefs[$key1].'">запис №'.$num.'</a>';
        }
        $callsList[$key]['new_callrecording'] = implode(";", $hrefs);
      }
    }
    return $callsList;
  }

  private function time_zone( $input_date = NULL ){
    $start_summer_time = strtotime("last sunday", mktime(0, 0, 0, 3, 32, date('Y')));
    $start_summer_time_ = date('Y-m-d', $start_summer_time);
    $start_summer_timestamp = strtotime($start_summer_time_);

    $end_summer_time = strtotime("last sunday", mktime(0, 0, 0, 10, 32, date('Y')));
    $end_summer_time_ = date('Y-m-d', $end_summer_time);
    $end_summer_timestamp = strtotime($end_summer_time_);

    if( is_null($input_date) ) {
      $date = strtotime( date('Y-m-d') );
    } else {
       $date = strtotime( $input_date );
    }

    if( $date >= $start_summer_timestamp && $date < $end_summer_timestamp ){
        $time = 10800;
    } else {
        $time = 7200;
    }

    return $time;
  }
}
?>
