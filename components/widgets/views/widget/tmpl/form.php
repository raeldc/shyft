<section class="widget replace toolbar-top">
    <?= @helper('toolbar.render', array('toolbar' => $toolbar))?>
</section>

<form action="<?=@route('id='.$widget->id)?>" method="post" class="-koowa-form">
<fieldset>
    <legend>Create Widget</legend>

    <ul class="tabs" data-tabs="tabs">
        <li class="active"><a href="#widget-required">Essentials</a></li>
        <li><a href="#widget-configuration">Configuration</a></li>
        <li><a href="#widget-params">Parameters</a></li>
        <li><a href="#widget-condition">Display Conditions</a></li>
        <li><a href="#widget-about">About</a></li>
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

        <!--start: #widget-configuration-->
        <div id="widget-configuration">
            <p class="alert-message block-message success">Here you'll find the different options that the Widget Type: <strong>HTML Container</strong> needs.</p>
            <div class="clearfix">
                <label for="form-textarea">Body</label>
                <div class="input">
                    <textarea class="xlarge" id="form-textarea" name="body" rows="5"><?=$widget->body?></textarea>
                </div>
            </div>
         </div><!--end: #widget-configuration-->

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
        </div><!--end: #widget-params-->

        <!--start: #widget-condition-->
        <div id="widget-condition">
            <p class="alert-message block-message success">
                Show/Hide this widget if certain conditions are met. Conditions are based on the URL used to access a page.
            </p>

            <div class="clearfix">
                <div class="input">
                    <ul class="inputs-list">
                        <li>
                            <label>
                                <input type="radio" checked="checked" name="visibility" value="all">
                                <span>Display everywhere</span>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" name="visibility" value="display">
                                <span>Display if one of these conditions are met</span>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" name="visibility" value="hide">
                                <span>Don't display if one of these conditions are met</span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="clearfix">
                <h5>Conditions in URL Query Format</h5>
                <div>
                    <textarea class="xxlarge" id="form-textarea" name="conditions" rows="5" disabled><?=$widget->conditions?></textarea>
                    <span class="help-block">Put Conditions in URL Query format. Each line is a condition. You can use wildcard asterisk *<br>
                     Example: <strong>com=pages&view=page&id=my-first-page</strong></span>
                </div>
            </div>
         </div><!--end: #widget-condition-->

         <!--start: #widget-about-->
        <div id="widget-about">
            <div class="clearfix">
                <h3>Provided by Shyft, Inc</h3>
                <p>This widget simply displays a text which can be an HTML format. Display images, quotations or other static sidebar content using this widget</p>
            </div>
         </div><!--end: #widget-about-->

    </div><!--end: .pills-content-->
</fieldset>
</form>