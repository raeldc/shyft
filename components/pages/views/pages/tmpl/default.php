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
		<?php foreach ($pages as $page): ?>
		<tr>
			<td><?=@helper('grid.checkbox',array('row' => $page))?></td>
			<td><a href="index.php?com=pages&id=<?=$page->id;?>&layout=form"><?=$page->title;?></a></td>
			<td><?=$page->url?></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?= @helper('paginator.pagination', array('total' => $total)) ?>
</form>