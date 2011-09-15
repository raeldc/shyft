<?php

class FlowDispatcherDefault extends KDispatcherAbstract 
{ 
    /**
	 * Forward after a post request
	 *
	 * Overridden because of Joomla Dependency
	 *
	 * @return mixed
	 */
	public function _actionForward(KCommandContext $context)
	{
		if (KRequest::type() == 'HTTP')
		{
			if($redirect = $this->getController()->getRedirect())
			{
			    $this->_redirect($redirect['url'], $redirect['message'], $redirect['type']);
			}
		}

		if(KRequest::type() == 'AJAX')
		{
			$view = KRequest::get('get.view', 'cmd');
			$context->result = $this->getController()->execute('display', $context);
			return $context->result;
		}
	}

	protected function _redirect( $url, $msg='', $msgType='message', $moved = false )
	{
		// check for relative internal links
		if (preg_match( '#^index[2]?.php#', $url )) {
			$url = KRequest::base() .'/'.$url;
		}

		// Strip out any line breaks
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		// If the headers have been sent, then we cannot send an additional location header
		// so we will output a javascript redirect statement.
		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		} else {
			header($moved ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
			if (empty($url)) {
				header('Location: '.KRequest::referrer());
			}else header('Location: '.$url);			
		}

		exit();
	}
}