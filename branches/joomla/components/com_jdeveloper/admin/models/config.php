<?php 

jimport('joomla.utilities.simplexml');

class JDeveloperModelConfig extends KModelDefault
{
	public $defaultDevFolder  = null;
	public $xmlConfig 		  = null;
	public $currentProject    = null; 
	
	static $instance 		  = null;
	
	public function __construct($options=array()) 	
	{		
		parent::__construct(array());
		$this->xmlConfig =  (simplexml_load_file(JPATH_COMPONENT.DS.'config.xml'));
		$this->defaultDevFolder = JPATH_COMPONENT.DS.'jdevfolder';
//		$this->setDevFolderPermission();
	}
	
	/**
	 * Sets the dev folder persmission recursively
	 * @return 
	 * @param $permission Object[optional]
	 */
	public function setDevFolderPermission($permission='777')
	{
		if ( $this->devFolderExists() )	
			JPath::setPermissions($this->getDevFolder(),'0'.$permission,'0'.$permission);
	}
	
	/**
	 * returns the full path to a subfolder or file within the dev folder
	 * @return 
	 * @param $relativePath Object
	 */
	public function getDevPath()
	{		
		$parts = func_get_args();
		$relativePath = implode(DS,$parts);
		return $this->getDevFolder().DS.$relativePath;
	}
	
	/**
	 * insert a key/value pair in the config.xml file
	 * @return 
	 * @param $k Object
	 * @param $v Object
	 */
	public function setConfig($k,$v="\n")
	{
		$this->xmlConfig->addChild($k,$v);
		JFile::write(JPATH_COMPONENT.DS.'config.xml',pretifyXML($this->xmlConfig));	
	}	
	
	/**
	 * saves the config file as is
	 * @return 
	 */
	public function saveConfig()
	{
		JFile::write(JPATH_COMPONENT.DS.'config.xml',pretifyXML($this->xmlConfig));	
	}
	
	/**
	 * 
	 * @return 
	 * @param $project Object
	 */
	public function projectXmlPointer($project)
	{
		$xmlKey = 'projects';
		
		if ( !($projectsNode = $this->xmlConfig->$xmlKey) ) {
			$projectsNode = $this->xmlConfig->addChild($xmlKey);
		}
		$projectNode = null;
		foreach($projectsNode->children() as $node) {
			if ( $node['name'] == $project->name ) {
				$projectNode = $node;
				break;	
			}
		}
		if ( !$projectNode ) {
			$projectNode = $projectsNode->addChild('project')->addAttribute('name',$project->name);
			$this->saveConfig();
		}
		return $projectNode;
		
	}
	
	
	/**
	 * 
	 * @return 
	 * @param $key Object
	 * @param $project Object[optional]
	 */
	public function projectConfigValue($key,$defaultValue=null,$project=null)
	{
		if ( !$project )
			$project = 	$this->getCurrentProject();
			
		$xml = $this->projectXmlPointer($project);
		return isset($xml->$key) ? $xml->$key : $defaultValue;
	}
	/**
	 * 
	 * @return 
	 * @param $projectName Object
	 */
	public function setProjectConfigValue($key,$value,$project=null)
	{
		if ( !$project )
			$project = 	$this->getCurrentProject();
			
		$xml = $this->projectXmlPointer($project);
		$xml->$key = $value;
		$this->saveConfig();
		
	}
	
	/**
	 * creates a project
	 * @return 
	 * @param $projectName Object
	 */
	public function createProject($projectName)
	{
		//if developer folder doesn't exists don't create a project
		
		if ( !$this->devFolderExists() ) {
			$this->setError('Unable to create a project. Please create a developer folder first.');
			return false;
		}
		
		$projectName = preg_replace('/0-9a-zA-z /','',$projectName);

		if ( count($projectName) == 0 ) {
			$this->setError('Unable to create a project. Please make sure there is at least one character in the project name.');			
			return false;
		}
		
		$projectFolderName = KInflector::underscore(Inflector::camelize($projectName));
		JFolder::create($this->getDevPath($projectFolderName));
	}
	
	/**
	 * return an array of projects within the dev folder
	 * @return 
	 */
	public function getProjects()
	{		
		if ( !isset($this->projects) ) {
			
			$folders = JFolder::folders($this->getDevFolder());
			$projects = array();
			foreach($folders as $folder)
			{
				$project = new stdClass();
				$project->name   = ucfirst($folder);
				$project->folder = $folder;
				$project->path   = $this->getDevPath($folder);
				$projects[] = $project;
			}	
			$this->projects = $projects;					
		}
		return $this->projects;
	}
	
	/**
	 * return a project based on the projectFolderName if not found returns the first project in the list
	 * @return 
	 * @param $projectFolderName Object
	 */
	public function getProject($projectFolderName)
	{
		$projects = $this->getProjects();
		
		if ( count($projects) == 0 )
			throw new Exception('There are no projects in the dev folder');
											
		$target = null;
		foreach($projects as $project)
			if ( $project->folder == $projectFolderName ) {
			 	$target =& $project;
				break;
			}
			
		if ( !$target )
			throw new Exception("{$projectFolderName} doesn't exists");
		
		return $target;
	}
	/**
	 * return a project saved in the state if not found gets the first project
	 * @return 
	 */
	public function getCurrentProject()
	{
		$project = $this->getState('current_project', null );
		if ( !$project ) {
			$projects = $this->getProjects();
			$project = $projects[0];
		}
		
		if ( !$this->xmlConfig->projects )
			$this->xmlConfig->addChild('projects');
			
		foreach($this->xmlConfig->projects as $projectNode )
		{
//			$projectNode->
			
		}
		return $project;
	}
	
	/**
	 * @return 
	 */
	public function getProjectPath()
	{
	
		$project = $this->getCurrentProject();
			
		$parts = func_get_args();
		$relativePath = implode(DS,$parts);
		$path = $project->path.DS.$relativePath;
		if ( !JFolder::exists($path) )
			JFolder::create($path);
		
		$this->setDevFolderPermission();
		return $path;
	}	
	
	/**
	 * resets joomla to the initial state right after the installation
	 * @return 
	 */
	public function reset()
	{
		jimport('joomla.installer.helper');
		$db = $this->getDBO();
		$sessionId = JFactory::getSession()->getId();
		$q = sprintf("SELECT * FROM #__session WHERE session_id LIKE '%s' LIMIT 1",$sessionId);
		$db->execute($q);
		$userId = $db->loadObject()->userid;
		$user = JFactory::getUser($userId);
		$user->id = null;
		
		$db->execute("SHOW TABLES");
		$tables = $db->loadResultArray();
		
		foreach($tables as $t) {
			if (preg_match('/session/',$t)) 
				continue;
				
			$db->execute("DROP TABLE ".$t);
		}
		
		$sql = JFile::read(dirname(__FILE__).DS.'sql'.DS.'reset.sql');
		$queries = JInstallerHelper::splitsql($sql);
		
		foreach($queries as $q)
			$db->execute($q);
		
		$user->save();

		$db->execute("INSERT INTO `#__components` VALUES (null,'Joomla Developer','option=com_jdeveloper',0,0,'option=com_jdeveloper','Joomla Developer','com_jdeveloper',0,'js/ThemeOffice/component.png',0,'',1)");			
		
		$q = sprintf("UPDATE #__session SET userid = %s WHERE session_id LIKE '%s' LIMIT 1",$user->id,$sessionId);
		$db->execute($q);
		$this->setMigrationVersion(0);
		
	}
	/**
	 * get the current migration version
	 * @return 
	 */
	public function getMigrationVersion()
	{
		return (int) $this->xmlConfig->migration;		
	}
	/**
	 * set the current migration version
	 * @return 
	 * @param $ver Object
	 */
	public function setMigrationVersion($ver)
	{
		$this->xmlConfig->migration = (int) $ver;
		JFile::write(JPATH_COMPONENT.DS.'config.xml',pretifyXML($this->xmlConfig));		
	}
	
	/**
	 * updates the sysmlink form the dev folder to the joomla installation
	 * @return 
	 * @param $root Object
	 * @param $oldDevFolder Object
	 * @param $newDevFolder Object
	 */
	public function updateSymLinks($root,$oldDevFolder,$newDevFolder)
	{
		$entries = array_merge(JFolder::files($root),JFolder::folders($root));
		foreach($entries as $entry) 
		{
			$path = $root.DS.$entry;
			if ( is_link($path) ) {
				$targetLink = readlink($path);
				$count = 0;
				$newTarget = str_replace($oldDevFolder,$newDevFolder,$targetLink,$count);
				if ( $count == 1 )
				{					
					unlink($path);
					symlink($newTarget,$path);
				}

			}				
			else if ( is_dir($path) ) {			
				$this->updateSymLinks($path,$oldDevFolder,$newDevFolder);				
			}
		}
		
	}
	
	/**
	 * 
	 * @return 
	 * @param $devFolder Object
	 */
	public function setDevFolder($devFolder)
	{

		if ( !preg_match('/\w/',$devFolder) ) {
			$this->setError('Invalid folder name');
			return false;
		}
		if ( JFolder::exists($devFolder) ) {
//			$this->setError('Folder already exists');
//			return false;
		} else {
		
			if ( $this->devFolderExists())
			{
				if ( !JFolder::exists($devFolder) )
					if ( !JFolder::create($devFolder) ) 
					{
						$this->setError('Unable to create folder. Make sure the parent folder has 777 permission');
						return false;	
						
					} 
				copyr($this->getDevFolder(),$devFolder);
				$this->updateSymlinks(JPATH_ROOT,$this->getDevFolder(),$devFolder);
				
			}else {
				
				JFolder::create($devFolder);
			}
		}
		JPath::setPermissions($devFolder,'0777','0777');
		$this->xmlConfig->devfolder = $devFolder;
		JFile::write(JPATH_COMPONENT.DS.'config.xml',pretifyXML($this->xmlConfig));
		return true;
	}
	
	/**
	 * 
	 * @return 
	 */
	public function getDevFolder()
	{
		if ( $this->devFolderExists() )
			return $this->xmlConfig->devfolder;
		else
			return $this->defaultDevFolder;
		
	}
	
	/**
	 * 
	 * @return 
	 */
	public function devFolderExists()
	{	
		$devFolder = $this->xmlConfig->devfolder;
		return preg_match('/\w/',$devFolder) && JFolder::exists($devFolder);

	}
	
}

?>