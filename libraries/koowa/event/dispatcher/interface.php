<?php
/**
 * @version     $Id: dispatcher.php 4948 2012-09-03 23:05:48Z johanjanssens $
 * @package     Koowa_Event
 * @subpackage 	Dispatcher
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Event Dispatcher Interface.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Event
 * @subpackage 	Dispatcher
 */
interface KEventDispatcherInterface
{
    /**
     * Dispatches an event by dispatching arguments to all listeners that handle
     * the event and returning their return values.
     *
     * @param   string  The event name
     * @param   object|array   An array, a KConfig or a KEvent object
     * @return  KEvent
     */
    public function dispatchEvent($name, $event = array());

    /**
     * Add an event listener
     *
     * @param  string   $name      The event name
     * @param  callable $listener  The listener
     * @param  integer  $priority  The event priority, usually between 1 (high priority) and 5 (lowest),
     *                             default is 3. If no priority is set, the command priority will be used
     *                             instead.
     * @return KEventDispatcherInterface
     */
    public function addEventListener($name, $listener, $priority = KEvent::PRIORITY_NORMAL);

    /**
     * Remove an event listener
     *
     * @param   string   $event     The event name
     * @param   callable $listener  The listener
     * @return  KEventDispatcherInterface
     */
    public function removeEventListener($name, $listener);

    /**
     * Get a list of listeners for a specific event
     *
     * @param   string          The event name
     * @return  KObjectQueue    An object queue containing the listeners
     */
    public function getListeners($name);

    /**
     * Check if we are listening to a specific event
     *
     * @param   string  The event name
     * @return  boolean TRUE if we are listening for a specific event, otherwise FALSE.
     */
    public function hasListeners($name);

    /**
     * Add an event subscriber
     *
     * @param  object    The event subscriber to add
     * @return  KEventDispatcherInterface
     */
    public function addEventSubscriber(KEventSubscriberInterface $subscriber, $priority = null);

    /**
     * Remove an event subscriber
     *
     * @param  object    The event subscriber to remove
     * @return  KEventDispatcherInterface
     */
    public function removeEventSubscriber(KEventSubscriberInterface $subscriber);

    /**
     * Gets the event subscribers
     *
     * @return array    An associative array of event subscribers, keys are the subscriber handles
     */
    public function getSubscribers();

    /**
     * Check if the handler is connected to a dispatcher
     *
     * @param  object  The event dispatcher
     * @return boolean TRUE if the handler is already connected to the dispatcher. FALSE otherwise.
     */
    public function isSubscribed(KEventSubscriberInterface $subscriber);

    /**
     * Set the priority of an event
     *
     * @param  string   $name      The event name
     * @param  callable $listener  The listener
     * @param  integer  $priority  The event priority
     * @return  KEventDispatcherInterface
     */
    public function setEventPriority($name, $listener, $priority);

    /**
     * Get the priority of an event
     *
     * @param   string    $name      The event name
     * @param   callable  $listener  The listener
     * @return  integer|false The event priority or FALSE if the event isn't listened for.
     */
    public function getEventPriority($name, $listener);


}