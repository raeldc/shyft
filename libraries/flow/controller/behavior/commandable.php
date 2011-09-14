<?php
/**
 * @category	Flow
 * @package		Flow_Controller
 * @subpackage	Command
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Commandable Controller Behavior Class
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Flow
 * @package     Flow_Controller
 * @subpackage	Behavior
 */
class FlowControllerBehaviorCommandable extends KControllerBehaviorCommandable
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
                $config     = array('attribs' => array(
                    				//'href' => JRoute::_( 'index.php?option=com_'.$identifier->package.'&view='.$identifier->name)
                              ));
                
                $this->getToolbar()->addCommand('new', $config);
            }
            
            if($this->canDelete()) {
                $this->getToolbar()->addCommand('delete');
            }
        }
    }
}