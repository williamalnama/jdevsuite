<?php
	defined('_JEXEC') or die('Restricted access');
?>
<form >
	<div class="col ">
	<fieldset>
			<legend><?php echo JText::_('Dev Folder'); ?></legend>
			<table width="100%" class="paramlist admintable" cellspacing="1">				
				<?php if ( $this->config->devFolderExists() ) : ?>
				<tr>				
					<td width="40%" class="paramlist_key">
						<label for="dev_field"><?php echo JText::_('Current Folder'); ?></label>
					</td>
					<td class="paramlist_value">
						<?php print $this->config->getDevFolder() ?>
					</td>				
				</tr>
				<?php endif;?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="dev_field"><?php echo JText::_('New Folder'); ?></label>
					</td>
					<td class="paramlist_value">
						<?php if ( $this->config->devFolderExists() ) : ?>
							<input id="dev_field" size="70" type="text" name="dev_dir" value="" />						
							<input type='submit' value='Move folder' />
						<?php else : ?>
							<input id="dev_field" size="70" type="text" name="dev_dir" value="<?php print $this->config->getDevFolder() ?>" />												
							<input type='submit' value='Create' />						
						<?php endif; ?>
						<input type='hidden'  name='task'  value='setDevFolderPosted'/>						
						<input type='hidden'  name='controller' value='config'/>
						<input type='hidden'  name='option' value="<?php print JRequest::getVar('option') ?>" />						
					</td>
				</tr>
			</table>
	</fieldset>
	</div>
</form>
	