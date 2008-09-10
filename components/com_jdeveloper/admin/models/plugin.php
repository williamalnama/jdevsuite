<?php

//jimport('joomla.installer.installer');


class Plugin
{
	public function __construct($id,$baseFolder)
	{		
		if ( ! ($vars = self::parseId($id)) )
			return false;
			
		foreach($vars as $k=>$v)
			$this->$k = $v;
			
		$this->type   = 'plugin';
		$this->folder = $baseFolder.DS.$this->id;	
		$this->pluginName;
		$this->getManifest();		
		
	}
	
	public function getManifest()
	{
		$xmlFile = $this->folder.DS.$this->pluginName.'.xml';
		return simplexml_load_file($xmlFile);
	}

	public function updateManifest()
	{
		$files   = JFolder::files($this->folder);
		$folders = JFolder::folders($this->folder);
		
		$root = $this->getManifest();
		if ( $filesNode = $root->files )
		{
			$filesNode = dom_import_simplexml($filesNode);
			$filesNode->parentNode->removeChild($filesNode);			
		}
		$filesNode = $root->addChild('files');
		
		$this->pluginFiles   = array();
		$this->pluginFolders = array();
		
		foreach($files as $f) 
		{
			if ( $f == $this->pluginName.'.xml')
				continue;
			
			$fileNodde =  $filesNode->addChild('filename',$f);
			if ($f == $this->pluginName.'.php')
				$fileNodde->addAttribute('plugin',$this->pluginName);
			$this->pluginFiles[] = $f;
		}
		foreach($folders as $f) 
		{
			$folderNode =  $filesNode->addChild('folder',$f);
			$this->pluginFiles[] = $f;			
		}
		JFile::write( $this->folder.DS.$this->pluginName.'.xml',pretifyXML($root));
	}
	public function isInstalled()
	{
		$db = JFactory::getDBO();
		$q  = sprintf("SELECT * FROM #__plugins WHERE element LIKE '%s' AND folder LIKE '%s' LIMIT 1",$this->pluginName,$this->groupName);
		$db->execute($q);
		return @$db->loadObject()->id;
	}	
	public function uninstall()
	{
		$installer = JInstaller::getInstance();
		$id = $this->isInstalled();

		$installer->uninstall($this->type,$id);
		
	}
	public function install()
	{
		if ($this->isInstalled())
			$this->uninstall();	
		
		$this->updateManifest();
		
		$installer = JInstaller::getInstance();
		$installer->install($this->folder);
		
		
		
				
	}
	public static function parseId($id)
	{
		
		$id = strtolower($id);
		$id = preg_replace('/[^A-Za-z_]/','',$id);
		if ( !preg_match('/\w/',$id) )		
			return false;
		
		list($groupName,$pluginName) = preg_split('/_/',$id,2);

		if ( !$groupName || !$pluginName )
			return false;

		$humanName = str_titleCase($pluginName);
		$className = str_classifiy($pluginName);

		return array( 'id'=>$id,
			'groupName'=>$groupName,
			'pluginName'=>$pluginName,
			'humanName'=>$humanName,
			'className'=>$className			
		);
	}

	public static function create($id,$baseFolder)
	{
		if ( ! ($vars = self::parseId($id)) )
			return false;
			
		extract($vars,EXTR_OVERWRITE);		

		$pluginFolder = $baseFolder.DS.$id;
		
		JFolder::create($pluginFolder);
		
		template('plugin.xml')->copy($pluginFolder.DS.$pluginName.'.xml',
									array('humanName'=>$humanName,
										  'groupName'=>ucfirst($groupName),										  
										  'pluginName'=>$pluginName,
										  'creationDate'=>date('Y:m')));
										  
		template('plugin')->copy($pluginFolder.DS.$pluginName.'.php',
							array('groupName'=>ucfirst($groupName),
								  'className'=>$className));

		JPath::setPermissions($pluginFolder,'0777','0777');			

		return new Plugin($id,$baseFolder);		
	}
	
}