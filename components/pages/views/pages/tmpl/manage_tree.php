<?php if ($total): ?>
<ul>
	<?php foreach ($pages as $page): ?>
	<li><a href="<?=@route('view=page&layout=default&id='.$page->permalink)?>"><?=$page->title;?></a></li>
	<?php endforeach ?>
</ul>

<?php endif ?>