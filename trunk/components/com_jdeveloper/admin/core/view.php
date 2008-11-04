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

		$this->createProjectsDropDown();
						
		JSubMenuHelper::addEntry( 'Migrations',	urlfor(array('controller'=>'migrations')), 	($controller == 'migrations'));
		JSubMenuHelper::addEntry( 'Components',	urlfor(array('ext'=>'component','controller'=>'extensionhandler')), 	($ext == 'component'));
//		JSubMenuHelper::addEntry( 'Modules',	urlfor(array('ext'=>'module','controller'=>'extensionhandler')), 	($ext == 'module'));
		JSubMenuHelper::addEntry( 'Plugins',	urlfor(array('ext'=>'plugin','controller'=>'extensionhandler')), 	($ext == 'plugin'));				
//		JSubMenuHelper::addEntry( 'Languages',	urlfor(array('ext'=>'language','controller'=>'extensionhandler')), 	($ext == 'language'));	
		JSubMenuHelper::addEntry( 'Configuration',	urlfor(array('controller'=>'config')), 	($controller == 'config'));
		JSubMenuHelper::addEntry( 'About',	urlfor(array('controller'=>'about')), 	($controller == 'about'));		
	}
	public function createProjectsDropDown()
	{	
		if ( count($this->projects) == 0 )
			return;
			
		$selectProject = '<select id="project-selector">';
		
		foreach($this->projects as $project) 
			if ($project->name == $this->currentProject->name)
				$selectProject .= "<option selected value='{$project->folder}' >{$project->name}</option>";
			else
				$selectProject .= "<option value='{$project->folder}' >{$project->name}</option>";
		
		$selectProject .= '</select>

				<script>
					$("project-selector").getParent().removeProperty("href");
					$("project-selector").addEvent("change",function() {
							window.location="'.urlfor(array('task'=>JRequest::getVar('task'),'ext'=>JRequest::getVar('ext'))).'&project_id=" + this.value
					})
				</script>
		
		';
		JSubMenuHelper::addEntry($selectProject,"#",false);	
	
	}
	public function display()
	{
		
		$methods = array('initializeLayout',JRequest::getVar('layout',$this->getLayout()).'Layout');		
		
		foreach($methods as $method)
			if ( method_exists($this,$method) ) 
				$this->$method();
		
		parent::display();
		
	}
	
}