<?php
defined('_JEXEC') or die('Restricted access');
	JToolBarHelper::title( JText::_( 'Migrations' ) );	
	$bar = JToolBar::getInstance('toolbar');
	$url = urlFor(array('task'=>'addMigration'));
	$js  = sprintf("javascript:if (name = prompt('Enter the migration name')){ document.location = '%s&name='+name}",$url);
	$bar->appendButton('Link', 'new', 'Add Migration', $js);
	
	$migrations = $this->migration->getList();
	$currentVersion = $this->migration->version();
?>
	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="1%">
					<?php echo jtext('NUM'); ?>
				</th>
				<th width='20%'>
					<?php echo jtext('Name') ?>
				</th>
				<th width='10%'>
					<?php echo jtext('Migration') ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($migrations as $m) : ?>
				<tr>		
					<td><?php print $m->version ?></td>
					<td><?php print $m->name ?></td>
					<td>
						<a href='<?php print urlFor(array("controller"=>"migrations","ver"=>$m->version,"task"=>"migrate")) ?>'>
						<?php if ($currentVersion >= $m->version) : ?>
								migrate down
						<?php else : ?>

								migrate up 
						<?php endif ?>
						</a>									
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>