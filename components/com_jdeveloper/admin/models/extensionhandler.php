<?php

//require dirname(__FILE__).DS.'installer.php';

require dirname(__FILE__).DS.'installer'.DS.'installer.php';



class ModelExtensionHandler extends JModel
{
	public function __construct($config)
	{
		parent::__construct(array());	
		$this->config = $config;
	}	
	
	/**
	 * gets a list of extension (component,plugins,modules) depending on the model state
	 * @return 
	 */
	public function getExtensions()
	{
		$class	= $this->getExtensionClass();
		$type	= strtolower(str_replace('JDeveloper','',$class));
		$path   = $this->config->getProjectPath(Inflector::pluralize($type));
		$items = call_user_func($class.'::getList',$path);
		
		$extensions = array();
		
		foreach($items as $item) {			
			
			$extension = new $class($item,$path);

			if ( $extension->showInList() )
				$extensions[] = $extension;
			
		}
		return $extensions;

	}
	
	/**
	 * gets an extension if not exists throws an exception
	 * @return 
	 */
	public function getExtension($name)
	{		
		$class	= $this->getExtensionClass();
		$type	= strtolower(str_replace('JDeveloper','',$class));
		$path   = $this->config->getProjectPath(Inflector::pluralize($type));		
		
		$extension = new $class($name,$path);

		return $extension;
	}	
	
	/**
	 * 
	 * @return 
	 */
	public function getExtensionClass()
	{
		$type = $this->getState('extension_type','component');
		$class  = 'JDeveloper'.ucfirst(strtolower($type));
		$path   = dirname(__FILE__).DS.'jelements'.DS.$type.'.php';
		
		if ( !class_exists($class) ) {				
			if ( !class_exists('AbstractJElement') )
				require dirname(__FILE__).DS.'jelements'.DS.'abstract.php';
			require $path;
			if ( !class_exists($class) )
				throw new Exception("$type is not a valid joomla element");
		}
		return $class;
	}

	public function getExtensionType()
	{
		$type = str_replace('JDeveloper','',$this->getExtensionClass());
		return strtolower($type);
	}
		
	/**	
	 * installs an extension
	 * @return 
	 * @param $name Object
	 */
	public function uninstall($extension)
	{			
		$extension->uninstall();
	
	}
			
	/**	
	 * installs an extension
	 * @return 
	 * @param $name Object
	 */
	public function install($extension)
	{			
		$extension->install();
	
	}

	/**
	 * 
	 * @return 
	 * @param $name Object
	 */
	public function create($name)
	{				
		$extension = $this->getExtension($name);
		$extension->create();
				
	}




}

?>