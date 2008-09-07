<?php 
defined('_JEXEC') or die('Restricted access');

class Updater
{
	private $updateURL = 'http://jdevsuite.googlecode.com/svn/trunk/packages/releases/';
	private $latestVersion = null;
	private $latestVersionFile = null;

	public function updateToLatest()
	{
		if ( !$this->latestVersion || !$this->latestVersionFile)
			return;
			
		$url = $this->latestVersionFile;
	
		// Download the package at the URL given
		$p_file = JInstallerHelper::downloadPackage($url);
	
		// Was the package downloaded?
		if (!$p_file) {
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('Invalid URL'));
			return false;
		}
		
		$config =& JFactory::getConfig();
		$tmp_dest 	= $config->getValue('config.tmp_path');
		$tmp_update_folder = $tmp_dest.DS.'com_jdeveloper';
		
		JArchive::extract( $tmp_dest.DS.$p_file, $tmp_update_folder);
			
		$tmp_update_folder .= DS.'admin';
		foreach(JFolder::folders($tmp_update_folder) as $folder)	
		{
			$tmpFolder = $tmp_update_folder.DS.$folder;
			$cFolder = JPATH_COMPONENT.DS.$folder;
			if (JFolder::exists($cFolder) )
				JFolder::delete($cFolder);
				
			JFolder::move($tmpFolder,$cFolder);
		}

		jimport('joomla.application.component.model');
		require_once JPATH_COMPONENT.DS.'core'.DS.'lib'.DS.'helpers.php';		
		require_once JPATH_COMPONENT.DS.'models'.DS.'config.php';
		
		$config  = new ModelConfig();
		$config->setConfig('version',$this->latestVersion);

	}
	public function getLatestVersion()
	{		
		return (float) $this->latestVersion;
	}
	public function newVersionAvailable()
	{
		jimport('joomla.installer.helper');
		$ch = curl_init();
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $this->updateURL);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		// grab URL and pass it to the browser
		ob_start();
		curl_exec($ch);
		$html = ob_get_clean();
		curl_close($ch);
		
		$li   = array();
		preg_match_all('/<li>.*?<\/li>/',$html,$li);
		$li  = array_shift($li);
		$versions = array();
		$latestVersion = $currentVersion = (float) simplexml_load_file(JPATH_COMPONENT.DS.'config.xml')->version;
		foreach($li as $i) {
			$file = strip_tags($i);
			if ( !preg_match('/com_jdeveloper_/',$file) ) continue;
			$version = (float) str_replace('com_jdeveloper_','',$file);
			$latestVersion = max($version,$latestVersion);	
			$versions[$version] = $this->updateURL.$file;
		}
		if ( $currentVersion < $latestVersion )
		{
			$this->latestVersion = $latestVersion;
			$this->latestVersionFile = $versions[$latestVersion];
			return $latestVersion;
		} else 
			return false;
		
	}		
	
	
}