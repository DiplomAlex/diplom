<?php
class checkBill {
  public $login; // string
  public $password; // string
  public $txn; // string
}

class checkBillResponse {
  public $user; // string
  public $amount; // string
  public $date; // string
  public $lifetime; // string
  public $status; // int
}

class getBillList {
  public $login; // string
  public $password; // string
  public $dateFrom; // string
  public $dateTo; // string
  public $status; // int
}

class getBillListResponse {
  public $txns; // string
  public $count; // int
}

class cancelBill {
  public $login; // string
  public $password; // string
  public $txn; // string
}

class cancelBillResponse {
  public $cancelBillResult; // int
}

class createBill {
  public $login; // string
  public $password; // string
  public $user; // string
  public $amount; // string
  public $comment; // string
  public $txn; // string
  public $lifetime; // string
  public $alarm; // int
  public $create; // boolean
}

class createBillResponse {
  public $createBillResult; // int
}

$qiwi = Zend_Registry::get('config')->qiwi;
define('LOGIN', $qiwi->login);
define('PASSWORD', $qiwi->pass);

// IShopServerWSService class

// @author    {author}
// @copyright {copyright}
// @package   {package}
class App_Service_IShopServerWSService_Soap extends SoapClient
{
	private static $classmap = array(
									'checkBill' => 'checkBill',
									'checkBillResponse' => 'checkBillResponse',
									'getBillList' => 'getBillList',
									'getBillListResponse' => 'getBillListResponse',
									'cancelBill' => 'cancelBill',
									'cancelBillResponse' => 'cancelBillResponse',
									'createBill' => 'createBill',
									'createBillResponse' => 'createBillResponse',
								   );

	public function App_Service_IShopServerWSService_Soap($wsdl = "cutecms/application/library/App/Service/IShopServerWSService/WSDL/IShopServerWS.wsdl", $options = array('location' => 'http://ishop.qiwi.ru/services/ishop', 'trace' => TRACE)) {
		foreach(self::$classmap as $key => $value) {
		  if(!isset($options['classmap'][$key])) {
			$options['classmap'][$key] = $value;
		  }
		}
		parent::__construct($wsdl, $options);
	}
  
	// @param checkBill $parameters
	// @return checkBillResponse
	public function checkBill($txn_id)
	{
		$params = new checkBill();
		$params->login = LOGIN; 
		$params->password = PASSWORD;
		$params->txn = $txn_id; 

		return $this->__soapCall('checkBill', array($params), array(
				'uri' => 'http://server.ishop.mw.ru/',
				'soapaction' => ''
			   )
			);
		return $res->checkBillResult;
	}

	// @param getBillList $parameters
	// @return getBillListResponse
	public function getBillList($dateFrom, $dateTo, $status) 
	{
		$params = new getBillList();
		$params->login = LOGIN; 
		$params->password = PASSWORD;
		$params->dateFrom = $dateFrom; 
		$params->dateTo = $dateTo; 
		$params->status = $status; 

		return $this->__soapCall('getBillList', array($params), array(
				'uri' => 'http://server.ishop.mw.ru/',
				'soapaction' => ''
			   )
			);
	}

	// @param cancelBill $parameters
	// @return cancelBillResponse
	public function cancelBill($txn_id)
	{
		$params = new cancelBill();
		$params->login = LOGIN;
		$params->password = PASSWORD;
		$params->txn = $txn_id;
		
		$res = $this->__soapCall('cancelBill', array($params), array(
				'uri' => 'http://server.ishop.mw.ru/',
				'soapaction' => ''
			   )
			);
	
		return $res->cancelBillResult;
	}

	// @param createBill $parameters
	// @return createBillResponse
	public function createBill($phone, $amount, $txn_id, $comment, $lifetime='07.00.00 00:00:00', $alarm=0, $create=true) 
	{
		$params = new createBill();
		$params->login = LOGIN; // логин
		$params->password = PASSWORD; // пароль
		$params->user = $phone; // пользователь, которому выставляется счет
		$params->amount = ''.$amount; // сумма
		$params->comment = $comment; // комментарий
		$params->txn = $txn_id; // номер заказа
		$params->lifetime = $lifetime; // время жизни (если пусто, используется по умолчанию 30 дней)
		
		// уведомлять пользователя о выставленном счете (0 - нет, 1 - послать СМС, 2 - сделать звонок)
		// уведомления платные для магазина, доступны только магазинам, зарегистрированным по схеме "Именной кошелёк"
		$params->alarm = $alarm; 

		// выставлять счет незарегистрированному пользователю
		// false - возвращать ошибку в случае, если пользователь не зарегистрирован
		// true - выставлять счет всегда
		$params->create = $create;
	
		$res = $this->__soapCall('createBill', array($params), array(
				'uri' => 'http://server.ishop.mw.ru/',
				'soapaction' => ''
			   )
			);
		return $res->createBillResult;
	}
}
?>
