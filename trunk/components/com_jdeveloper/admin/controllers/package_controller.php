<?php
defined('_JEXEC') or die('Restricted access');

class ControllerPackage  extends ComponentController
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
		//package everything	
	}

}