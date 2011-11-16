<?php

class ComPageControllerPage extends ComDefaultControllerDefault
{
	protected function _initialize(KConfig $config)
	{
		// Set the default mode to site
		$config->request->append(array(
			'mode' => 'site'
		));

		// Force the view to page because there is no other
		$config->request->view = 'page';
		
		// If mode is admin, for the layout to be form.
		if ($config->request->mode == 'admin') {
			$config->request->layout = 'form';
		}

		parent::_initialize($config);

		// If mode is site, we don't want these are the only behaviors that we want
		if ($config->request->mode == 'site') {
			$config->behaviors = array('executable', 'discoverable');
		}
	}

	public function getRedirect()
	{
		$result = array();

		$result = array(
			'url'     => $this->getView()->getRoute('view=page'),
			'message' => $this->_redirect_message,
			'type'    => $this->_redirect_type,
		);

		return $result;
	}
}