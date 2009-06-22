<?php
	defined('_JEXEC') or die('Restricted access');
	JToolBarHelper::title( JText::_( 'Configuration' ) );		

	$url = urlFor(array('task'=>'reset'));
	$js  = sprintf("javascript:if (confirm('Reseting environment will result restoration of the initial state of joomla including sample data. Are you sure ? ')){ document.location = '%s'}",$url);
	JToolBar::getInstance('toolbar')->appendButton('Link', 'cancel', 'Reset', $js);
		
?>
<?php if ( $this->configModel->devFolderExists() ) : ?>
<form method='POST' >
	<div class="col ">
	<fieldset>
			<legend><?php echo JText::_('Projects'); ?></legend>
			<table width="100%" class="paramlist admintable" cellspacing="1">				
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="dev_field"><?php echo JText::_('Create new Project'); ?></label>
					</td>
					<td class="paramlist_value">
							
							<input id="project-name" size="70" type="text" name="project_name" value="" />												
							<input type='submit' value='Create' />						
							<input type='hidden'  name='task'  value='newProject' />
							<input type='hidden'  name='controller' value='config'/>
							<input type='hidden'  name='option' value="com_jdeveloper" />						
					</td>
				</tr>
			</table>
	</fieldset>
	</div>
</form>
<?php endif ?>
<form method='POST' >	
	<div class="col ">
	<fieldset>
			<legend><?php echo JText::_('Dev Folder'); ?></legend>
			<table width="100%" class="paramlist admintable" cellspacing="1">				
				<?php if ( $this->configModel->devFolderExists() ) : ?>
				<tr>				
					<td width="40%" class="paramlist_key">
						<label for="dev_field"><?php echo JText::_('Current Folder'); ?></label>
					</td>
					<td class="paramlist_value">
						<?php print $this->configModel->getDevFolder() ?>
					</td>				
				</tr>
				<?php endif;?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="dev_field"><?php echo JText::_('New Folder'); ?></label>
					</td>
					<td class="paramlist_value">
						<?php if ( $this->configModel->devFolderExists() ) : ?>
							<input id="dev_field" size="70" type="text" name="dev_dir" value="" />						
							<input type='submit' value='Move folder' />
							<input type='hidden'  name='task'  value='changeDevFolder'/>
						<?php else : ?>
							<input id="dev_field" size="70" type="text" name="dev_dir" value="<?php print $this->configModel->getDevFolder() ?>" />												
							<input type='submit' value='Create' />						
							<input type='hidden'  name='task'  value='setDevFolder' />
						<?php endif; ?>
						
						<input type='hidden'  name='controller' value='config'/>
						<input type='hidden'  name='option' value="<?php print JRequest::getVar('option') ?>" />						
					</td>
				</tr>
			</table>
	</fieldset>
	</div>
</form>
	