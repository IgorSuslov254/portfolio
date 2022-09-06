<?php
class Clients
{
  private $CI;

  public function __construct()
  {
   $this->CI =& get_instance();
   $this->CI->load->model('Clients_model');
  }

  function findClients($user,$emailANDPhone)
  {
    return $user->findClients($emailANDPhone);
  }

  function appealsAndCallsOfClients($idClient){
    $ap = new Appeals();
    $appeals = $ap->getAppealsOfClient($idClient);

    // $result1['appeals'] = $appeals; 
    
    if(!empty($appeals))
    {
      foreach ($appeals as $key => $value)            //убираю деление обращений на категории
      {
        if ($key == 'all') continue;
        foreach ($appeals[$key] as $key1 => $value1)
        {
          $result1['appeals'][]=$appeals[$key][$key1];
        }
      }
    }
    else
    {
      $result1['appeals'] = '';
    }
    

    //return $this->CI->Clients_model->getAppealsAndCalls($idClient);
    $call = new Calls();
    $result1['calls'] = $call->getCallsOfClient($idClient);
    return $result1;
  }
}

?>
