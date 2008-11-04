<?php
	defined('_JEXEC') or die('Restricted access');
	JToolBarHelper::title( JText::_( 'Configuration' ) );		

	$url = urlFor(array('task'=>'reset'));
	$js  = sprintf("javascript:if (confirm('Reseting environment will result restoration of the initial state of joomla including sample data. Are you sure ? ')){ document.location = '%s'}",$url);
	JToolBar::getInstance('toolbar')->appendButton('Link', 'cancel', 'Reset', $js);
		
?>

<form method='POST' >
	<div class="col ">
	<fieldset>
			<legend><?php echo JText::_('Projects'); ?></legend>
			<table width="100%" class="paramlist admintable" cellspacing="1">				
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="dev_field"><?php echo JText::_('Create new Package'); ?></label>
					</td>
					<td class="paramlist_value">
							
							<input id="project-name" size="70" type="text" name="project_name" value="" />												
							<input type='submit' value='Create' />						
							<input type='hidden'  name='task'  value='create' />
							<input type='hidden'  name='controller' value='package'/>
							<input type='hidden'  name='option' value="com_jdeveloper" />						
					</td>
				</tr>
			</table>
	</fieldset>
	</div>
</form>
