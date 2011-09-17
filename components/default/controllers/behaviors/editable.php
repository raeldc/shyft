<?php
/**
 * @category	Flow
 * @package		Flow_Controller
 * @subpackage	Command
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.flowku.com
 */

/**
 * Editable Controller Behavior Class
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Flow
 * @package     Flow_Controller
 * @subpackage	Behavior
 */
class ComDefaultControllerBehaviorEditable extends KControllerBehaviorEditable
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
		        $view   	= KInflector::pluralize($identifier->name);
		        $url    	= 'index.php?com='.$component.'&view='.$view;
		    
		        $referrer = KFactory::get('koowa:http.url',array('url' => $url));
		    }
	        
			KRequest::set('cookie.referrer_'.md5(KRequest::url()), (string) $referrer);
		}
	}
}