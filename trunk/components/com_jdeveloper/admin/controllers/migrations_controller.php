<?php
defined('_JEXEC') or die('Restricted access');
class ControllerMigrations extends ComponentController
{
	

	public function migrate()
	{
		$ver = JRequest::getVar('ver',null);
		if ( ! is_null($ver) ) {
			$this->getModel('migration')->migrate($ver);
		}
		$this->setRedirect('back');
				
	}
	public function default_()
	{
		$this->assign(array('migration'=>$this->getModel('migration')));
	
	}	
	public function addMigration()
	{
		$name = JRequest::getVar('name',false);
		if (!$name) 
		{
			$this->setRedirect('back','Invalid Migration Name','error');	
			return;
		}
		
		$migration = $this->getModel('migration')->create($name);
		$this->setRedirect('back','Migration File Created','message');	

	}
	public function getViewName()
	{		
		return 'migrations';	
	}
	
}