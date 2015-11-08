<?php
class Lab_MyCabinetController extends Zend_Controller_Action
{	  
	protected $_defaultInjections = array(
        'Form_ChangePasswordr' => 'Lab_Form_ChangePassword',
		'Form_ChangeInfo' => 'Lab_Form_ChangeInfo',
    );
	
	protected $_injector = NULL;
	 
	public function getInjector()
    {
        if (($this->_injector === NULL) AND ( ! $this->_injector = $this->_getParam('injector'))) {
            $this->_injector = new App_DIContainer($this);
            foreach ($this->_defaultInjections as $interface=>$class) {
                $this->_injector->inject($interface, $class);
            }
        }
        return $this->_injector;
    }
	
    public function init()
    {
        App_Event::factory('Lab_Controller__init', array($this))->dispatch();
		$this->view->flag_right_colum = 2;
		$this->view->headTitle('Личный кабинет');
	}
    
    public function indexAction()
    {

    }
    
    public function personsAction()
    {
		$form = $this->getInjector()->getObject('Form_ChangeInfo');
		$servise = Model_Service::factory('user');
		$user = $servise->getCurrent();
		if ($servise->isAuthorized()){
			if ( ! $this->getRequest()->isPost()) {
				$values['firstname'] = $user['firstname'];
				$values['fathersname'] = $user['fathersname'];;
				$values['lastname'] = $user['lastname'];
				$values['email_address'] = $user['email'];
				$values['city'] = $user['address'];
				$values['telephone'] = $user['tel'];
				$form->populate($values);
				$this->view->form = $form;
			}else{
				$values = $this->getRequest()->getParams();
				
				$this->view->form = $form;
				if ($form->isValid($values)){
					$ar['id'] = $user['id'];
					$ar['name'] = $values['firstname'].' '.$values['fathersname'].' '.$values['lastname'];
					$ar['firstname'] = $values['firstname'];
					$ar['fathersname'] = $values['fathersname'];
					$ar['lastname'] = $values['lastname'];
					$ar['email'] = $values['email_address'];
					$ar['tel'] = $values['telephone'];
					$ar['login'] = $values['email_address'];
					$user = Model_Service::factory('user')->registerNewUser($ar);
					$this->view->sucsessUpdete = TRUE;
					Model_Service::factory('user')->renewCurrent();
                    $this->getHelper('Redirector')->gotoUrlAndExit($this->view->url(array(), 'lab-my_cabinet'));
				}
			}
		}else{
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array(), 'index', 'index'));
		}
    }
    
    public function personssaveAction()
    {
        $Auth = Model_Service::factory('user')->isAuthorized();
		if ($Auth){
		$values = $this->getRequest()->getParams();
		$form = $this->getInjector()->getObject('Form_ChangeInfo');
		if ($this->getRequest()->isPost()) {
		   if ($form->isValid($values)){
				$ar['id'] = $values['id'];
				$ar['name'] = $values['firstname'].' '.$values['fathersname'].' '.$values['lastname'];
				$ar['firstname'] = $values['firstname'];
				$ar['fathersname'] = $values['fathersname'];
				$ar['lastname'] = $values['lastname'];
				$ar['email'] = $values['email_address'];
				$ar['tel'] = $values['telephone'];
				$ar['login'] = $values['email_address'];
				$user = Model_Service::factory('user')->registerNewUser($ar);
				$this->view->sucsessUpdete = TRUE;
               $this->getHelper('Redirector')->gotoUrlAndExit($this->view->url(array(), 'lab-my_cabinet'));
			}
		}
		}else{
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array(), 'index', 'index'));
		}
    }
    
    public function changepasswordAction()
    {
		$form = $this->getInjector()->getObject('Form_ChangePasswordr');
		$this->view->form = $form;
		
        $Auth = Model_Service::factory('user')->isAuthorized();
		if ($Auth){
			$user = Model_Service::factory('user')->getCurrent();
			$this->view->user = $user;
			
			if($this->getRequest()->isPost()){
				$values = $this->getRequest()->getParams();
				if ($form->isValid($values)){
					if(md5($values['password_current']) == $user['password']){
						if($values['password_new'] == $values['password_confirmation']){
						   $ar['id'] = $user['id'];
						   $ar['password'] = $values['password_new'];
						   $user = Model_Service::factory('user')->registerNewUser($ar);
						   $this->view->sucsessUpdetePass = TRUE;
						   $this->_forward('index', 'MyCabinet');
						}else{
						$this->view->erorUpPasswordNew = TRUE;
						}
					}else{
						$this->view->erorUpPasswordOld = TRUE;
					}
				}
			}
		}else{
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array(), 'index', 'index'));
		}
    }
}