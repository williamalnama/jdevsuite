<?php
defined('_JEXEC') or die('Restricted access');



class ControllerExtensionHandler extends ComponentController
{
	public function __construct()
	{
		parent::__construct();
		$this->extensionHandlerModel = $this->getModel('extensionhandler','',$this->configModel);
		$this->extensionHandlerModel->setState('extension_type',JRequest::getVar('ext'));

	}
	public function create()
	{
		$name  = JRequest::getVar('name');	
		
		if ( !is_null($name) )		
			$this->extensionHandlerModel->create($name);
	
		$this->setRedirect(array('ext'=>$this->extensionHandlerModel->getExtensionType()));		

	}
	public function uninstall()
	{
		$name  = JRequest::getVar('name');			
		$extension  = $this->extensionHandlerModel->getExtension($name);
		$this->extensionHandlerModel->uninstall($extension);
		$installer = JInstaller::getInstance();
		$msg = $installer->get('message').'<br>'.$installer->get('extension.message').'<br>';
		$this->setRedirect(array('ext'=>$this->extensionHandlerModel->getExtensionType()),'Extension UnInstalled',$msg);

	}
	public function install()
	{			
		$name  = JRequest::getVar('name');
		$extension  = $this->extensionHandlerModel->getExtension($name);
		$this->extensionHandlerModel->install($extension);
		$installer = JInstaller::getInstance();
		$msg = $installer->get('message').'<br>'.$installer->get('extension.message').'<br>';
		$this->setRedirect(array('ext'=>$this->extensionHandlerModel->getExtensionType()),'Extension Installed',$msg);
	}
	
	public function default_()
	{			

		$extensions = $this->extensionHandlerModel->getExtensions();
		$this->setLayout($this->extensionHandlerModel->getExtensionType().'_default');
		$this->view->assignRef('extensions',$extensions);
		
	}


}