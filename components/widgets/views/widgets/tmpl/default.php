<section class="widget append toolbar-left">
	<?=@template('manage')?>
</section>

<form method="get" action="<?=@route()?>" class="-koowa-grid">
<table class="zebra-striped" id="sortTableExample">
	<thead>
		<tr>
			<th class="header">#</th>
			<th class="yellow header">Title</th>
			<th class="blue header">URL</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($widgets as $widget): ?>
		<tr>
			<td><?=@helper('grid.checkbox',array('row' => $widget))?></td>
			<td><a href="<?=@route('mode=admin&view=widget&layout=form&id='.$widget->id)?>"><?=$widget->title;?></a></td>
			<td><?=$widget->url?></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?= @helper('paginator.pagination', array('total' => $total)) ?>
</form>