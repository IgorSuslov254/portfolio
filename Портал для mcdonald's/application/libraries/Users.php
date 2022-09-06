<?php
 abstract class Users
{
  protected $CI;
  protected $login;
  protected $id;
  public function __construct($login,$id)
  {
    $this->CI = & get_instance();
    $this->id = $id;
    $this->login = $login;
    $this->CI->load->model('Appeal_model');
    $this->CI->load->model('Clients_model');
    $this->CI->load->model('Calls_model');
    $this->CI->load->model('CRM_model');
    $this->CI->load->model('Payment_model');
  }
  public function getLogin()
  {
    return $this->login;
  }
  public function getId()
  {
    return $this->id;
  }
  static function setRole($login,$Role_name,$id){
		switch ($Role_name){
			case "Офіс":
				$user = new MainOffice($login,$id);
			break;
			case "Операційний менеджер":
				$user = new OpManager($login,$id);
			break;
			case "Консультант":
				$user = new Consultant($login,$id);
			break;
			case "Ресторан":
				$user = new Restaurant($login,$id);
			break;
			case "Модератор":
				$user = new Moderator($login,$id);
			break;
			default:
      {
        print_r("Erorr Of Role");
				return -1;
      }
			break;
		}
		return $user;
	}

  //abstract public function getAppeals($date_begin,$date_end);
}

/**
 * интерфейс для работы с обращениями
 */
interface IAppeals
{
  public function getAppeals($date_begin,$date_end, $addParam = []);
}

interface IAppeals_count
{
  public function getAppeals_count($date_begin,$date_end, $addParam = []);
}

/**
 * интерфейс для получения списка ответов
 */
interface IGetAnswer
{
  public function getListOfAnswers($incident);
}

/**
 * поиск клиентов
 */
interface IFindClient
{
  public function findClients($emailANDPhone);
}

/**
 * перемещение обращения в работу
 */
interface IInWork
{
  public function inWork($ticketNuber);
}

/**
 * получение списка звонков
 */
interface IGetCalls
{
  public function getCalls($date_begin,$date_end);
}

/**
 * изменеие обращения
 */
interface IChangeAppeal
{
  public function changeAppeal($ticketNumber,$arrayOfData);
}

/**
 * добавление клиента
 */
trait TAddClient
{
  public function addClient($data)
  {
    $data['CreatedBy']  = $this->getId();
    return $this->CI->CRM_model->addClient($data);
  }
}

/**
 * добавление обращения
 */
trait TAddAppeal
{
  public function addAppeal($data)
  {
    return $this->CI->CRM_model->addAppeal($data);
  }
}

/**
 *добавление ответа на обращения
 */
trait TAddAnswer
{
  function addAnswer($data)
  {
    return $this->CI->CRM_model->addAnswer($data);
  }
}

/**
 *изменение ответа на обращения
 */
trait TChangeAnswer
{
  function changeAnswer($data)
  {
    return $this->CI->CRM_model->changeAnswer($data);
  }
}

/**
 * создание компенсации
 */
interface ICreatePayment
{
  public function createPayment($data);
}

/**
 * вывод списка компенсаций
 */
trait TGetListOfPayment
{
  function getListOfPayment($date_begin,$date_end) //возвращает список обращений для заданного логина и заданный
  {
    $res = $this->CI->Payment_model->getPaymentList($date_begin,$date_end);
    return $res;
  }
}

/**
 * перевод статуса компенсации в hold
 */
trait TToHold
{
  function toHold($id,$timeUnix)
  {
    return $this->CI->CRM_model->changeStatus($id,'100000001',$timeUnix);
  }
}

/**
 * перевод статуса компенсации в Active
 */
 trait TToActive
 {
   function toActive($id,$timeUnix)
   {
     return $this->CI->CRM_model->changeStatus($id,'100000000',$timeUnix);
   }
 }

 /*
*добавление заявки на изменение режима работы ресторана
*/

trait TChangeWorktime
{
  function changeWorktime($data)
  {
    return $this->CI->CRM_model->changeWorktime($this->getId(), $data);
  }
}

 /*
*добавление заявки на изменение данных о директоре ресторана
*/

trait TChangeDirtector
{
  function changeDirector($data)
  {
    return $this->CI->CRM_model->changeDirector($this->getId(), $data);
  }
}

/**
 * добавление заявки на изменение ресторана
 */
trait TChangeRestrant
{
  function changeRestrant($data)
  {
    return $this->CI->CRM_model->changeRestrant($this->getId(), $data);
  }
}






class MainOffice extends Users implements IAppeals,IGetAnswer,IFindClient,IInWork,IGetCalls,IChangeAppeal
{
  use TAddAnswer,TChangeAnswer,TGetListOfPayment;
  public $nameOfRole = "MainOffice";
  public function __construct($login,$id)
  {
    parent::__construct($login,$id);
  }
  public function getAppeals($date_begin,$date_end, $addParam = [])
  {
      return $this->CI->Appeal_model->getAppealsForOffice($this->login,$date_begin,$date_end, $addParam);
  }
  public function getAppeals_count($date_begin, $date_end, $addParam = [])
  {
    return $this->CI->Appeal_model->getCountsForOffice($this->login,$date_begin,$date_end, $addParam);
  }
  public function getListOfAnswers($incident)
  {
    return $this->CI->Appeal_model->getAppealsAnswers($incident);
  }

  public function findClients($emailANDPhone)
  {
      return $this->CI->Clients_model->findClients($emailANDPhone);
  }

  public function inWork($ticketNuber)
  {
    return $this->CI->Appeal_model->setInWork($ticketNuber);
  }

  public function getCalls($date_begin,$date_end)
  {
    return $this->CI->Calls_model->getCalls($date_begin,$date_end);
  }

  public function changeAppeal($ticketNumber,$arrayOfData)
  {
    return $this->CI->CRM_model->changeAppeal($ticketNumber,$arrayOfData);
  }

}



class OpManager extends Users implements IAppeals,IAppeals_count,IInWork,IChangeAppeal
{
  use TAddAnswer,TChangeAnswer;
  public $nameOfRole = "OpManager";
  public function __construct($login,$id)
  {
    parent::__construct($login,$id);
  }
  public function getAppeals($date_begin,$date_end, $addParam = [])
  {
    return $this->CI->Appeal_model->getAppealsForConsAndOpManager($this->login,$date_begin,$date_end, $addParam);
  }
  public function getAppeals_count($date_begin,$date_end, $addParam = [])
  {
    return $this->CI->Appeal_model->getCountsForConsAndOpManager($this->login,$date_begin,$date_end, $addParam);
  }

  public function inWork($ticketNuber)
  {
    return $this->CI->Appeal_model->setInWork($ticketNuber);
  }
  public function changeAppeal($ticketNumber,$arrayOfData)
  {
    return $this->CI->CRM_model->changeAppeal($ticketNumber,$arrayOfData);
  }

}



class Consultant extends Users implements IAppeals,IInWork,IChangeAppeal
{
  use TAddAnswer,TChangeAnswer;
  public $nameOfRole = "Consultant";
  public function __construct($login,$id)
  {
    parent::__construct($login,$id);
  }
  public function getAppeals($date_begin,$date_end, $addParam = [])
  {
    return $this->CI->Appeal_model->getAppealsForConsAndOpManager($this->login,$date_begin,$date_end, $addParam);
  }
  public function getAppeals_count($date_begin,$date_end, $addParam = [])
  {
    return $this->CI->Appeal_model->getCountsForConsAndOpManager($this->login,$date_begin,$date_end, $addParam);
  }
  public function inWork($ticketNuber)
  {
    return $this->CI->Appeal_model->setInWork($ticketNuber);
  }
  public function changeAppeal($ticketNumber,$arrayOfData)
  {
    return $this->CI->CRM_model->changeAppeal($ticketNumber,$arrayOfData);
  }

}



class Restaurant extends Users implements IAppeals,IGetAnswer,IInWork,IChangeAppeal,ICreatePayment
{
  use TAddAnswer,TChangeAnswer,TGetListOfPayment,TToHold,TToActive,TChangeWorktime,TChangeDirtector,TChangeRestrant;
  public $nameOfRole = "Restaurant";

  public function __construct($login,$id)
  {
    parent::__construct($login,$id);
  }
  public function getAppeals($date_begin,$date_end, $addParam = [])
  {
    return $this->CI->Appeal_model->getAppealsForRestaurant($this->getId(),$date_begin,$date_end, $addParam);
  }
  public function getAppeals_count($date_begin,$date_end, $addParam = [])
  {
    return $this->CI->Appeal_model->getCountsForRestaurant($this->getId(),$date_begin,$date_end, $addParam);
  }
  public function getListOfAnswers($incident)
  {
    return $this->CI->Appeal_model->getAppealsAnswers($incident);
  }

  public function findClients($emailANDPhone)
  {
      return $this->CI->Clients_model->findClients($emailANDPhone);
  }

  public function inWork($ticketNuber)
  {
    return $this->CI->Appeal_model->setInWork($ticketNuber);
  }

  public function changeAppeal($ticketNumber,$arrayOfData)
  {
    return $this->CI->CRM_model->changeAppeal($ticketNumber,$arrayOfData);
  }

  public function createPayment($data)
  {
    $data['id'] = $this->getId();
    return $this->CI->CRM_model->createPayment($data);
  }

  /*public function getListOfPayment($date_begin,$date_end)
  {
    $res = $this->CI->Payment_model->getPaymentList($date_begin,$date_end,$this->getId());
    return $res;
  }*/

}



class Moderator extends Users implements IFindClient,IInWork,IAppeals
{
  use TAddClient, TAddAppeal;
  public $nameOfRole = "Moderator";
  public function __construct($login,$id)
  {
    parent::__construct($login,$id);
  }

  public function findClients($emailANDPhone)
  {
        return $this->CI->Clients_model->findClients($emailANDPhone);
  }
  public function getAppeals_count($date_begin,$date_end, $addParam = [])
  {
    return $this->CI->Appeal_model->getCounts_forModerator($date_begin, $date_end, $addParam);
  }
  public function inWork($ticketNuber)
  {
    return $this->CI->Appeal_model->setInWork($ticketNuber);
  }

  public function getAppeals($date_begin,$date_end, $addParam = [])
  {
    $id = $this->getId();
    return $this->CI->Appeal_model->getAppealsForModerator($id,$date_begin,$date_end);

  }

}
?>
