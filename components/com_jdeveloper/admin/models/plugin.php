<?php

jimport('joomla.installer.installer');

class Plugin
{
	public function __construct($folderName,$baseFolder)
	{		
		$folderName = strtolower($folderName);
		list($group,$name) = preg_split('/_/',$folderName,2);
		$this->name = $name;
		$this->group = $group;
		$this->wholeName = $folderName;
		$this->humanname = preg_replace('/_/',' ',$this->name);
		$this->xmlName   = ucfirst($this->group).' - '.ucfirst($this->humanname);
		print $this->xmlName.'<br>';
		$this->type  = 'plugin';
	}
	public function isInstalled()
	{
		
		return false;
	}
	public function install()
	{
		
		
	}
	public static function create($name,$baseFolder)
	{
		$name = strtolower($name);
		$name = preg_replace('/[^A-Za-z_]/','',$name);
		if ( !preg_match('/\w/',$name) )		
			return false;
		
		list($group,$name) = $array = preg_split('/_/',$name,2);

		if ( count($array) != 2 )
			return false;
			
		$folder = $baseFolder.DS.$group.'_'.$name;
		JFolder::create($folder);
		
		template('plugin.xml')->copy($folder.DS.$name.'.xml',array('name'=>$name,'group'=>$group,'creationDate'=>date('Y:m')));
		template('plugin')->copy($folder.DS.$name.'.php',array('name'=>$name,'group'=>$group));

		JPath::setPermissions($folder,'0777','0777');			
		
		return new Plugin($option,$baseFolder);		
	}
	
}