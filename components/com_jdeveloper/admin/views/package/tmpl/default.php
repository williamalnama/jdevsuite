<?php
	defined('_JEXEC') or die('Restricted access');
	JToolBarHelper::title( JText::_( 'Packaging' ) );	
	$bar = JToolBar::getInstance('toolbar');
	$bar->appendButton('Link', 'new', 'Package','javascript:submit()');
?>
	<script>
		function submit() {
			var checked = false;
			var chk = $$('.checkbox');
			chk.each(function(input) {
				if (input.checked)
					checked = true;
			})
			if (!checked) {
				alert('select one package');
				return;				
			}
			$('package').submit();
		}			
	</script>
	<form id="package" method="post">
	
	<input type="hidden" name="option" value="com_jdeveloper">
	<input type="hidden" name="conroller" value="package">
	<input type="hidden" name="task" value="package">
	
	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="1%">
					<?php echo jtext('NUM'); ?>
				</th>
				<th width='10%'>
					<?php echo jtext('Name') ?>
				</th>				
				<th width='1%'>
					<?php echo jtext('Type') ?>
				</th>
			</tr>
		</thead>
		<tbody id="sortable">
			<?php foreach($this->extensions as $i=>$extension) : ?>
			<tr>
				<!--<td align='left'><?php print $i + 1 ?></td>-->
				<td align='left'>
					<input class="checkbox" type="checkbox" name="extension[]" value="<?php print $extension['type']?>_<?php print $extension['name']?>" />
				</td>
				<td align='left' class="handle"><?php print $extension['name'] ?></td>
				<td align='left'><?php print $extension['type'] ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	</form>
	<script>
		new Sortables($('sortable'),{handles:$$('.handle')});
	</script>
