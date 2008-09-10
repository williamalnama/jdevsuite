<?php
defined('_JEXEC') or die('Restricted access');
class ControllerExtensionHandler extends ComponentController
{
	public function __construct()
	{
		parent::__construct();
		$type = JRequest::getVar('ext','component');
		try {
			
			$this->model = $this->getModel('extensionhandler',null,array('type'=>$type));
			
		}catch(EXCEPTION $e){
			
			
		}

	}
	public function create()
	{
		$name  = JRequest::getVar('name',null);		
		
		if ( !is_null($name) )
			$this->model->create($name);
			
		$this->setRedirect('back');
		
	}
	public function uninstall()
	{
		$ext  = JRequest::getVar('name');
		$this->model->uninstall($ext);	
		$this->default_();
//		$this->setRedirect('back');	
	}
	public function install()
	{			
		$ext  = JRequest::getVar('name');				
		$this->model->install($ext);
		$this->default_();
		//$this->setRedirect('back');
	}
	public function default_()
	{					
		$this->assign(array('model'=>$this->model));
		$this->setLayout($this->model->type.'_default');	
	}
	public function getViewName()
	{
		return 'extensionhandler';
	}


}