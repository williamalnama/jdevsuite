<?php
defined('_JEXEC') or die('Restricted access');
class ViewExtensionHandler extends ComponentView
{
	
	public function display()
	{	
		$this->defaultLayout();
		parent::display();
	}
	
	
}