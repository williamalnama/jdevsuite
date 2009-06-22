<?php

abstract class AbstractJElement {
	
	/**
	 * 
	 * @return 
	 */
	public abstract static function getList($path);
	
	public static function type()
	{	
	
	}
	/**
	 * name
	 */
	public $name = null;
		
	/**
	 * path to this extension
	 */
	public $path = null;
	
	/**
	 * thea path to the folder before containing the $path
	 */
	public $root = null;
	
	/**
	 * type of the extension component, plugin, module
	 */
	public $type = null;
	

	final public function __construct($name,$root)
	{
		$this->name = strtolower(preg_replace('/ /','_',$name));
		$this->root = $root;
		$this->path = $root.DS.$this->name;

		$this->type = strtolower(str_replace('JDeveloper','',get_class($this)));
		$this->initialize();
	}
	
	public function packageName() {
		return $this->name;
	}
	
	public abstract function showInList();
	/**
	 * returns the path of the manifest.xml file. by default it's the root
	 * @return 
	 */
	public function manifestPath()
	{
		return $this->path.DS.'manifest.xml';
	}
	
	
	/**
	 * 
	 * @return 
	 * @param $name Object
	 */
	public function updateManifest()
	{	
		
		
		//if the languages folder exists add them to the manifest		
		if ( JFolder::exists($this->path.DS.'languages') )
		{	
			$manifest = $this->manifest();
			$nodes = array(array('root'=>$manifest,'path'=>$this->path.DS.'languages','folder'=>'languages'));
			if ( JFolder::exists($this->path.DS.'languages'.DS.'admin') ) {
				$nodes[] = array('root'=>$manifest->administration,'path'=>$this->path.DS.'languages'.DS.'admin','folder'=>'languages'.DS.'admin');
			}
			foreach($nodes as $node) {
				$root = $node['root'];
				$path = $node['path'];
				$folder = $node['folder'];
				
				//create language tag in xml
				if ( isset($root->languages) ) {
					$langsNode = dom_import_simplexml($root->languages);				
					$langsNode->parentNode->removeChild($langsNode);
				}
				$langsNode = $root->addChild('languages');
				$langsNode->addAttribute('folder',$folder);
				
				$files = JFolder::files($path);
							
				foreach($files as $file) {
					if (!preg_match('/^\./',$file))	{
						$langNode = $langsNode->addChild('language',$file);
						$tag 	  = substr($file,0,strpos($file,"."));
						$langNode->addAttribute('tag',$tag);
					}
				}
			}
						
		}
		

		//if the media folder exists add them to the manifest		
		if ( JFolder::exists($this->path.DS.'media') )
		{	$root = $this->manifest();
			//create media tag in xml
			if ( isset($root->media) ) {
				$mediaNode = dom_import_simplexml($root->media);				
				$mediaNode->parentNode->removeChild($mediaNode);
			}
			$mediaNode = $root->addChild('media');
			$mediaNode->addAttribute('folder','media');
			$mediaNode->addAttribute('destination',$this->id);
			
			$folders = JFolder::folders($this->path.DS.'media');
			foreach($folders as $folder) 
				if (!preg_match('/^\./',$folder))				
					$mediaNode->addChild('folder',$folder);
				
			$files = JFolder::files($this->path.DS.'media');
			foreach($files as $file) 
				if (!preg_match('/^\./',$file))	
					$mediaNode->addChild('file',$file);
			
						
		}	

	}
		
	/**
	 * returns the xml object of the manifest.xml it. The path of manifest 
	 * is returned by the manifestPath method. 
	 * @return 
	 */
	final public function manifest()
	{		
		if ( isset($this->xml) )
			return $this->xml;

		$manifestFile = $this->manifestPath();
			
		if ( !JFile::exists($manifestFile) ) 
			throw new Exception("Extension doesn't have manifest file {$this->path}");
		else
			return ($this->xml = new SimpleXMLElement(JFile::read($manifestFile)));
	}
	
	
	/**
	 * lets the child class to set the custom initial state of the object 
	 * @return 
	*/
	public  function initialize()
	{
	
	}
	
	/**
	 * creates an extension
	 * @return 
	 */
	abstract public function create();

	/**
	 * checks whether this extension has been installed yet or not
	 * @return 
	*/
	abstract  function isInstalled();
		
	public function getFriendlyName()
	{
		return Inflector::titlize($this->name);
	}

	/**
	 * install the extension
	 * @return 
	 */
	public function install()
	{
		if ($this->isInstalled())
			$this->uninstall();	

		$this->updateManifest();
		$installer = JInstaller::getInstance();
		$installer->install($this->path);
	}

	/**
	 * uninstall the extension 
	 * @return 
	 */
	public function uninstall()
	{
		$installer = JInstaller::getInstance();
		$id = $this->isInstalled();
		if ( !$id )
			return;
		
		$installer->setPath('source',$this->path);
		$installer->_findManifest();

		$installer->uninstall($this->type,$id);	
	}	
	
}