<?php

class Component extends AbstractJElement
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
		$db = JFactory::getDBO();
		$q  = sprintf("SELECT * FROM jos_components WHERE parent = 0 AND `option` LIKE 'com_%s' LIMIT 1",str_replace('_','',$this->name));
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
		JFolder::create($this->path.DS.'admin');
		JFolder::create($this->path.DS.'site');
		template('manifest')->copy($this->path.DS.'manifest.xml',array('name'=>$this->getFriendlyName(),'creationDate'=>date('Y:m')));
	}
	
	/**
	 * 
	 * @return 
	 */
	public function updateManifest()
	{
		
		//updates language/media 
		parent::updateManifest();
		
		$root = $this->manifest();
		
		$dom  = dom_import_simplexml($root);
		
		
		foreach(array('install','uninstall','installfile','uninstallfile') as $tag) {
			if ( isset($root->$tag) ) {
				$node = dom_import_simplexml($root->$tag);
				$node->parentNode->removeChild($node);
			}
					
		}
		
		if (JFolder::exists($this->path.DS.'install') )
		{
			$node = $root->addChild('install')->addChild('sql')->addChild('file','install/install.mysql.utf8.sql');			
			
			$node->addAttribute('charset','utf8');
			$node->addAttribute('driver','mysql');
						
			$node = $root->addChild('uninstall')->addChild('sql')->addChild('file','install/uninstall.mysql.utf8.sql');				
			
			$node->addAttribute('charset','utf8');
			$node->addAttribute('driver','mysql');
			
			$root->addChild('installfile','install/install.php');
			$root->addChild('uninstallfile','install/uninstallfile.php');
		}
		
		
		$subFolders = array('admin','site');

		if ( !isset($root->name) )
			$root->addChild('name');
			
		
		$root->name = $this->getFriendlyName();
		
		if ( !$root->administration )
			$adminNode = $root->addChild('administration');
		else	
			$adminNode = $root->administration;	

		$siteNode  = $root;		
				
		foreach($subFolders as $sFolder) 
		{
			$files 	 = JFolder::files($this->path.DS.$sFolder);
			$folders = JFolder::folders($this->path.DS.$sFolder);
			$node = $sFolder == 'admin' ? $adminNode : $siteNode;

			if ($fileNode = $node->files) {
				$fileNode = dom_import_simplexml($fileNode);
				$fileNode->parentNode->removeChild($fileNode);
			}

			$filesNode = $node->addChild('files');			
			$filesNode->addAttribute('folder',$sFolder);
			
			foreach($files as $f) 			
				$filesNode->addChild('file',$f);
			
			foreach($folders as $f) 			
				$filesNode->addChild('folder',$f);
										
		}
		JFile::write( $this->manifestPath(),pretifyXML($root));
	}	
	public function initialize()
	{
		$this->id = 'com_'.$this->name;		
	}	
	


}