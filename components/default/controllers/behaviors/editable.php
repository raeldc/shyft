<?php
/**
 * @category	Shyft
 * @package		Shyft_Controller
 * @subpackage	Command
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.shyfted.com
 */

/**
 * Editable Controller Behavior Class
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Shyft
 * @package     Shyft_Controller
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
		        $url    	= $this->getView()->getRoute('view='.$view);
		    
		        $referrer = $this->getService('koowa:http.url',array('url' => $url));
		    }
	        
			KRequest::set('cookie.referrer_'.md5(KRequest::url()), (string) $referrer);
		}
	}

	protected function _actionApply(KCommandContext $context)
	{
		$action = $this->getModel()->getState()->isUnique() ? 'edit' : 'add';
		$data   = $context->caller->execute($action, $context);
	
		//Create the redirect
		$url = clone KRequest::url();
		$query = array();

		if($this->getModel()->getState()->isUnique())
		{
	        $states = $this->getModel()->getState()->getData(true);
		
		    foreach($states as $key => $value) {
		        $query[$key] = (string)$data->get($key);
		    }

		    $url = $this->getView()->getRoute(http_build_query($query));
		}
		else
		{ 
		    if ($data instanceof KDatabaseRowAbstract) 
		    { 
                $query[$data->getIdentityColumn()] = (string)$data->get($data->getIdentityColumn());
                $url = $this->getView()->getRoute(http_build_query($query));
            } 
            else $url = $this->getReferrer();
		}

		$this->setRedirect($url);
		
		return $data;
	}
}
