<?php if ($total): ?>
<ul>
	<?php foreach ($pages as $page): ?>
	<li><a href="<?=@route('&mode=admin&page='.$page->permalink)?>"><?=$page->title;?></a></li>
	<?php endforeach ?>
</ul>

<li><a href="<?=@route('&id=something')?>">Test Link</a></li>

<?php endif ?>