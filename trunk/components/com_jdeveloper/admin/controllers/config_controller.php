<?php
defined('_JEXEC') or die('Restricted access');

class ControllerConfig  extends ComponentController
{	
	public function __construct()
	{
		parent::__construct();
		$this->configModel = $this->getModel('config');
		
	}
	public function reset()
	{
		
		$this->configModel->reset();
		$this->setRedirect(array('task'=>'default'));
		
	}
	public function default_()
	{
		require_once JPATH_COMPONENT.DS.'updater.php';
		
		if ( !$this->configModel->devFolderExists() )
			JError::raiseNotice(500,jtext::_('You need to create a developer folder'));
		
		$updater = new Updater();
		if ( $updater->newVersionAvailable() ) 
		{			
			$msg = sprintf("New Version %s Avaiable. <a href='%s'>Update Now!</a>",
							$updater->getLatestVersion(),
							urlfor(array('update'=>$updater->getLatestVersion())));
							
			JError::raiseNotice(500,$msg);
		}
		$this->assign(array('config'=>$this->configModel,'updater'=>$updater));
		$this->setLayout('default');
						
	}
	public function changeDevFolder()
	{
		
		$this->changeFolder = true;
		$this->setDevFolder();
	}
	public function setDevFolder()
	{
		$dir = trim(JRequest::getVar('dev_dir'));
		
		if ( !$this->configModel->setDevFolder($dir) ) {
			JError::raiseNotice(500,$this->configModel->getError());
			$this->setRedirect(array('task'=>'default'));
		}
		else {				
		
			$msg = isset($this->changeFolder) ? jtext('Development folder has been moved to the new destination. You must remove the old one manually') :
												jtext('Development folder has been created');
												
			$this->setRedirect(array('task'=>'default'),$msg,'message');
			
		}
	
	}
	public function getViewName()
	{
		return 'config';	
	}
	
}