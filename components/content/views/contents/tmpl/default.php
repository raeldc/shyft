<form method="get" action="<?=@route()?>" class="-koowa-grid">
<table class="zebra-striped" id="sortTableExample">
	<thead>
		<tr>
			<th class="header">#</th>
			<th class="yellow header">Title</th>
			<th class="blue header">Body</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ($contents as $content): ?>
		<tr>
			<td><?=@helper('grid.checkbox',array('row' => $content))?></td>
			<td><a href="<?=@route('view=content&layout=form&id='.$content->id)?>"><?=$content->title;?></a></td>
			<td><?=$content->body?></td>
		</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?= @helper('paginator.pagination', array('total' => $total)) ?>
</form>