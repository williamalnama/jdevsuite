<?php

jimport('joomla.installer.installer');

class Component
{
	public $name = null;
	public $option = null;
	public $type = 'component';
	private $folder = null;
	
	public static function create($name,$baseFolder)
	{
		$name = preg_replace('/[^0-9A-Za-z ]/','',$name);
		if ( !preg_match('/\w/',$name) )		
			return false;
		$option = strtolower( 'com_'.preg_replace('/ /','',$name) );
		$folder = $baseFolder.DS.$option;
		
		JFolder::create($folder);
		JFolder::create($folder.DS.'admin');
		JFolder::create($folder.DS.'site');
		JPath::setPermissions($folder,'0777','0777');	
		template('manifest')->copy($folder.DS.'manifest.xml',array('name'=>$name,'creationDate'=>date('Y:m')));
		return new Component($option,$baseFolder);		
	}
	public function __construct($option,$base_folder)
	{						
		if ( ! preg_match('/com_\w+/',$option) )
			throw new Exception("Invalid Component Name");
		
		$this->option = $option;
		$this->folder = $base_folder.DS.$option;
		
		$folders = JFolder::folders($this->folder);

		if ( array('admin','site') != $folders )
			throw new Exception("Only admin, site folders allowed in the component folder");		
		
		$xml = $this->getManifestXML();
				
		if ( !$xml )
			throw new Exception("Component doesn't have manifest file");
	
		$this->name = pick($xml->name,$this->option);
		$option = strtolower( 'com_'.preg_replace('/ |[^0-9A-Za-z]/','',$this->name) );
		
		if ( $option != $this->option )
			throw new Exception('Make sure the component option name is in the right format');		
		

	}
	public function updateManifest()
	{
		$subFolders = array('admin','site');
		$root = $this->getManifestXML();
		
		if ( !$root->administration )
			$adminNode = $root->addChild('administration');
		else	
			$adminNode = $root->administration;	

		$siteNode  = $root;		
				
		foreach($subFolders as $sFolder) 
		{
			$files 	 = JFolder::files($this->folder.DS.$sFolder);
			$folders = JFolder::folders($this->folder.DS.$sFolder);
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
		JFile::write( $this->folder.DS.'manifest.xml',pretifyXML($root));
		
	}
	public function getManifestPath()
	{
		return 	$this->folder.DS.'manifest.xml';
	}
	public function getManifestXML()
	{		
		$manifestFile = $this->getManifestPath();
			
		if ( !JFile::exists($manifestFile) ) 
			return false;
		else
			return new SimpleXMLElement(JFile::read($manifestFile));
	}
	
	//might not need these anymore - using modified jinstaller	
	public function uninstall()
	{
		
		$installer = JInstaller::getInstance();
		
		$admin = JPATH_ADMINISTRATOR.DS.'components'.DS.$this->option;
		$site  = JPATH_SITE.DS.'components'.DS.$this->option;

		if ( is_link($admin) )
			JFile::delete($admin);
		else if (JFolder::exists($admin)) {
			JFolder::delete($admin);
		}

		if ( is_link($site) )
			JFile::delete($site);
		else if (JFolder::exists($site)) {
			JFolder::delete($site);
		}	
					
		$targetAdmin = $this->folder.DS.'admin';
		$targetSite  = $this->folder.DS.'site';

		if ( $id = $this->isInstalled() ) 
		{
			copyr($targetAdmin,$admin);
			copyr($targetSite,$site);
			copyr($this->getManifestPath(),$admin.DS.'manifest.xml');						
			$installer->uninstall($this->type,$id);			
		}
	}
	
	//might not need these anymore - using modified jinstaller
	public function install()
	{
		$this->updateManifest();
		$installer = JInstaller::getInstance();

		$this->uninstall();
		
		$admin = JPATH_ADMINISTRATOR.DS.'components'.DS.$this->option;
		$site  = JPATH_SITE.DS.'components'.DS.$this->option;
		$targetAdmin = $this->folder.DS.'admin';
		$targetSite  = $this->folder.DS.'site';
				
		if ( $installer->install($this->folder) )
		{									
			JFolder::delete($admin);
			JFolder::delete($site);
			symlink($targetAdmin,$admin);
			symlink($targetSite,$site);
	
		}
		
	}
	/*
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
				
	}	*/
	public function isInstalled()
	{
		$db = JFactory::getDBO();
		$q  = sprintf("SELECT * FROM jos_components WHERE parent = 0 AND `option` LIKE '%s' LIMIT 1",$this->option);
		$db->execute($q);
		return @$db->loadObject()->id;
	}
	
}