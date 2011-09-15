<?foreach ($contents as $content):?>
	<a href="index.php?com=content&id=<?=$content->_id;?>&layout=form"><?=$content->title;?></a><br />
<?endforeach;?>