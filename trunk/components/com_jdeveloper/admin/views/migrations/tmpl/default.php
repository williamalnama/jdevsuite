<?php
defined('_JEXEC') or die('Restricted access');
	JToolBarHelper::title( JText::_( 'Migrations' ) );	
	$bar = JToolBar::getInstance('toolbar');

	$peformedQueries = PerfomedQueries::get();
	if (count($peformedQueries) > 0)
		$bar->appendButton('Link','back', 'Toggle Prefix', "javascript:replacePrefix()");
	
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
								<img title='Migrate Down' src='images/tick.png' />
						<?php else : ?>
								<img title='Migrate Up' src='images/publish_x.png' />
								
						<?php endif ?>
						</a>									
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if ( count($peformedQueries) > 0 ) : ?>
			<tr>
				<td colspan=20>
					<div style='padding:20px;color:black;font-size:10pt;'>
						<?php foreach($peformedQueries as $q) : ?>
							<?php list($query,$error) = $q ?>							
							<div style='margin-top:5px;margin-bottom:5px'>
								<div class='query'><?php print preg_replace('/\\t/','&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',preg_replace('/\\n/','<br>',$query)); ?>;</div>
								<?php if ($error) : ?>
									<div style='color:red'><?php print $error?></div>
								<?php endif ?>
							</div>
							
						<?php endforeach; ?>		
					</div>					
				</td>
			</tr>
		</tbody>
		<script>
			function replacePrefix() 
			{
				$$("div.query").each(function(e){
					if ( e.getText().contains('#_') )
						e.setText(e.getText().replace(/#_/,'jos'));
					else
						e.setText(e.getText().replace(/jos/,'#_'));
				})
			}
		</script>
		<?php PerfomedQueries::clear() ?>
		<?php endif; ?>
	</table>
