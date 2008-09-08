<?php 


class ModelConfig extends JModel
{
	var $defaultDevFolder = null;
	var $xmlConfig 		  = null;
	
	public function __construct() 	
	{
		parent::__construct(array());
		$this->xmlConfig =  simplexml_load_file(JPATH_COMPONENT.DS.'config.xml');		
		$this->defaultDevFolder = JPATH_COMPONENT.DS.'jdevfolder';
		
	}
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
		
	}
	public function setConfig($k,$v)
	{
		$this->xmlConfig->$k = $v;
		JFile::write(JPATH_COMPONENT.DS.'config.xml',pretifyXML($this->xmlConfig));				
	}
	public function getMigrationVersion()
	{
		return (int) $this->xmlConfig->migration;		
	}
	public function setMigrationVersion($ver)
	{
		$this->xmlConfig->migration = (int) $ver;
		JFile::write(JPATH_COMPONENT.DS.'config.xml',pretifyXML($this->xmlConfig));		
	}
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
	public function setDevFolder($devFolder)
	{

		if ( !preg_match('/\w/',$devFolder) ) {
			$this->setError('Invalid folder name');
			return false;
		}
		if ( JFolder::exists($devFolder) ) {
			$this->setError('Folder already exists');
			return false;
		}
		
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
		
		JPath::setPermissions($devFolder,'0777','0777');
		$this->xmlConfig->devfolder = $devFolder;
		JFile::write(JPATH_COMPONENT.DS.'config.xml',pretifyXML($this->xmlConfig));
		return true;
	}
	public function getDevFolder()
	{
		if ( $this->devFolderExists() )
			return $this->xmlConfig->devfolder;
		else
			return $this->defaultDevFolder;
		
	}
	public function devFolderExists()
	{	
		$devFolder = $this->xmlConfig->devfolder;
		return preg_match('/\w/',$devFolder) && JFolder::exists($devFolder);

	}
	
}

?>