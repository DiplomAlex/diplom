<?php
// �� ���� ������ �������� ����������� �� QIWI ��������.
// SoapServer ������ �������� SOAP-������, ��������� �������� ����� login, password, txn, status,
// �������� �� � ������ ������ Param � �������� ������� updateBill ������� ������ TestServer.

// ������ ��������� ��������� ����������� ������ ���� � updateBill.

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
			// � ����������� �� ������� ����� $param->status ������ ������ ������ � ��������
			if ($param->status == 60) {
				// ����� �������
				$service->updateStatus($param->txn,5);
			} else if ($param->status > 100) {
				// ����� �� ������� (������� �������������, ������������ ������� �� ������� � �.�.)
				$service->updateStatus($param->txn,7);
			} else if ($param->status >= 50 && $param->status < 60) {
				// ���� � �������� ����������
				$service->updateStatus($param->txn,6);
			} else {
				// ����������� ������ ������
					$service->updateStatus($param->txn,8);
			}

			// ��������� ����� �� �����������
			// ���� ��� �������� �� ���������� ������� ������ � �������� ������ �������, �������� ����� 0
			// $temp->updateBillResult = 0
			// ���� ��������� ��������� ������ (��������, ������������� ��), �������� ��������� �����
			// � ���� ������ QIWI ������ ����� ������������ �������� ��������� ����������� ���� �� ������� ��� 0
			// ��� �� ������� 24 ����
			$temp = new Response();
			$temp->updateBillResult = 0;
			return 0;
		
		}else{
			return -1;
		}
	}
 }
?>
