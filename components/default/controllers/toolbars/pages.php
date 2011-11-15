<?php
/**
 * @category	Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 * @copyright  	Copyright (C) 2007 - 2010 Israel Canasa. All rights reserved.
 * @license   	GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.shyfted.com
 */

/**
 * Pages Toolbar 
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 */
class ComDefaultControllerToolbarPages extends KControllerToolbarAbstract
{
	/**
	 * Insert the pages management toolbar into a container
	 * 
	 * This is temporary. Obsolete when widgets are loaded and configured dynamically.
	 * 
	 * @param	KEvent	A event object
	 */
    public function onAfterControllerGet(KEvent $event)
    {   
    	$this->getService('com://site/application.view.theme')
    		->getContainer()
    		->append('toolbar-left', $this->getService('com://site/pages.controller.page')
                ->view('pages')
                ->layout('tree')
                ->all(true)
                ->display()
			);
    }
}