<section class="widget replace toolbar-top">
    <?= @helper('toolbar.render', array('toolbar' => $toolbar))?>
</section>

<form action="<?=@route('id='.$widget->id)?>" method="post" class="-koowa-form">
<fieldset>
    <legend>Create Widget</legend>

    <ul class="tabs" data-tabs="tabs">
        <li class="active"><a href="#widget-required">Information</a></li>
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
                    <?=@helper('com://site/content.template.helper.listbox.types', array(
                            'selected' => $widget->type->id,
                            'filter' => array(
                                'type' => 'widget'
                            )
                        ))?>
                </div>
        	</div>

            <div class="clearfix">
                <label for="form-position">Theme Container</label>
                <div class="input">
                    <?=@helper('com://site/themes.template.helper.listbox.containers', array(
                        'selected' => $widget->container
                    ))?>
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
            <p class="alert-message block-message success">Here you'll find the different options that the Widget Type: <strong><?=$widget->type->title?></strong> needs.</p>
            <?php
            // @TODO: Might be a Nooku bug, but we can't do @template('com://site/'.$variable);
            $config = 'com://site/'.$widget->type->component.'.view.config.'.$widget->type->config;
            echo @template($config);
            ?>
         </div><!--end: #widget-params-->

        <!--start: #widget-condition-->
        <div id="widget-condition">
            <p class="alert-message block-message success">
                Show/Hide this widget if certain conditions are met. Conditions are based on the URL used to access a page.
            </p>

            <div class="clearfix">

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