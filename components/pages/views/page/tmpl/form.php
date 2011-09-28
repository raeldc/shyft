<section class="widget append left">
    <?=@template('com://site/pages.view.pages.manage')?>
</section>

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
        <label for="form-slug">SEF URL</label>
        <div class="input input-prepend">
            <span class="add-on"><?=KRequest::base()?>/</span>
            <input id="form-slug" name="slug" size="5" type="text" value="<?=$page->slug?>">
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
            <li class="active"><a href="#page-content">Type</a></li>
            <li><a href="#page-component">Options</a></li>
            <li><a href="#page-layout">Layout</a></li>
            <li><a href="#page-widgets">Widgets</a></li>
            <li><a href="#page-display">User Access</a></li>
            <li class="dropdown" data-dropdown="dropdown">
                <a href="#" class="dropdown-toggle">More Options</a>
                <ul class="dropdown-menu">
                    <li><a href="#page-search">SEO</a></li>
                    <li><a href="#">Tags</a></li>
                    <li><a href="#">Redirections</a></li>
                    <li><a href="#page-revisions">Revision History</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Help</a></li>
                </ul>
            </li>
        </ul>

        <div class="tab-content">
            <!--start: #page-content-->
            <div class="active" id="page-content">
                <div class="clearfix">
                    <label for="form-position">Content Type</label>
                    <div class="input">
                        <select class="medium" name="type">
                            <option value="com://site/static">Static HTML Page</option>
                            <option value="com://site/pages">Pages</option>
                            <option value="com://site/widgets">Widgets</option>
                        </select>
                        <span class="help-block">
                            You can point this page to a Shyft Component.
                        </span>
                    </div>
                </div>
             </div><!--end: #page-content-->

             <!--start: #page-component-->
            <div id="page-component">
                <div class="clearfix">
                    <h5>Content</h5>
                    <div>
                        <textarea class="xxlarge" name="component[content]" rows="10"><?=$page->component['content']?></textarea>
                        <span class="help-block">Content of the Page</span>
                    </div>
                </div>
             </div><!--end: #page-component-->

             <!--start: #page-search-->
            <div id="page-search">
                <h4>Search Engine Optimization</h4>
                <div class="clearfix">
                    <label>SEO Options</label>

                    <ul class="input inputs-list">
                        <li>
                            <label>
                                <input type="radio" checked="" name="force_meta" value="0">
                                <span>Allow components to set values, these are just defaults</span>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" name="force_meta" value="1">
                                <span>Forcefully use these values, ignoring the component&rsquo;s values</span>
                            </label>
                        </li>
                    </ul>
                </div>

                <div class="clearfix">
                    <label for="form-page_title">Page Title</label>
                    <div class="input input-prepend">
                        <label class="add-on"><input type="checkbox" name="enabled[page_title]" value="1"></label>
                        <input class="large" id="form-page_title" name="page_title" size="16" type="text">
                        <span class="help-block">Browser window title</span>
                    </div>
                </div>

                <div class="clearfix">
                    <label for="form-page_title">Meta Description</label>
                    <div class="input input-prepend">
                        <label class="add-on"><input type="checkbox" name="enabled[meta_description]" value="1"></label>
                        <input class="large" id="form-meta_description" name="meta_description" size="16" type="text">
                    </div>
                </div>

                <div class="clearfix">
                    <label for="form-meta_keywords">Meta Keywords</label>
                    <div class="input input-prepend">
                        <label class="add-on">
                            <input type="checkbox" name="enabled[meta_keywords]" value="1">
                        </label>

                        <input class="xlarge" id="form-meta_keywords" name="meta_keywords" size="16" type="text">
                    </div>
                </div>

            </div><!--end: #page-search-->

             <!--start: #page-display-->
            <div id="page-display">
                Access    
            </div><!--end: #page-display-->

        </div>
    </fieldset>
    <div class="actions">
        <input type="submit" class="btn primary" value="Save Changes">&nbsp;<button type="reset" class="btn">Cancel</button>
    </div>
</fieldset>
</form>