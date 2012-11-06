<?php

class ComContentViewContentsHtml extends ComDefaultViewHtml
{
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'template_filters' => array('widget'),
		));

		parent::_initialize($config);
	}
}