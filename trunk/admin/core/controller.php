<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');

class ComponentController extends JController
{
	public function __construct()
	{
		parent::__construct();
		if ( !$this->getModel('config')->devFolderExists() && strtolower(JRequest::getString('task')) != 'setdevfolder' ){
			
			$this->setRedirect(array('controller'=>'config','task'=>'setDevFolder'));
		}
			
	}

 	public function setRedirect($url,$msg='',$type='message')
  	{  
		if ($url == 'back')
			$url = @$_SERVER['HTTP_REFERER'];
			
		return parent::setRedirect(urlFor($url),$msg,$type);
  	}
	public function assign($vars=array())
	{	
		$view =& $this->getView($this->getViewName(),'html');
		
		foreach($vars as $k=>$v)
			$view->$k = $v;	
				
	}	
	public function setLayout($layout)
	{	
		$view =& $this->getView($this->getViewName(),'html');
		JRequest::setVar('layout',$layout);
		$view->setLayout($layout);
	}
	
}
