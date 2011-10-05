<?php if ($total): ?>
<ul>
	<?php foreach ($pages as $page): ?>
	<li><a href="<?=@route('layout=default&id='.$page->id)?>"><?=$page->title;?></a></li>
	<?php endforeach ?>
</ul>

<?php endif ?>