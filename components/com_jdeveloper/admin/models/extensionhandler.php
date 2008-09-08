<?php

require 'component.php';


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
		Component::create($name,$this->getFolder());			
	}
	public function install($option)
	{	
		
		$component = new Component($option,$this->getFolder());
		$component->install();
		
	}
	public function uninstall($option)
	{		
		$component = new Component($option,$this->getFolder());
		$component->uninstall();		
	}
	public function getList()
	{		
		$componentFolders = JFolder::folders($this->getFolder());		
		$components = array();
		foreach($componentFolders as $cFolder)
		{			
			try {
				$component = new Component($cFolder,$this->getFolder());
				$components[] = $component;
			}catch(Exception $e) {
				
			}
			
		}
		return $components;
	}

}

?>