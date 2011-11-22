<?php

class ComThemesTemplateHelperListbox extends KTemplateHelperListbox
{
	public function containers($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'name'		  => 'container',
			'attribs'	  => array(),
			'model'		  => 'themes',
		    'prompt'      => '- Select -', 
		    'unique'	  => true
		))->append(array(
			'value'		 => 'name',
			'selected'   => $config->{$config->name},
		    'identifier' => 'com://'.$this->getIdentifier()->application.'/'.$this->getIdentifier()->package.'.model.'.KInflector::pluralize($config->model)
		))->append(array(
			'text'		=> 'title',
			'column'    => $config->value,
			'deselect'  => false,
		))->append(array(
		    'filter' 	=> array('sort' => $config->text),
		));

		$themes = $this->getService($config->identifier)->set($config->filter)->getList();

		//Compose the options array
        $options   = array();

 		if($config->deselect) {
         	$options[] = $this->option(array('text' => $config->prompt));
        }
		
 		foreach($themes as $theme) 
 		{
 			foreach ($theme->layouts as $layout) 
 			{
 				foreach ($layout->containers as $item) 
 				{
 		    		$options[] =  $this->option(array('text' => $item->{$config->text}.' ('.$theme->name.'/'.$layout->name.')', 'value' => $item->{$config->value}));		
 				}
 			}
		}

		//Add the options to the config object
		$config->options = $options;
		return $this->optionlist($config);
	}
}