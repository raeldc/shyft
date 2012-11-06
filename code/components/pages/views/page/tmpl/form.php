<section class="widget replace toolbar-top">
    <?= @helper('toolbar.render', array('toolbar' => $toolbar))?>
</section>

<form action="<?=@route('id='.$page->id)?>" method="post" class="-koowa-form">
<fieldset>
    <legend>Create Page</legend>

    <div class="clearfix">
        <label for="form-title">Title</label>
        <div class="input">
            <input class="xlarge" id="form-title" name="title" size="30" type="text" value="<?=$page->title?>">
        </div>
    </div>

    <div class="clearfix">
        <label for="form-slug">Slug</label>
        <div class="input input-prepend">
            <span class="add-on"><?=KRequest::base()?>/</span>
            <input id="form-slug" name="slug" size="5" type="text" value="<?=$page->id?>">
        </div>
    </div>

    <div class="clearfix">
        <label>Show in Navigation</label>
        <ul class="input inputs-list">
            <li>
                <label>
                    <input type="radio" checked="" name="enabled" value="1">
                    <span>Yes</span>
                </label>
            </li>
            <li>
                <label>
                    <input type="radio" name="enabled" value="0">
                    <span>No</span>
                </label>
            </li>
        </ul>
    </div>

    <fieldset>
        <ul class="tabs" data-tabs="tabs">
            <li class="active"><a href="#page-type">Type</a></li>
        </ul>

        <div class="tab-content">
            <!--start: #page-content-->
            <div class="active" id="page-type">
                <div class="clearfix">
                    <label for="form-position">Content Type</label>
                    <div class="input">
                        <?=@service('com://site/content.template.helper.listbox')->types(array(
                            'selected' => $page->type->id,
                            'filter' => array(
                                'type' => 'content'
                            )
                        ))?>
                    </div>
                </div>
             </div><!--end: #page-content-->
        </div>
    </fieldset>
</fieldset>
</form>