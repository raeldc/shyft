<?php if ($total): ?>
<ul>
	<?php foreach ($pages as $page): ?>
	<li><a href="<?=@route('view=page&id='.$page->id)?>"><?=$page->title;?></a></li>
	<?php endforeach ?>
</ul>

<?php endif ?>