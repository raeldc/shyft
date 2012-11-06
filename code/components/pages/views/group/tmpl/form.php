<form action="<?=@route('id='.$group->id)?>" method="post" class="-koowa-form">
<fieldset>
    <legend>Create Group</legend>

    <div class="clearfix">
        <label for="form-title">Group Name</label>
        <div class="input">
            <input class="xlarge" id="form-name" name="name" size="30" type="text" value="<?=$group->name?>">
        </div>
    </div>
</fieldset>
</form>