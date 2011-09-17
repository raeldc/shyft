<form action="<?=@route('id='.$page->id)?>" method="post" class="-koowa-form">
<input type="hidden" name="action" value="save">
<fieldset>
    <legend>Create Page</legend>

    <div class="clearfix">
        <label for="form-title">Title</label>
        <div class="input">
            <input class="xlarge" id="form-title" name="title" size="30" type="text" value="<?=$page->title?>">
        </div>
    </div>

    <div class="clearfix">
        <label for="form-url">URL</label>
        <div class="input">
            <input class="xlarge" id="form-url" name="url" size="30" type="text" value="<?=$page->url?>">
            <span class="help-block">
                Just input internal URLs for now. Start with index.php
            </span>
        </div>
    </div>
    <div class="actions">
        <input type="submit" class="btn primary" value="Save Changes">&nbsp;<button type="reset" class="btn">Cancel</button>
    </div>
</fieldset>
</form>