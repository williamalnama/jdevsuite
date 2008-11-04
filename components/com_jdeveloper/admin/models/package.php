<?php

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

	
	public function __construct($args)
	{
		parent::__construct(array());

		list($this->config,$this->extHandler) = $args;
		$this->path = $this->config->getProjectPath('packages');

	}
	public function package()
	{
		$components =  JFolder::folders($this->config->getProjectPath('components'));
		$component  = null;
		
		if ( count($components) > 0 )
			$component = $components[0];


		$this->extHandler->setState('extension_type','component');
		$component = $this->extHandler->getExtension($component);
//		$component->updateManifest();
		
		//create component folder
		$dest = $this->path.DS.$component->id;
		$src  = $component->path;
		JFolder::create($dest);
		
//		copyr($src,$dest);
		
		$this->extHandler->setState('extension_type','plugin');
		$plugins = JFolder::folders($this->config->getProjectPath('plugins'));
		
		if ( count($plugins) > 0 ) {
			JFolder::create($dest.DS."plugins");
			foreach ($plugins as &$plugin)
			{			
				 $plugin =& $this->extHandler->getExtension($plugin);
	//			 $plugin->updateManifest();
				 $pluginDest = $dest.DS."plugins".DS.$plugin->groupName;				 
				 JFolder::create($pluginDest);
				 copyr($plugin->path,$pluginDest);

			}
		}
		

		
		die;	
	}
	
}