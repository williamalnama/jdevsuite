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
		return true;
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