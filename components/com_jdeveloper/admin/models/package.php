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
	
	/**
	 * 
	 * @return 
	 */
	public function package()
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
	
}