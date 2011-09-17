<?php if ($total): ?>
<nav>
	<?php foreach ($pages as $page): ?>
	<a href="index.php?com=pages&id=<?=$page->id;?>&layout=form"><?=$page->title;?></a>
	<?php endforeach ?>
</nav>
<?php endif ?>