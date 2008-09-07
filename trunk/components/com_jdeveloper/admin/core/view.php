<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');

class ComponentView extends JView
{
	public function defaultLayout()
	{
		global $option;

		$controller = strtolower( JRequest::getString('controller') );
		$ext 		= strtolower( JRequest::getString('ext') );
		
		JSubMenuHelper::addEntry( 'Migrations',	urlfor(array('controller'=>'migrations')), 	($controller == 'migrations'));
		JSubMenuHelper::addEntry( 'Components',	urlfor(array('ext'=>'component','controller'=>'extensionhandler')), 	($ext == 'components'));
		JSubMenuHelper::addEntry( 'Modules',	urlfor(array('ext'=>'module','controller'=>'extensionhandler')), 	($ext == 'module'));
		JSubMenuHelper::addEntry( 'Plugins',	urlfor(array('ext'=>'plugin','controller'=>'extensionhandler')), 	($ext == 'plugin'));				
		JSubMenuHelper::addEntry( 'Configuration',	urlfor(array('controller'=>'config')), 	($controller == 'config'));
	}	
	public final function __construct()
	{
		parent::__construct();	
		$methods = array('initializeLayout',JRequest::getVar('layout',$this->getLayout()).'Layout');
		print_r(JRequest::getVar('layout'));
		foreach($methods as $method)
			if ( method_exists($this,$method) ) 
				$this->$method();
	}
	
	
}