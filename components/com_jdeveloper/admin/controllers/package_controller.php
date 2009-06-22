<?php
defined('_JEXEC') or die('Restricted access');

class JDevControllerPackage  extends ComponentController
{	

	/**
	 * 
	 */
	private $packageModel = null;
		
	public function __construct()
	{
		parent::__construct();

		$extensionHandlerModel = $this->getModel('extensionhandler','',$this->configModel);		
		
		$this->packageModel = $this->getModel('package','',array($this->configModel,$extensionHandlerModel));
			
	}	
	public function default_()
	{
		
	}
	public function create()
	{
		$this->packageModel->package();
		$this->setRedirect(array('task'=>'default'),'Package created successfully');
		//package everything	
	}

}