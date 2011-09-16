<form action="<?=@route('id='.$content->id)?>" method="post" class="-koowa-form">
<input type="hidden" name="action" value="save">
<fieldset>
    <legend>Create Content</legend>

    <div class="clearfix">
        <label for="form-title">Title</label>
        <div class="input">
            <input class="xlarge" id="form-title" name="title" size="30" type="text" value="<?=$content->title?>">
        </div>
    </div>

    <div class="clearfix">
        <label for="form-phone">Phone</label>
        <div class="input">
            <input class="xlarge" id="form-phone" name="phone" size="30" type="text" value="<?=$content->phone?>">
        </div>
    </div>

    <div class="clearfix">
        <label for="form-textarea">Body</label>
        <div class="input">
            <textarea class="xxlarge" id="form-textarea" name="body" rows="5"><?=$content->body?></textarea>
            <span class="help-block">
            Block of help text to describe the field above if need be.
            </span>
        </div>
    </div><!-- /clearfix -->
    <div class="actions">
        <input type="submit" class="btn primary" value="Save Changes">&nbsp;<button type="reset" class="btn">Cancel</button>
    </div>
</fieldset>
</form>