<?php if ($total): ?>
<nav>
	<ul class="nav">
		<?php foreach ($pages as $page): ?>

		<?php if ($page->default): ?>
		<li><a href="<?=@route('index.php?mode=site&page=');?>"><?=$page->title;?></a></li>
		<?else:?>
		<li><a href="<?=@route('index.php?mode=site&page='.$page->id);?>"><?=$page->title;?></a></li>
		<?php endif ?>
		
		<?php endforeach ?>
	</ul>
</nav>
<?php endif ?>