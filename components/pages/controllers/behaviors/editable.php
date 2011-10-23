<?php

class ComPagesControllerBehaviorEditable extends ComDefaultControllerBehaviorEditable
{
	/**
	 * Set the referrer
	 *
	 * @return void
	 */
	public function setReferrer()
	{								   
	    $identifier = $this->getMixer()->getIdentifier();
	    
	    if(!KRequest::has('cookie.referrer_'.md5(KRequest::referrer())))
	    {
	        $referrer = KRequest::referrer();
	        $request  = KRequest::url();
			
			//Compare request url and referrer
	        if(!isset($referrer) || ((string) $referrer == (string) $request))
		    {  
		        $component 	= $identifier->package;
		        $url    	= $this->getView()->createRoute('view=pages');
		    
		        $referrer = $this->getService('koowa:http.url',array('url' => $url));
		    }
	        
			KRequest::set('cookie.referrer_'.md5(KRequest::url()), (string) $referrer);
		}
	}
}