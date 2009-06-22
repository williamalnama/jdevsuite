<?php 
defined('_JEXEC') or die('Restricted access');
class Template
{
	function Template($uri)
	{		
		$file = JPATH_COMPONENT.DS.'templates'.DS.$uri;
		if ( !JFile::exists($file) )
			$file .= '.php';

		if ( !JFile::exists($file) )
			throw new Exception("File {$uri} doesn't exists");
			
		$this->templateFile = $file;		
	}
	function copy($desc,$assignments=array())
	{

		ob_start();
		extract($assignments);
		require $this->templateFile;
		$content = ob_get_clean();
		$ext 	 = JFile::getExt($desc);
		if ($ext == 'php')
			$content = "<?php defined('_JEXEC') or die('Restricted access');\r\n\r\n".$content;
		else if ($ext == 'xml')
			$content = '<?xml version="1.0" encoding="utf-8"?>'.$content;
			
		JFile::write($desc,$content);
		
		
	}
	
}
function template($url)
{
	return new Template($url);			
}