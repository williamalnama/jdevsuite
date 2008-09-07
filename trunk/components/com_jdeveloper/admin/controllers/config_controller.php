<?php
defined('_JEXEC') or die('Restricted access');
class ControllerConfig  extends ComponentController
{	
	public function __construct()
	{
		parent::__construct();
		$this->configModel = $this->getModel('config');
		$this->registerTask('setdevfolder','default_');
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
	public function setDevFolderPosted()
	{
		$dir = trim(JRequest::getVar('dev_dir'));

		if ( !$this->configModel->setDevFolder($dir) )
			JError::raiseNotice(500,$this->configModel->getError());
		else {				
			$this->setRedirect(array('task'=>'default'),'Folder created. Make sure you delete your old folder','message');
			return;
		}
		$this->setRedirect(array('task'=>'default'));
	}
	public function getViewName()
	{
		return 'config';	
	}
	
}