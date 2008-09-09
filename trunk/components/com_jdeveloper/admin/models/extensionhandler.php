<?php

require 'component.php';
require 'plugin.php';


class ModelExtensionHandler extends JModel
{
	public function __construct($args)
	{
		
		$this->type   = strtolower( pick(@$args['type'],'component') );

		$this->config = new ModelConfig();
		if (is_null($this->type) or !in_array($this->type,array('component','plugin','module')))
			throw new Exception('please enter a valid extension type');
			
		parent::__construct($args);	
	}
	public function getHumanName()
	{
		return ucfirst($this->type);		
	}
	public function getFolder()
	{
		$folder = $this->config->getDevFolder().DS.$this->type.'s';
		if ( !JFolder::exists($folder) ) {
			JFolder::create($folder);
			JPath::setPermissions($folder,'0777','0777');
		}
		return $folder;
	}
	public function create($name)
	{				
		if ($this->type == 'component')
			Component::create($name,$this->getFolder());			
		else if ($this->type == 'plugin')
			Plugin::create($name,$this->getFolder());
		
	}
	public function install($name)
	{			
		$extension = $this->getExtInstance($name);
		$extension->install();				
	}
	public function uninstall($name)
	{		
		$component = $this->getExtInstance($name);
		$component->uninstall();		
	}
	public function getComponentList()
	{
		$componentFolders = JFolder::folders($this->getFolder());
		$components = array();
		foreach($componentFolders as $cFolder)
		{			
			try {
				$component = $this->getExtInstance($cFolder);
				$components[] = $component;
			}catch(Exception $e) {
				
			}
			
		}
		return $components;				
	}
	public function getPluginList()
	{
		$groups  = JFolder::folders($this->getFolder());
		$plugins = array();
		foreach($groups as $group)
		{
			$files = JFolder::files($this->getFolder().DS.$group);
			foreach($files as $f) {
				if ( JFile::getExt($f) != 'xml') continue;
				$f = preg_replace('/\..*/','',$f);
				$id = strtolower($group).'_'.strtolower($f);
				$plugins[]= $this->getExtInstance($id);
			}
		}
	
		return $plugins;
		
	}
	public function getList()
	{		
		$method = 'get'.$this->type.'list';
		return $this->$method();
	}
	private function getExtInstance($folderName)
	{
		$classname = ucfirst($this->type);
		return new $classname($folderName,$this->getFolder());		
	}

}

?>