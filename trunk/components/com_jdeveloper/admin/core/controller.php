<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');

class ComponentController extends JController
{
	/**
	 * configuration service
	 */
	public $configModel = null;
	
	/**
	 * view object
	 */
	public $view		= null;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->configModel =  $this->getModel('config');
		
	    $this->view		   =& $this->getView($this->getViewName(),'html');
		
		//if dev folder doesn't exists redirect user to the config to create
		//new dev folder
		if ( !$this->configModel->devFolderExists() && strtolower(JRequest::getString('controller')) != 'config' ){			
			$this->setRedirect(array('controller'=>'config'));
		} else {	

			$projects    = $this->configModel->getProjects();	
			
			try {			
				$projectId = JRequest::getVar('project_id', JFactory::getSession()->get('project_id'));				
				$currentProject = $this->configModel->getProject($projectId);
			} catch(Exception $e) {
				$currentProject = $this->configModel->getCurrentProject();
			}
			JFactory::getSession()->set('project_id',$currentProject->folder);
			$this->configModel->setState('current_project',$currentProject);			

			
			$this->view->assignRef('currentProject',$currentProject);
			$this->view->assignRef('projects',$projects);

		}
			
	}
	/**
	 * optimized redirect for fast redirect
	 * @return 
	 * @param $url Object
	 * @param $msg Object[optional]
	 * @param $type Object[optional]
	 */
 	public function setRedirect($url,$msg='',$type='message')
  	{  
		if ($url == 'back')
			$url = @$_SERVER['HTTP_REFERER'];
	
		return parent::setRedirect(urlFor($url),$msg,$type);
  	}
	
	/**
	 * returns infer the view name based on the controller name
	 * @return 
	 */
	public function getViewName()
	{			
		return strtolower(str_replace('Controller','',get_class($this)));
	}	
	
	
	/**
	 * assign variables to the view fast
	 * @return 
	 * @param $vars Object[optional]
	 */
	public function assign($vars=array())
	{			
		foreach($vars as $k=>$v) {		
			$this->view->assignRef($k,$v);
		}
	}	
	

	/**
	 * sets out the view layout fast
	 * @return 
	 * @param $layout Object
	 */
	public function setLayout($layout)
	{	
		$this->view->setLayout($layout);
	}
	
}
