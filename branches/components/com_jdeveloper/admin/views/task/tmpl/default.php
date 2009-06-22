<?php
	defined('_JEXEC') or die('Restricted access');
	$bar = JToolBar::getInstance('toolbar');
	
	$url = urlFor(array('task'=>'create'));
	$js  = sprintf("javascript:if (name = prompt('Enter the %s name')){ document.location = '%s&name='+name}",'Task',$url);
	$bar->appendButton('Link', 'new', sprintf("Create New %s",'Task'), $js);
		
	JToolBarHelper::title( JText::_( 'Tasks' ) );		
?>

	<?php if ( isset($this->output) ) : ?>
		
		<fieldset>
			<legend><?php echo JText::_('Output'); ?></legend>
			<?php print $this->output; ?>
		</fieldset>		
	<?php endif; ?>
	
	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="1%">
					<?php echo jtext('NUM'); ?>
				</th>
				<th width='10%'>
					<?php echo jtext('Tasks') ?>
				</th>				
				<th width='1%'>
					<?php echo jtext('Run') ?>
				</th>			
			</tr>
		</thead>
		<tbody>			
			<?php foreach($this->tasks as $i=>$task) : ?>
			<tr>
				<td align='center'><?php print $i + 1 ?></td>
				<td align='center'><?php print $task->title ?></td>
				<td align='center'>
					<a href="<?php print urlFor(array('task'=>'run','name'=>$task->name)) ?>" ?>
						run
					</a>					
				</td>

			<?php endforeach; ?>
		</tbody>
	</table>

