<?php 
// no direct access
	defined('_JEXEC') or die('Restricted access');


			
	//load jcore
	jimport('joomla.filesystem.file');
	jimport('joomla.application.component.model');
	jimport('joomla.application.component.view');
	jimport('joomla.application.component.controller');

	//load locals
	JLoader::import('controller',	JPATH_COMPONENT.DS.'core');
	JLoader::import('view', 		JPATH_COMPONENT.DS.'core');
	
	$libFolder = JPATH_COMPONENT.DS.'core'.DS.'lib';
	
	foreach(JFolder::files($libFolder) as $file)	
		require_once $libFolder.DS.$file;
	
	//dispatch
	$controller = JRequest::getVar('controller',null);
	
	if ( !$controller ) {
		$controller='migrations';
		JRequest::setVar('controller','migrations');
	}
	
	JRequest::setVar('view', $controller);
	
	if ( JFile::exists(JPATH_COMPONENT.DS.'controllers'.DS.$controller.'_controller.php') ) 
	{
				
		JLoader::import( $controller.'_controller' , JPATH_COMPONENT.DS.'controllers' );

		$class = new ReflectionClass('Controller'.preg_replace('/_/','',ucfirst($controller)));
		$controller = $class->newInstance();

		$task = JRequest::getCmd('task',JRequest::getCmd('layout','default'));

		if ($task == '') $task = 'default';
		
		if ($task == 'default')
			$task = 'default_';
			
		$controller->execute($task);

		if ( $controller->_doTask != 'display' && !$controller->redirect()) {
			$controller->view->display();
		}
				
//		$controller->redirect();
		
	} else {

		JError::raiseError(400,"Invalid Controller {$controller}");
	}
	

