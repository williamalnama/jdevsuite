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
		$extensions = $this->packageModel->getElements();
		$this->view->assign('extensions',$extensions);
	}
	public function create()
	{
		$this->packageModel->package();
		$this->setRedirect(array('task'=>'default'),'Package created successfully');
		//package everything	
	}
	public function package()
	{
		$types = JRequest::getVar('extension');
		$extensions = array();
		foreach($types as $type) {
			if (strpos($type,'_') > 0) {	
				list($t,$n) = explode('_',$type,2);
				$t = Inflector::singularize($t);
				$extensions[] = array('type'=>$t,'name'=>$n);
			}
		}
		$name = JRequest::getVar('package_name');
		$this->packageModel->package($extensions,$name);
		$this->setRedirect(array('task'=>'default'),'Package created successfully');
	}

}