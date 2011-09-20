<section class="widget append left">
	<?=@template('com://site/widgets.view.widgets.manage')?>
</section>

<form action="<?=@route('id='.$widget->id)?>" method="post" class="-koowa-form">
<input type="hidden" name="action" value="save">
<fieldset>
    <legend>Create Widget</legend>

    <ul class="tabs" data-tabs="tabs">
        <li class="active"><a href="#widget-required">Essentials</a></li>
        <li><a href="#widget-configuration">Configuration</a></li>
        <li><a href="#widget-params">Parameters</a></li>
    </ul>

    
    <div class="tab-content">
        <!--start: #widget-required-->
        <div class="active" id="widget-required">
            <div class="clearfix">
                <label for="form-title">Title</label>
                <div class="input">
                    <input class="xlarge" id="form-title" name="title" size="30" type="text" value="<?=$widget->title?>">
                </div>
            </div>

            <div class="clearfix">
            	<label for="form-position">Widget Type</label>
                <div class="input">
                    <select class="medium" name="container">
                    	<option value="html">HTML Container</option>
                    	<option value="navigation">Navigation</option>
                    	<option value="content">Content List</option>
                    </select>
                    <span class="help-block">
                        In the /widgets directory, you'll find the list of Widget Types you can use.
                    </span>
                </div>
        	</div>

            <div class="clearfix">
                <label for="form-position">Theme Container</label>
                <div class="input">
                    <select class="medium" name="container">
                        <option value="left">Left</option>
                        <option value="featured">Featured</option>
                        <option value="navigation">Navigation</option>
                    </select>
                    <span class="help-block">
                        Select the theme container where you want this widget to show up
                    </span>
                </div>
            </div>


            <div class="clearfix">
                <label>Display Widget?</label>
                <div class="input">
                    <ul class="inputs-list">
                        <li>
                            <label>
                                <input type="radio" checked="" name="enabled" value="true">
                                <span>Yes</span>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" name="enabled" value="false">
                                <span>No</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
            
        </div><!--end: #widget-required-->

        <!--start: #widget-params-->
        <div id="widget-configuration">
            <p class="alert-message block-message success">Here you'll find the different options that the Widget Type: <strong>HTML Container</strong> needs.</p>
            <div class="clearfix">
                <label for="form-textarea">Body</label>
                <div class="input">
                    <textarea class="xlarge" id="form-textarea" name="body" rows="5"><?=$widget->body?></textarea>
                </div>
            </div>
        </div>

        <!--start: #widget-params-->
        <div id="widget-params">
            <div class="clearfix">
                <label id="optionsRadio">Show Title?</label>
                <div class="input">
                    <ul class="inputs-list">
                        <li>
                            <label>
                                <input type="radio" checked="" name="show_title" value="true">
                                <span>Yes</span>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" name="show_title" value="false">
                                <span>No</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="clearfix">
                <label for="form-title-url">URL of Title</label>
                <div class="input">
                    <input class="xlarge" id="form-title-url" name="title_url" size="30" type="text" value="<?=$widget->url?>">
                    <span class="help-block">
                        If you put a URL here, the title will be hyperlinked.
                    </span>
                </div>
            </div>
        </div><!--end: #widget-required-->
    </div><!--end: .pills-content-->

    <div class="actions">
        <input type="submit" class="btn primary" value="Save">&nbsp;<button type="reset" class="btn">Cancel</button>
    </div>
</fieldset>
</form>