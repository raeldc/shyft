<?php
/**
 * @category	Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.shyfted.com
 */

/**
 * Widget Template Filter
 * 
 * This filter allow to dynamically inject data into widget container.
 * Code derived from Nooku's Module template filter.
 * 
 * Filter will parse elements of the form <section class="widget top|bottom container">[content]</section> 
 * and prepend or append the content to the container. 
 *
 * It's using div so the template can still be compatible on different platforms. 
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 */
class ComDefaultTemplateFilterWidget extends KTemplateFilterAbstract implements KTemplateFilterWrite
{  
    /**
	 * Find any <section class="widget top|bottom container"></section> elements and inject them into the Theme container
	 *
	 * @param string Block of text to parse
	 * @return ComDefaultTemplateFilterWidget
	 */
    public function write(&$text)
    {
		$matches = array();
		
		if(preg_match_all('#<section class="widget ([^>^"]*)">(.*)</section>#siU', $text, $matches)) 
		{
		    foreach($matches[0] as $key => $match)
			{
			    //Remove placeholder
			    $text = str_replace($match, '', $text);
				
		        $properties = explode(' ', $matches[1][$key]);

		        if(count($properties) >= 2) 
		        {
		        	$position = (array_shift($properties) == 'prepend') ? 'prepend' : 'append';
		        	$container = array_shift($properties);
		        }
		        else
		        {
		        	$position = 'append';
		        	$container = array_shift($properties);
		        }

			    $this->getView()
			    	->getContainer()
			    	->append($container, $matches[2][$key], $position);
			}
		}

		return $this;
    }    
}
