<?php

class JDeveloperModule extends AbstractJElement
{
	
	/**
	 * 
	 * @return 
	 * @param $path Object
	 */
	public static function getList($path)
	{
		return JFolder::folders($path);
	}
		
	/**
	 * 
	 * @return 
	 */
	public function initialize()
	{
		$this->modName = 'mod_'.$this->name;		
	}
	
	/**
	 * 
	 * @return 
	 */
	public function showInList()
	{
		return true;	
	}

	/**
	 * 
	 * @return 
	 */
	public function manifestPath()
	{		
		return $this->path.DS.$this->modName.'.xml';
	}
	
	/**
	 * 
	 * @return 
	 * @param $name Object
	 */
	public function getFriendlyName()
	{
		return ucwords(preg_replace('/_/',' ',$this->name));
	}
	
	/**	
	 * 
	 * @return 
	 */
	public function isInstalled()
	{
		$attrs = $this->manifest()->attributes();
		if ( isset($attrs['client']) && $attrs['client'] == 'administrator' )
			$clientId = 1;
		else	
			$clientId = 0;
		$client = JApplicationHelper::getClientInfo($clientId);
		$path 	= $client->path.DS.'modules'.DS.$this->modName;

		if (file_exists($path)) 
			return $this->name;
		else	
			return false;
		/*	
		$db = JFactory::getDBO();
		$q  = sprintf("SELECT * FROM #__modules WHERE module LIKE '%s' LIMIT 1",$this->modName);
		$db->execute($q);
		return ($object = $db->loadObject()) ? $object->id : false;*/
	}
	
	/**
	 * 
	 * @return 
	 */
	public function create()
	{
		JFolder::create($this->path);


		template('module.xml')->copy($this->manifestPath(),
									array('name'=>$this->getFriendlyName(),										  
										  'creationDate'=>date('Y:m')));
		template('module.php')->copy($this->path.DS.$this->modName.'.php');
	}	
	/**
	 * 
	 * @return 
	 */
	public function updateManifest()
	{
		parent::updateManifest();
		
		$files   = JFolder::files($this->path);
		$folders = JFolder::folders($this->path);

		$root = $this->manifest();
		if ( $filesNode = $root->files ) {
			$filesNode = dom_import_simplexml($filesNode);
			$filesNode->parentNode->removeChild($filesNode);			
		}

		$filesNode = $root->addChild('files');
		
		$this->moduleFiles   = array();
		$this->moduleFolders = array();

		foreach($files as $f) 
		{
			//skip the manifest.xml file
			if ( $f == $this->modName.'.xml')
				continue;
			
			$fileNodde =  $filesNode->addChild('filename',$f);
			if ($f == $this->modName.'.php')
				$fileNodde->addAttribute('module',$this->modName);
			$this->moduleFiles[] = $f;
		}
		foreach($folders as $f) 
		{
			$folderNode =  $filesNode->addChild('folder',$f);
			$this->moduleFolders[] = $f;			
		}
		JFile::write( $this->manifestPath(),pretifyXML($root));
	}		
}