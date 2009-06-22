<?php

class ModelTask extends JModel
{
	/**
	 * configu
	 */
	private $config = null;
	
	/**
	 * 
	 */
	private $path   = null;
	
	/**
	 * 
	 * @return 
	 * @param $config Object
	 */
	public function __construct($config)
	{
		parent::__construct(array());
		$this->config = $config;
		$this->path = $this->config->getProjectPath('tasks');
	}
	
	/**
	 * 
	 * @return 
	 */
	public function getList()
	{

		$files = JFolder::files($this->path);
		$items = array();
		
		foreach($files as $file) {
		
			$item = new stdClass();
			$item->name  = $file;
			$item->title = Inflector::humanize(preg_replace('/.php/','',$file));
			$items[] = $item;
		}
		return $items;
	}
	
	/**
	 * 
	 * @return 
	 * @param $name Object
	 */
	public function run($name)
	{
		ob_start();
		require $this->path.DS.$name;		
		$output = ob_get_contents();
		ob_flush();
		return $output;
	}
	
	
	/**
	 * 
	 * @return 
	 * @param $name Object[optional]
	 */
	public function create($name='')
	{
		if ( count($name) == 0 )
			return;
		
		$name = Inflector::underscore(preg_replace('/ /','_',strtolower($name))).'.php';
		if ( !JFile::exists($this->path.DS.$name) )
			JFile::write($this->path.DS.$name,'');
	}
	
}