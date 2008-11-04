<?php 
defined('_JEXEC') or die('Restricted access');

require 'updater.php';
$updated = false;

if ( $latestVersion  = JRequest::getFloat('update') )
{
	$updater = new Updater();
	if ( $latestVersion == $updater->newVersionAvailable() ) 
	{		
		 $updater->updateToLatest();
		 global $mainframe;
         $mainframe->redirect('index.php?option=com_jdeveloper&controller=config','Component Updated','message');
		 $updated = true;
	}
	
}
if ( !$updated )
	require 'core'.DS.'dispatcher.php';
	