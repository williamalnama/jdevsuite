<?php

class JDeveloperControllerMigration extends ComponentController
{
	public $migrationModel = null;
	
	public function __construct($options=array())
	{
		parent::__construct($options);
		$this->migrationModel = KFactory::get('site::com.jdeveloper.model.migration');
	}
	
	public function display()
	{				
		parent::display();
	}
	
	public function migrate()
	{
		$ver = JRequest::getVar('ver',null);
		if ( ! is_null($ver) ) {
			
				$this->migrationModel->migrate($ver);
			
		}
		//$this->setRedirect('back');
///		$this->assign(array('migrationOutput'=>$output));
		$this->default_();
				
	}
	public function default_()
	{
		
		$this->assign(array('migration'=>$this->migrationModel));
	
	}	
	public function addMigration()
	{
		$name = JRequest::getVar('name',false);
		if (!$name) 
		{
			$this->setRedirect('back','Invalid Migration Name','error');	
			return;
		}
		
		$migration = $this->migrationModel->create($name);
		$this->setRedirect('back','Migration File Created','message');	

	}

	
}