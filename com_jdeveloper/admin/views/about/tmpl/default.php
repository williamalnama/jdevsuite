<?php defined('_JEXEC') or die(); ?>

<?php

	JToolBarHelper::title( JText::_( 'About This Component' ), 'systeminfo.png' );

?>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'Project Information' ); ?></legend>		
		<table class="admintable" cellspacing="1">
			<tr>
				<td width="150" class="key"><?php echo JText::_('NAME'); ?></td>
				<td>Joomla Developer Suite</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo JText::_('Start Date'); ?></td>
				<td>Sep 2008</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo JText::_('COPYRIGHT'); ?></td>
				<td>&copy; <?php echo date('Y'); ?> - rmd Studio Inc. - PeerGlobe Technology Inc.</td>
			</tr>
			
			<tr>
				<td class="key">License: </td>
				<td><a target="_blank" href="http://www.gnu.org/copyleft/gpl.html"> GNU/GPL</a></td>
			</tr>
		</table>
	</fieldset>
	

	

<fieldset class="adminform">
	<legend><?php echo JText::_( 'Contributor' ); ?></legend>
	<table class="admintable" cellspacing="1">
			<tr>
				<td width="150" class="key"><?php echo JText::_('NAME'); ?></td>
				<td>Rastin Mehr</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo JText::_('Role'); ?></td>
				<td>Web Application Architect</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo JText::_('Company'); ?></td>
				<td>rmd Studio Inc. ( <a target="_blank" href="http://www.rmdstudio.com">www.rmdStudio.com</a> ) - Custom CMS & Social Media Solutions</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo JText::_('EMAIL'); ?></td>
				<td><a href="mailto:rastin@rmdstudio.com">rastin@rmdstudio.com</a></td>
			</tr>
		</table>
</fieldset>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'Contributor' ); ?></legend>
	<table class="admintable" cellspacing="1">
			<tr>
				<td width="150" class="key"><?php echo JText::_('NAME'); ?></td>
				<td>Arash Sanieyan</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo JText::_('Role'); ?></td>
				<td>Web Application Architect</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo JText::_('Company'); ?></td>
				<td>PeerGlobe Technology Inc. ( <a target="_blank" href="http://www.peerglobe.com">www.PeerGlobe.com</a> ) - Web Development and Integrated Mobile Solutions</td>
			</tr>
			
			<tr>
				<td class="key"><?php echo JText::_('EMAIL'); ?></td>
				<td><a href="mailto:ash@peerglobe.com">ash@peerglobe.com</a></td>
			</tr>
		</table>
</fieldset>