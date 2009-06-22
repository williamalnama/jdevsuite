<?php

class JDeveloperComponent extends AbstractJElement
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
		
		$db = JFactory::getDBO();
		$db->execute("SHOW TABLES");
		$tables = $db->loadResultArray();
		$targetName = $db->getPrefix().$this->name.'_';
		$componentTables = array();
		foreach($tables as $table) {
			 if (strpos($table,$targetName) === 0) {
			 	$componentTables[] = $table;	
			 }
		}
		
		if (count($componentTables)) {
			$tables = array();
			foreach($componentTables as $table) {
				$q = 'SHOW CREATE TABLE `' . $table.'`';
				$db->execute($q);
				$row = $db->loadRow();
				$createStm = $row[1];
				$createStm = preg_replace('/[^)]+$/','',$createStm).' ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`;';
				$tables[]  = $createStm;
			};
			$schema = implode("\n\n\n",$tables);
			$schema = preg_replace('/'.$db->getPrefix().'/','#__',$schema);
			JFile::write($this->path.DS.'admin'.DS.'install'.DS.'install.mysql.utf8.sql',$schema);
			
			$reverse = array();
			foreach($componentTables as $table)
				$reverse[] = "DROP TABLE ".$table.";";

			$reverse = implode("\n\n",$reverse);
			
			JFile::write($this->path.DS.'admin'.DS.'install'.DS.'uninstall.mysql.utf8.sql',$reverse);
		}
		
		if (JFolder::exists($this->path.DS.'admin'.DS.'install') )
		{
			$node = $root->addChild('install')->addChild('sql')->addChild('file','install/install.mysql.utf8.sql');			
				
			$node->addAttribute('charset','utf8');
			$node->addAttribute('driver','mysql');
						
			$node = $root->addChild('uninstall')->addChild('sql')->addChild('file','install/uninstall.mysql.utf8.sql');				
			
			$node->addAttribute('charset','utf8');
			$node->addAttribute('driver','mysql');
			
			$root->addChild('installfile','install/install.php');
			$root->addChild('uninstallfile','install/uninstall.php');
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

			foreach($files as $f) {
				if (!preg_match('/^\./',$f))
					$filesNode->addChild('file',$f);
			}
			foreach($folders as $f) {		
				if (!preg_match('/^\./',$f))
					$filesNode->addChild('folder',$f);
			}	
		}
		JFile::write( $this->manifestPath(),pretifyXML($root));
	}	
	public function initialize()
	{
		$this->id = 'com_'.$this->name;		
	}	
	
	public function packageName()
	{
		return $this->id;	
	}
	


}