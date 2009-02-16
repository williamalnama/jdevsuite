<?php

class JDeveloperTemplate extends AbstractJElement
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
	public function showInList()
	{
		return true;
	}

	
	/**	
	 * if component installed returns its id o/w returns false
	 * @return 
	 */
	public function isInstalled()
	{
				
		$attrs = $this->manifest()->attributes();		
		$folderName = $this->manifest()->name;
		$folderName = JFilterInput::clean($folderName, 'cmd');		
		$folderName = strtolower(str_replace(" ", "_", $folderName));
		
		if ( isset($attrs['client']) && $attrs['client'] == 'administrator' )
			$clientId = 1;
		else	
			$clientId = 0;
		$client = JApplicationHelper::getClientInfo($clientId);
		$path 	= $client->path.DS.'templates'.DS.$folderName;

		if (file_exists($path)) 
			return $folderName;
		else	
			return false;

	}
	
	/**
	 * 
	 * @return 
	 */
	public function create()
	{

	}
	
	/**
	 * 
	 * @return 
	 */
	public function updateManifest()
	{
		//updates language/media 
		parent::updateManifest();
		$files   = JFolder::files($this->path);
		$folders = JFolder::folders($this->path);

		$root = $this->manifest();
		if ( $filesNode = $root->files ) {
			$filesNode = dom_import_simplexml($filesNode);
			$filesNode->parentNode->removeChild($filesNode);			
		}

		$filesNode = $root->addChild('files');
		
		foreach($files as $f) 
		{			
			$fileNodde =  $filesNode->addChild('filename',$f);

		}
		foreach($folders as $f) 
		{
			$folderNode =  $filesNode->addChild('folder',$f);
		}
		JFile::write( $this->manifestPath(),pretifyXML($root));
	}	
	public function initialize()
	{
//		$this->id = 'com_'.$this->name;		
	}	
	public function manifestPath()
	{
		return $this->path.DS.'templateDetails.xml';
	}	
	


}