<?php
defined('_JEXEC') or die('Restricted access');
	$bar = JToolBar::getInstance('toolbar');
	JToolBarHelper::title( JText::_( 'Modules' ) );	

	$url = urlFor(array('task'=>'create','ext'=>'module'));
	$js  = sprintf("javascript:if (name = prompt('Enter the %s name')){ document.location = '%s&name='+name}",'Module',$url);
	$bar->appendButton('Link', 'new', sprintf("Create New %s",'Module'), $js);
	$extensions = $this->extensions;

?>

	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="1%">
					<?php echo jtext('NUM'); ?>
				</th>
				<th width='20%'>
					<?php echo jtext('Module Name') ?>
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
				<td align='center'><?php print $ext->getFriendlyName() ?></td>
				<td align='center'>
					<a href="<?php print urlFor(array('task'=>'install','ext'=>$ext->type,'name'=>$ext->name)) ?>" ?>
						<?php if ( $ext->isInstalled() ) : ?>
							re-install
						<?php else : ?>
							install
						<?php endif;?>
					</a>					
				</td>
				<td align='center'>
					<a href="<?php print urlFor(array('task'=>'uninstall','ext'=>$ext->type,'name'=>$ext->name)) ?>" ?>
						<?php if ( $ext->isInstalled() ) : ?>
							uninstall
						<?php else : ?>
							
						<?php endif;?>
					</a>					
				</td>				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>