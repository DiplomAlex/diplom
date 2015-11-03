<?php
// На этот скрипт приходят уведомления от QIWI Кошелька.
// SoapServer парсит входящий SOAP-запрос, извлекает значения тегов login, password, txn, status,
// помещает их в объект класса Param и вызывает функцию updateBill объекта класса TestServer.

// Логика обработки магазином уведомления должна быть в updateBill.

$s = new SoapServer('cutecms/application/library/App/Service/IShopServerWSService/WSDL/IShopClientWS.wsdl', array('classmap' => array('tns:updateBill' => 'Param', 'tns:updateBillResponse' => 'Response')));
App_debug::log($s); 
$s->setClass('App_Service_IShopServerWSService_Server');
$s->handle();

class Response {
	public $updateBillResult;
}

class Param {
	public $login;
	public $password;
	public $txn;      
	public $status;
}

class App_Service_IShopServerWSService_Server extends SoapClient
{
	function updateBill($param) 
	{
		$qiwi = Zend_Registry::get('config')->qiwi;
		if (strtoupper(md5($param->txn . strtoupper(md5($qiwi->pass)))) == $param->password and $qiwi->login == $param->login) {
		
			$service = Model_Service::factory('checkout/order');
			// В зависимости от статуса счета $param->status меняем статус заказа в магазине
			if ($param->status == 60) {
				// заказ оплачен
				$service->updateStatus($param->txn,5);
			} else if ($param->status > 100) {
				// заказ не оплачен (отменен пользователем, недостаточно средств на балансе и т.п.)
				$service->updateStatus($param->txn,7);
			} else if ($param->status >= 50 && $param->status < 60) {
				// счет в процессе проведения
				$service->updateStatus($param->txn,6);
			} else {
				// неизвестный статус заказа
					$service->updateStatus($param->txn,8);
			}

			// формируем ответ на уведомление
			// если все операции по обновлению статуса заказа в магазине прошли успешно, отвечаем кодом 0
			// $temp->updateBillResult = 0
			// если произошли временные ошибки (например, недоступность БД), отвечаем ненулевым кодом
			// в этом случае QIWI Кошелёк будет периодически посылать повторные уведомления пока не получит код 0
			// или не пройдет 24 часа
			$temp = new Response();
			$temp->updateBillResult = 0;
			return 0;
		
		}else{
			return -1;
		}
	}
 }
?>
