<?php
/**
 * @version     $Id$
 * @package     Koowa_Event
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Event Class
 *
 * You can call the method stopPropagation() to abort the execution of further listeners in your event listener.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Event
 */
class KEvent extends KConfig
{
 	/**
     * Priority levels
     */
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH    = 2;
    const PRIORITY_NORMAL  = 3;
    const PRIORITY_LOW     = 4;
    const PRIORITY_LOWEST  = 5;
 	
 	/**
     * The propagation state of the event
     * 
     * @var boolean 
     */
    protected $_propagate = true;
 	
 	/**
     * The event name
     *
     * @var array
     */
    protected $_name;
    
    /**
     * Target of the event
     *
     * @var KObjectServiceable
     */
    protected $_target;
    
    /**
     * Dispatcher of the event
     * 
     * @var KEventDispatcher 
     */
    protected $_dispatcher;
         
    /**
     * Get the event name
     * 
     * @return string	The event name
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Set the event name
     *
     * @param string	The event name
     * @return KEvent
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * Get the event target
     *
     * @return object	The event target
     */
    public function getTarget()
    {
        return $this->_target;
    }
    
    /**
     * Set the event target
     *
     * @param object	The event target
     * @return KEvent
     */
    public function setTarget(KObjectServiceable $target)
    {
        $this->_target = $target;
        return $this;
    }
    
    /**
     * Stores the EventDispatcher that dispatches this Event
     *
     * @param EventDispatcher $dispatcher
     * @return KEvent
     */
    public function setDispatcher(KEventDispatcherInterface $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
        return $this;
    }
    
    /**
     * Returns the EventDispatcher that dispatches this Event
     *
     * @return KEventDispatcher
     */
    public function getDispatcher()
    {
        return $this->_dispatcher;
    }
    
    /**
     * Returns whether further event listeners should be triggered.
     *
     * @return boolean 	TRUE if the event can propagate. Otherwise FALSE
     */
    public function canPropagate()
    {
        return $this->_propagate;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger calls
     * stopPropagation().
     * 
     * @return KEvent
     */
    public function stopPropagation()
    {
        $this->_propagate = false;
        return $this;
    }
}