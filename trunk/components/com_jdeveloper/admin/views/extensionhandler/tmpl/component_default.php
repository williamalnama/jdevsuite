<?php
defined('_JEXEC') or die('Restricted access');
	$bar = JToolBar::getInstance('toolbar');
	JToolBarHelper::title( JText::_( 'Components' ) );	

	$url = urlFor(array('task'=>'create','ext'=>$this->model->type));
	$js  = sprintf("javascript:if (name = prompt('Enter the %s name')){ document.location = '%s&name='+name}",$this->model->getHumanName(),$url);
	$bar->appendButton('Link', 'new', sprintf("Add New %s",$this->model->getHumanName()), $js);

	$extensions = $this->model->getList();

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
					<?php echo jtext('Option') ?>
				</th>				
				<th width='1%'>
					<?php echo jtext('Install') ?>
				</th>
				<th width='1%'>
					<?php echo jtext('Uninstall') ?>
				</th>				
			</tr>
		</thead>
		<tbody>			
			<?php foreach($extensions as $i=>$ext) : ?>
			<tr>
				<td align='center'><?php print $i + 1 ?></td>
				<td align='center'><?php print $ext->name ?></td>				
				<td align='center'><?php print $ext->option ?></td>
				<td align='center'>
					<a href="<?php print urlFor(array('task'=>'install','ext'=>$ext->type,'name'=>$ext->option)) ?>" ?>
						<?php if ( $ext->isInstalled() ) : ?>
							re-install
						<?else : ?>
							install
						<?endif;?>
					</a>					
				</td>
				<td align='center'>
					<a href="<?php print urlFor(array('task'=>'uninstall','ext'=>$ext->type,'name'=>$ext->option)) ?>" ?>
						<?php if ( $ext->isInstalled() ) : ?>
							uninstall
						<?else : ?>
							
						<?endif;?>
					</a>					
				</td>				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>