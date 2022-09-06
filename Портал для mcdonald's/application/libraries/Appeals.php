<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
include(APPPATH."\libraries\Users.php");
include(APPPATH."\libraries\Clients.php");
include(APPPATH."\libraries\Calls.php");
include(APPPATH."\libraries\Payment.php");
class Appeals                                           //класс в autoload
{
  private $CI;

  public function __construct()
  {
   $this->CI =& get_instance();
  }

  public function getListOfAppeals($user, $date_begin=-1, $date_end=-1, $addParam = []) //возвращает список обращений для заданного логина и заданный
  {
    //date_default_timezone_set('Europe/Kiev');
    $date_begin==-1 ? $date_begin= date('Ym01'):1;                                                                 //промежуток (по умолчанию за текущий месяц)
    $date_end==-1 ? $date_end = date('Ymd 23:59:59'):1;

    $arrayOfAppeals = $user->getAppeals($date_begin, $date_end, $addParam);
    if(!isset($arrayOfAppeals))
    {
      return;
    }
    $arrayOfAppeals = $this->buildArrayOfAppeals($arrayOfAppeals);
    //return !$arrayOfAppeals ? -1 : json_decode(json_encode($arrayOfAppeals), TRUE);
    return json_decode(json_encode($arrayOfAppeals), TRUE);    //создает из объекта массив
  }

  public function getAppealsOfClient($idClient){
    $this->CI->load->model('Appeal_model');
    $appeals = $this->CI->Appeal_model->getAppealsOfClient($idClient);
    $appeals= $this->buildArrayOfAppeals($appeals);
    return json_decode(json_encode($appeals), TRUE);
  }

  public function getListOfAppeals_count($user, $date_begin=-1, $date_end=-1, $addParam = []) {
    //date_default_timezone_set('Europe/Kiev');
    $date_begin==-1 ? $date_begin= date('Ym01'):1;                                                                 //промежуток (по умолчанию за текущий месяц)
    $date_end==-1 ? $date_end = date('Ymd 23:59:59'):1;

    $arrayOfAppeals = $user->getAppeals_count($date_begin, $date_end, $addParam);
    return $arrayOfAppeals;    //создает из объекта массив
  }

  public function setInWork($user,$ticketNuber)
  {
    return $user->inWork($ticketNuber);
  }

  public function getListOfAnswers($user,$incidentNumber)
  {
    $result = $user->getListOfAnswers($incidentNumber);
    foreach ($result as $key => $value)
    {
      $result[$key]['createdon'] = date('d.m.Y H:i',strtotime($result[$key]['createdon'])+$this->time_zone() );
    }
    return $result;
  }

  public function changeAppeal($user,$ticketNumber,$arrayOfData)
  {
    return $user->changeAppeal($ticketNumber,$arrayOfData);
  }

  public function downloadFiles($idAppeal)
  {
    $this->CI->load->model('Appeal_model');
    $listOfFiles = $this->CI->Appeal_model->getListOfFiles($idAppeal);
    $listOfFile = $this->getLinkForDownloadFiles($listOfFiles);
    return $listOfFiles;
  }

  public function downloadThisFile($idFile)
  {
    $this->CI->load->model('Appeal_model');
    $file = $this->CI->Appeal_model->downloadFile($idFile);
    return $file;
  }

  private function getLinkForDownloadFiles($listOfFiles)
  {
    $arrayOfFile = array();
    foreach ($listOfFiles as $value)
    {
      $arrayOfFile[] = '<a href="Portal/Download/'.$value['AnnotationId'].'">"'.$value["FileName"].'"</a>';
    }
    return $arrayOfFile;
  }


  private function buildArrayOfAppeals($array)   //строит массив обращений с разделением по статусу обращения + настройка времени + файлы
  {

    // return $array;
    //date_default_timezone_set('Europe/Kiev');

    //file_put_contents('buildArrayOfAppeals.txt', json_encode($array).PHP_EOL, FILE_APPEND | LOCK_EX);

    foreach ($array as $key => $value)
    {
      if($array[$key]->FileName!= 0)
      {
        $array[$key]->FileName = '<div class="download_parent"><div class="btn color_button_black btn-rounded" onclick="dowload(\''.$array[$key]->IncidentId.'\');">Показати вкладення</div></div>';
      }
      else
      {
        $array[$key]->FileName = '';
      }

      $array[$key]->food = trim ( $array[$key]->food ,$characters = " ," );


      $array[$key]->CreatedOn = date('d.m.Y H:i',strtotime($array[$key]->CreatedOn)+$this->time_zone() );
      if(!empty($array[$key]->new_incidentdate))
      {
        $array[$key]->new_incidentdate = date('d.m.Y H:i',strtotime($array[$key]->new_incidentdate)+$this->time_zone() );
      }

      $arr["all"][] = $array[$key];

      switch ($array[$key]->new_requeststatus)
      {
        case '100000000':
        {
          $array[$key]->new_requeststatus = 'Нове';
          $this->enCodeArray($array[$key]);
          $arr["new"][] = $array[$key];
        }
          break;
          case '100000001':
          {
            $array[$key]->new_requeststatus = 'В роботі';
            $this->enCodeArray($array[$key]);
            $arr["inWork"][] = $array[$key];
          }
            break;
          case '100000002':
          {
            $array[$key]->new_requeststatus = 'Прострочені';
            $this->enCodeArray($array[$key]);
            $arr["expired"][] = $array[$key];
          }
            break;
          case '100000003':
          {
            $array[$key]->new_requeststatus = "На зворотній зв'язок КЦ";
            $this->enCodeArray($array[$key]);
            $arr["feedback"][] = $array[$key];
          }
            break;
          case '100000004':
          {
            $array[$key]->new_requeststatus = 'Закрито співробітником McD';
            $this->enCodeArray($array[$key]);
            $arr["closeOper"][] = $array[$key];
          }
            break;
            case '100000005':
            {
              $array[$key]->new_requeststatus = 'Закрито оператором КЦ';
              $this->enCodeArray($array[$key]);
              $arr["closeOper"][] = $array[$key];
            }
              break;
              case '100000006':
              {
                $array[$key]->new_requeststatus = 'На доопрацювання';
                $this->enCodeArray($array[$key]);
                $arr["rework"][] = $array[$key];
              }
                break;
                case '100000007':
                {
                  $array[$key]->new_requeststatus = 'Потребує допомоги PR-відділу';
                  $this->enCodeArray($array[$key]);
                  $arr["helpPR"][] = $array[$key];
                }
                  break;
                case '100000008':
                {
                  $array[$key]->new_requeststatus = 'Потребує допомоги юриста';
                  $this->enCodeArray($array[$key]);
                  $arr["helpLawyer"][] = $array[$key];
                }
                  break;
      }
    }
    /*foreach ($arr as $key => $value)
    {
      $value = !$value ? -1 : $value;
    }*/
    if(!isset($arr))
    {
      return;
    }
    return $arr;
  }



  private function enCodeArray($arrayOfAppeals)      //раскодирует различные поля обращения
  {

      switch ( $arrayOfAppeals->new_requestchannel)
       {
        case '100000000':
          $arrayOfAppeals->new_requestchannel = 'Call-центр';
          break;
        case '100000001':
          $arrayOfAppeals->new_requestchannel = 'Сайт';
        break;
        case '100000002':
          $arrayOfAppeals->new_requestchannel = 'Facebook';
          break;
        case '100000003':
          $arrayOfAppeals->new_requestchannel = 'Instagram';
          break;
        case '100000004':
          $arrayOfAppeals->new_requestchannel = 'Чат-бот';
         break;
        case '100000006':
          $arrayOfAppeals->new_requestchannel = 'McTellus';
          break;
        case '100000005':
          $arrayOfAppeals->new_requestchannel = 'Електронна пошта';
          break;
      }

      switch ($arrayOfAppeals->new_inclusiontype)
      {
        case '100000000':
          $arrayOfAppeals->new_inclusiontype = 'Скло';
          break;
        case '100000001':
          $arrayOfAppeals->new_inclusiontype = 'Пластик';
          break;
        case '100000002':
          $arrayOfAppeals->new_inclusiontype = 'Метал';
          break;
        case '100000003':
          $arrayOfAppeals->new_inclusiontype = 'Папір, картон';
          break;
        case '100000004':
          $arrayOfAppeals->new_inclusiontype = 'Деревина';
          break;
        case '100000005':
          $arrayOfAppeals->new_inclusiontype = 'Тканина (ворс, нитки)';
          break;
        case '100000006':
          $arrayOfAppeals->new_inclusiontype = 'Волосся, нігті';
          break;
        case '100000007':
          $arrayOfAppeals->new_inclusiontype = 'Органічні включення (кістки, очистки, стебла)';
          break;
        case '100000008':
          $arrayOfAppeals->new_inclusiontype = 'Комахи';
          break;
      }

      switch ($arrayOfAppeals->new_prorjurisconsult)
      {
        case '100000001':
          $arrayOfAppeals->new_prorjurisconsult = 'PR-відділ';
          break;
        case '100000002':
          $arrayOfAppeals->new_prorjurisconsult = 'Юрист';
          break;
      }


      switch ($arrayOfAppeals->new_intime)
      {
        case '100000000':
          $arrayOfAppeals->new_intime = 'Ні';
          break;
        case '100000001':
          $arrayOfAppeals->nnew_intime = 'Так';
          break;
      }


      switch ($arrayOfAppeals->new_satisfied)
      {
        case 1:
          $arrayOfAppeals->new_satisfied = 'Так';
          break;
        case 0:
          $arrayOfAppeals->new_satisfied = 'Ні';
          break;
      }

    return $arrayOfAppeals;
  }

  public function listForReport($date_begin=-1, $date_end=-1)   //для отчета (июль)
  {
    $date_begin==-1 ? $date_begin= date('Ym01'):1;                                                                 //промежуток (по умолчанию за текущий месяц)
    $date_end==-1 ? $date_end = date('Ymd 23:59:59'):1;
    
    $this->CI->load->model('Appeal_model');
    $arrayForReport = $this->CI->Appeal_model->report($date_begin, $date_end);

    if(!isset($arrayForReport))
    {
      return;
    }

    foreach ($arrayForReport as $key => $value)
    {
      $arrayForReport[$key]->CreatedOn = date('d.m.Y H:i',strtotime($arrayForReport[$key]->CreatedOn)+10800);
	  switch ($arrayForReport[$key]->new_requeststatus)
      {
        case '100000000':
        {
          $arrayForReport[$key]->new_requeststatus = 'Нове';
        }
          break;
          case '100000001':
          {
            $arrayForReport[$key]->new_requeststatus = 'В роботі';
          }
            break;
          case '100000002':
          {
            $arrayForReport[$key]->new_requeststatus = 'Прострочені';
          }
            break;
          case '100000003':
          {
            $arrayForReport[$key]->new_requeststatus = "На зворотній зв'язок КЦ";
          }
            break;
          case '100000004':
          {
            $arrayForReport[$key]->new_requeststatus = 'Закрито співробітником McD';
          }
            break;
            case '100000005':
            {
              $arrayForReport[$key]->new_requeststatus = 'Закрито оператором КЦ';
            }
              break;
              case '100000006':
              {
                $arrayForReport[$key]->new_requeststatus = 'На доопрацювання';
              }
                break;
                case '100000007':
                {
                  $arrayForReport[$key]->new_requeststatus = 'Потребує допомоги PR-відділу';
                }
                  break;
                case '100000008':
                {
                  $arrayForReport[$key]->new_requeststatus = 'Потребує допомоги юриста';
                }
                  break;
      }
      switch ( $arrayForReport[$key]->new_requestchannel)
       {
        case '100000000':
          $arrayForReport[$key]->new_requestchannel = 'Call-центр';
          break;
        case '100000001':
          $arrayForReport[$key]->new_requestchannel = 'Сайт';
        break;
        case '100000002':
          $arrayForReport[$key]->new_requestchannel = 'Facebook';
          break;
        case '100000003':
          $arrayForReport[$key]->new_requestchannel = 'Instagram';
          break;
        case '100000004':
          $arrayForReport[$key]->new_requestchannel = 'Чат-бот';
         break;
        case '100000006':
          $arrayForReport[$key]->new_requestchannel = 'McTellus';
          break;
        case '100000005':
          $arrayForReport[$key]->new_requestchannel = 'Електронна пошта';
          break;
        }
      }
    return json_decode(json_encode($arrayForReport), TRUE);
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
