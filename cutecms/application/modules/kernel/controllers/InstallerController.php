<?php

class InstallerController extends Zend_Controller_Action
{

	protected $_session = NULL;

	protected function _session( )
	{
		if ( $this->_session === NULL )
		{
			$this->_session = new Zend_Session_Namespace( );
		}
		return $this->_session;
	}

	public function init( )
	{
		$this->view->layout( )->setLayout( 'installer' );
	}

	public function indexAction( )
	{
		$form = new Form_Installer( );
		$service = Model_Service::factory( 'installer' );
		if ( !$this->getRequest( )->isPost( ) )
		{
			$form->populate( $service->getDefaultValues( ) );
			$this->view->form = $form;
		}
		else
		{
			$values = $this->getRequest( )->getParams( );
			if ( $form->isValid( $values ) )
			{
				$this->_session( )->configOptions = $values;
				$this->getHelper( 'Redirector' )->gotoUrlAndExit( $this->view->stdUrl( NULL, 'confirm', 'installer' ) );
			}
			else
			{
				$form->populate( $values );
				$this->view->form = $form;
			}
		}
	}

	public function confirmAction( )
	{
		$form = new App_Form_Question( );
		$form->setMethod( 'post' );
		$this->view->options = $this->_session( )->configOptions;
		if ( $this->getRequest( )->isPost( ) )
		{
			if ( $form->getAnswer( ) == 'yes' )
			{
				try
				{
					$msg = Model_Service::factory( 'installer' )->install( $this->_session( )->configOptions );
					$this->_session( )->installed = TRUE;
				}
				catch ( Exception $e )
				{
					$this->_session( )->installed = FALSE;
					$msg = $e->getMessage( );
				}
				if ( !is_bool( $msg ) && $msg !== TRUE )
				{
					$this->_session( )->errorMessage = $msg;
					$this->getHelper( 'Redirector' )->gotoUrlAndExit( $this->view->stdUrl( NULL, 'cancel', 'installer' ) );
				}
				else
				{
					$this->getHelper( 'Redirector' )->gotoUrlAndExit( $this->view->stdUrl( NULL, 'finish', 'installer' ) );
				}
			}
			else
			{
				$this->getHelper( 'Redirector' )->gotoUrlAndExit( $this->view->stdUrl( NULL, 'cancel', 'installer' ) );
			}
		}
		$this->view->form = $form;
	}

	public function cancelAction( )
	{
		$this->view->errorMessage = $this->_session( )->errorMessage;
	}

	public function finishAction( )
	{
		$this->view->options = $this->_session( )->configOptions;
		$this->view->success = $this->_session( )->installed;
		Model_Service::factory( 'installer' )->finish();
	}

}

