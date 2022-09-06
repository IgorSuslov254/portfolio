<?php

class Payment                                   //класс для работы с компенсациями
{
  private $CI;

  public function __construct()
  {
   $this->CI =& get_instance();
   $this->CI->load->model('Payment_model');
   $this->CI->load->model('ComboBox_model');
   $this->CI->load->model('CRM_model');
  }

  public function getPaymentList($user,$date_begin=-1,$date_end=-1)       //если даты не передаются, то выбираются последние два месяца
  {
    $dateTime = new DateTime("now");
    $twoMonthsAgo = $this->incrementDate($dateTime, -1);
    $twoMonthsAgo = $twoMonthsAgo->format ('Ym01');

    $date_begin==-1 ? $date_begin= $twoMonthsAgo:1;             //промежуток (по умолчанию за текущий месяц и предыдущий)
    $date_end==-1 ? $date_end = date('Ymd 23:59:59'):1;

    $res =$user->getListOfPayment($date_begin,$date_end);
    if(!isset($res))
    {
      return;
    }
    $res = $this->buildArray($res);
    return $res;
  }

  public function createPayment($user,$data)
  {
    $data['modal_create_payment_form_enddate'] = date('d.m.Y 23:59',strtotime($data['modal_create_payment_form_enddate']));
    $res = $user->createPayment($data);
    return $res;
  }

  public function toHold($user,$id)
  {
    $now = new DateTime();
    $timeUnix = $now->getTimestamp();      //получаем текущие дату и время в Unix
    $timeUnix += 14400;                     //увеличиваем на час
    return $user->toHold($id,$timeUnix);
  }

  public function toActive($user,$id)
  {
    $date_next_month = date("d-m-Y", strtotime("+1 month"));
    $timeUnix = strtotime($date_next_month);
    $timeUnix += 86400;

    /*$timeUnix = new DateTime();
    $timeUnix = $this->incrementDate($timeUnix, 1);            //увеличиваем время жизни на месяц
    $timeUnix = $timeUnix->getTimestamp();*/
    return $user->toActive($id,$timeUnix);
  }


  public function getFood($text)
  {
    $listFood = $this->CI->ComboBox_model->getFoodList($text);
    return $listFood;
  }

  private function incrementDate($startDate, $monthIncrement = 0)         //функция инкрементации месяца
  {
    $startingTimeStamp = $startDate->getTimestamp();
    // Get the month value of the given date:
    $monthString = date('Y-m', $startingTimeStamp);
    // Create a date string corresponding to the 1st of the give month,
    // making it safe for monthly calculations:
    $safeDateString = "first day of $monthString";
    // Increment date by given month increments:
    $incrementedDateString = "$safeDateString $monthIncrement month";
    $newTimeStamp = strtotime($incrementedDateString);
    $newDate = DateTime::createFromFormat('U', $newTimeStamp);
    return $newDate;
}

  private function buildArray($array)
  {

    $arr = array();
    foreach ($array as $key => $value)
    {
      if(!empty($array[$key]['CreatedOn']))
      {
        $array[$key]['CreatedOn'] = date('d.m.Y',strtotime($array[$key]['CreatedOn']));
      }

      if(!empty($array[$key]['new_enddate']))
      {
        $array[$key]['new_enddate'] = date('d.m.Y',strtotime($array[$key]['new_enddate']));
      }

      switch ($array[$key]['new_status'])
      {
        case '100000000':
        {
          unset($array[$key]['new_status']);
          $array[$key]['new_usedbyName'] = "Не погашено";
          $arr["Active"][] = $array[$key];
        }
        break;
        case '100000001':
        {
          unset($array[$key]['new_status']);
          $arr["Hold"][] = $array[$key];
        }
        break;
        case '100000002':
        {
          unset($array[$key]['new_status']);
          $arr["Used"][] = $array[$key];
        }
        break;
        case '100000003':
        {
          unset($array[$key]['new_status']);
          $arr["Deactive"][] = $array[$key];
        }
        break;
      }
    }
    return $arr;
  }

}

?>
