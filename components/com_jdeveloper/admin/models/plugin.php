<?php

jimport('joomla.installer.installer');

class Plugin
{
	public function __construct($id,$baseFolder)
	{		
		if ( ! ($vars = self::parseId($id)) )
			return false;
			
		foreach($vars as $k=>$v)
			$this->$k = $v;
			
		$this->type  = 'plugin';
	}
	public function isInstalled()
	{
		
		return false;
	}
	public function install()
	{
		
		
	}
	public static function parseId($id)
	{
		
		$id = strtolower($id);
		$id = preg_replace('/[^A-Za-z_]/','',$id);
		if ( !preg_match('/\w/',$id) )		
			return false;
		
		list($groupName,$sysName) = preg_split('/_/',$id,2);

		if ( !$groupName || !$sysName )
			return false;

		$humanName = str_titleCase($sysName);
		$className = str_classifiy($sysName);

		return array( 'id'=>$id,
			'groupName'=>$groupName,
			'sysName'=>$sysName,
			'humanName'=>$humanName,
			'className'=>$className			
		);
	}
	public static function create($id,$baseFolder)
	{
		if ( ! ($vars = self::parseId($id)) )
			return false;
			
		extract($vars,EXTR_OVERWRITE);		

		$groupFolder = $baseFolder.DS.$groupName;
		
		JFolder::create($groupFolder);
		
		template('plugin.xml')->copy($groupFolder.DS.$sysName.'.xml',
									array('humanName'=>$humanName,
										  'groupName'=>ucfirst($groupName),
										  'creationDate'=>date('Y:m')));
										  
		template('plugin')->copy($groupFolder.DS.$sysName.'.php',
							array('groupName'=>ucfirst($groupName),
								  'className'=>$className));

		JPath::setPermissions($groupFolder,'0777','0777');			

		return new Plugin($id,$baseFolder);		
	}
	
}