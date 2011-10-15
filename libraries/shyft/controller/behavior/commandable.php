<?php
/**
 * @category	Shyft
 * @package		Shyft_Controller
 * @subpackage	Command
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Commandable Controller Behavior Class
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Shyft
 * @package     Shyft_Controller
 * @subpackage	Behavior
 */
class SControllerBehaviorCommandable extends KControllerBehaviorCommandable
{
    /**
	 * Add default toolbar commands
	 * .
	 * @param	KCommandContext	A command context object
	 */
    protected function _afterBrowse(KCommandContext $context)
    {    
        if($this->_toolbar)
        {
            if($this->canAdd()) 
            {
                $identifier = $context->caller->getIdentifier();
                $config     = array(
                                'attribs' => array(
                    				'href'  =>  $context->caller->getView()->createRoute('layout=form&view='.$identifier->name)
                                ));

                $this->getToolbar()->addCommand('new', $config);
            }
            
            if($this->canDelete()) {
                $this->getToolbar()->addCommand('delete');
            }
        }
    }
}