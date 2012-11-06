<section class="widget replace toolbar-top">
    <?= @helper('toolbar.render', array('toolbar' => $toolbar))?>
</section>

<form action="<?=@route('id='.$page->id)?>" method="post" class="-koowa-form">
<fieldset>
    <legend>Page</legend>

     <div class="clearfix">
        <label for="form-title">Title</label>
        <div class="input">
            <input class="xlarge" id="form-title" name="title" size="30" type="text" value="<?=$page->title?>">
        </div>
    </div>

    <!--start: #body-->
    <div id="body">
        <div class="clearfix">
            <label for="form-textarea">Body</label>
            <div class="input">
                <textarea class="xlarge" id="form-textarea" name="body" rows="5"><?=$page->body?></textarea>
            </div>
        </div>
     </div>
     <!--end: #body-->
</fieldset>
</form>