<?php

jimport('joomla.filesystem.archive');

class ModelPackage extends JModel
{
	
	/**
	 * configu
	 */
	private $config = null;
	
	/**
	 * 
	 */
	private $extHandler = null;	

	/**
	 * 
	 * @return 
	 * @param $args Object
	 */
	public function __construct($args)
	{
		parent::__construct(array());

		list($this->config,$this->extHandler) = $args;
		$this->path = $this->config->getProjectPath('packages');

	}
	
	protected function packageExt($type,$name)
	{
		
		
	}
	
	public function package($extensions)
	{
		if (!count($extensions))
			return;
		
		$mainExt   = array_shift($extensions);
		$path      = $this->config->getProjectPath($mainExt['type'].'s');

		$this->extHandler->setState('extension_type',$mainExt['type']);
		$extension = $this->extHandler->getExtension($mainExt['name']);
		$extension->updateManifest();
		$dest = $this->path.DS.$extension->packageName();
		$zipDest = $this->path.DS.$extension->packageName();
		$src  = $extension->path;
		JFolder::create($dest);
		copyr($src,$dest,'/^\./');
		
		if (  count($extensions)  ) {
	
			$manifest = $extension->manifest();
/*
			if ( isset($manifest->dependencies) ) {
				$node = dom_import_simplexml($manifest->dependencies);
				$node->parentNode->removeChild($node);					
			}
*/
//			$node = $manifest->addChild('dependencies');
//			$node->addAttribute('folder','dependencies');

			foreach ($extensions as $i=>$extension)
			{
				 $this->extHandler->setState('extension_type',$extension['type']);
				 $extension = $this->extHandler->getExtension($extension['name']);
				 $extension->updateManifest();
				 $extDest  = $dest.DS."dependencies".DS.$i.'_'.$extension->packageName();
				 JFolder::create($extDest);
				 copyr($extension->path,$extDest,'/^\./');
//				 $subnode = $node->addChild($extension['type']);
//				 $plg->addAttribute('group',$plugin->groupName);
//				 $plg->addAttribute('name',$plugin->plgName);
			}
			
			JFile::write($dest.DS.'manifest.xml',pretifyXML($manifest));	
		}
		
		$files = JFolder::files($dest,'.',true,true);
		JArchive::create($zipDest,$files,'tar','',$this->path,true,true);
		JFolder::delete($dest);
		
		return;
	}		
	/**
	 * 
	 * @return 
	 */
	public function _package()
	{
		$components =  JFolder::folders($this->config->getProjectPath('components'));
		$component  = null;
		
		if ( count($components) > 0 )
			$component = $components[0];


		$this->extHandler->setState('extension_type','component');
		$component = $this->extHandler->getExtension($component);
		$component->updateManifest();
		
		//create component folder
		$dest = $this->path.DS.$component->id;
		$zipDest = $this->path.DS.$component->id;
		$src  = $component->path;
		JFolder::create($dest);
		
		copyr($src,$dest,'/^\.svn/');
		
		$this->extHandler->setState('extension_type','plugin');
		$plugins = JFolder::folders($this->config->getProjectPath('plugins'));		
		
		if (  count($plugins) > 0 ) {
	
			$manifest = $component->manifest();
			if ( isset($manifest->plugins) ) {
				$pluginNode = dom_import_simplexml($manifest->plugins);
				$pluginNode->parentNode->removeChild($pluginNode);					
			}
			
			$pluginNode = $manifest->addChild('plugins');
			$pluginNode->addAttribute('folder','plugins');
			JFolder::create($dest.DS."plugins");
			foreach ($plugins as &$plugin)
			{
				 $plugin =& $this->extHandler->getExtension($plugin);
				 $plugin->updateManifest();
				 $pluginDest = $dest.DS."plugins".DS.$plugin->groupName.'_'.$plugin->plgName;				 
				 JFolder::create($pluginDest);
				 copyr($plugin->path,$pluginDest,'/^\.svn/');
				 $plg = $pluginNode->addChild('plugin');
				 
				 $plg->addAttribute('group',$plugin->groupName);				 
				 $plg->addAttribute('name',$plugin->plgName);
			}
			
			JFile::write($dest.DS.'manifest.xml',pretifyXML($manifest));	
		}

		
		$files = JFolder::files($dest,'.',true,true);

		JArchive::create($zipDest,$files,'tar','',$this->path,true,true);
		JFolder::delete($dest);	
		
	}
	public function getElements()
	{
		$extensionTypes   = array('components','modules','plugins','templates');
		$extensions  	  = array();
		foreach($extensionTypes as $extension) {
			$path  = $this->config->getProjectPath($extension);
			$folders = JFolder::folders($path);
			foreach($folders as $folder)
				$extensions[] = array('name'=>$folder,'type'=>$extension);
		}
		return $extensions;
	}
	
}