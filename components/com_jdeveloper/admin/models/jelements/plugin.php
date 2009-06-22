<?php 

class JDeveloperPlugin extends AbstractJElement
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
	 */
	public $groupName = null;
	
	/**
	 * 
	 */
	public $plgName	  = null;
	
	/**
	 * 
	 * @return 
	 */
	public function initialize()
	{
		list($this->groupName,$this->plgName) = split('_',$this->name,2);		
		$this->id = 'plg_'.$this->plgName;
		
	}

	public function packageName()
	{
		return 'plg_'.$this->groupName.'_'.$this->plgName;
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
		return $this->path.DS.$this->plgName.'.xml';
	}
	
	/**
	 * 
	 * @return 
	 * @param $name Object
	 */
	public function getFriendlyName()
	{
		return ucwords(preg_replace('/_/',' ',preg_replace('/_/',' - ',$this->name,1)));
	}
	
	/**	
	 * 
	 * @return 
	 */
	public function isInstalled()
	{
		$db = JFactory::getDBO();
		$q  = sprintf("SELECT * FROM #__plugins WHERE element LIKE '%s' AND folder LIKE '%s' LIMIT 1",$this->plgName,$this->groupName);
		$db->execute($q);
		return ($object = $db->loadObject()) ? $object->id : false;
	}
	
	/**
	 * 
	 * @return 
	 */
	public function create()
	{
		JFolder::create($this->path);

		$plgClassName = Inflector::classify($this->name);
	
		template('plugin.xml')->copy($this->manifestPath(),
									array('name'=>$this->getFriendlyName(),
										  'groupName'=>$this->groupName,
										  'creationDate'=>date('Y:m')));
		
		template('plugin')->copy($this->path.DS.$this->plgName.'.php',
							array('className'=>$plgClassName));
								  		

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
		
		$this->pluginFiles   = array();
		$this->pluginFolders = array();

		foreach($files as $f) 
		{
			//skip the manifest.xml file
			if ( $f == $this->plgName.'.xml')
				continue;
			
			$fileNodde =  $filesNode->addChild('filename',$f);
			if ($f == $this->plgName.'.php')
				$fileNodde->addAttribute('plugin',$this->plgName);
			$this->pluginFiles[] = $f;
		}
		foreach($folders as $f) 
		{
			$folderNode =  $filesNode->addChild('folder',$f);
			$this->pluginFiles[] = $f;			
		}
		JFile::write( $this->manifestPath(),pretifyXML($root));
	}		
}