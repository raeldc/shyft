<?php
/**
 * @version     $Id$
 * @package        Koowa_Behavior
 * @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Abstract Behavior Class
 *
 * @author        Johan Janssens <johan@nooku.org>
 * @package     Koowa_Behavior
 */
abstract class KBehaviorAbstract extends KMixinAbstract implements KBehaviorInterface
{
    /**
     * The behavior priority
     *
     * @var integer
     */
    protected $_priority;

    /**
     * The service identifier
     *
     * @var KServiceIdentifier
     */
    private $__service_identifier;

    /**
     * The service manager
     *
     * @var KServiceManager
     */
    private $__service_manager;

    /**
     * Constructor.
     *
     * @param     object     An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        //Set the service container
        if (isset($config->service_manager)) {
            $this->__service_manager = $config->service_manager;
        }

        //Set the service identifier
        if (isset($config->service_identifier)) {
            $this->__service_identifier = $config->service_identifier;
        }

        parent::__construct($config);

        $this->_priority = $config->priority;

        //Automatically mixin the behavior
        if ($config->auto_mixin) {
            $this->mixin($this);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param     object     An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority' => KCommand::PRIORITY_NORMAL,
            'auto_mixin' => false
        ));

        parent::_initialize($config);
    }

    /**
     * Get the priority of a behavior
     *
     * @return    integer The command priority
     */
    public function getPriority()
    {
        return $this->_priority;
    }

    /**
     * Command handler
     *
     * This function transmlated the command name to a command handler function of the format '_before[Command]' or
     * '_after[Command]. Command handler functions should be declared protected.
     *
     * @param     string      The command name
     * @param     object       The command context
     * @return     boolean        Can return both true or false.
     */
    public function execute($name, KCommandContext $context)
    {
        $identifier = clone $context->getSubject()->getIdentifier();
        $type = array_pop($identifier->path);

        $parts = explode('.', $name);
        $method = '_' . $parts[0] . ucfirst($type) . ucfirst($parts[1]);

        if (method_exists($this, $method)) {
            return $this->$method($context);
        }

        return true;
    }

    /**
     * Get an object handle
     *
     * This function only returns a valid handle if one or more command handler functions are defined. A commend handler
     * function needs to follow the following format : '_afterX[Event]' or '_beforeX[Event]' to be recognised.
     *
     * @return string A string that is unique, or NULL
     * @see execute()
     */
    public function getHandle()
    {
        $methods = $this->getMethods();

        foreach ($methods as $method)
        {
            if (substr($method, 0, 7) == '_before' || substr($method, 0, 6) == '_after') {
                return parent::getHandle();
            }
        }

        return null;
    }

    /**
     * Get the methods that are available for mixin based
     *
     * This function also dynamically adds a function of format is[Behavior] to allow client code to check if the
     * behavior is callable.
     *
     * @param object The mixer requesting the mixable methods.
     * @return array An array of methods
     */
    public function getMixableMethods(KObject $mixer = null)
    {
        $methods = parent::getMixableMethods($mixer);
        $methods['is' . ucfirst($this->getIdentifier()->name)] = function() { return true; };

        unset($methods['execute']);
        unset($methods['getIdentifier']);
        unset($methods['getPriority']);
        unset($methods['getHandle']);
        unset($methods['getService']);

        return $methods;
    }

    /**
     * Get an instance of a class based on a class identifier only creating it if it does not exist yet.
     *
     * @param    string|object    $identifier The class identifier or identifier object
     * @param    array              $config     An optional associative array of configuration settings.
     * @throws    \RuntimeException If the service manager has not been defined.
     * @return    object          Return object on success, throws exception on failure
     * @see     KObjectServiceable
     */
    final public function getService($identifier = null, array $config = array())
    {
        if (isset($identifier))
        {
            if (!isset($this->__service_manager))
            {
                throw new RuntimeException(
                    "Failed to call " . get_class($this) . "::getService(). No service_manager object defined."
                );
            }

            $result = $this->__service_manager->get($identifier, $config);
        }
        else $result = $this->__service_manager;

        return $result;
    }

    /**
     * Gets the service identifier.
     *
     * @param    string|object    $identifier The class identifier or identifier object
     * @throws    \RuntimeException If the service manager has not been defined.
     * @return    KServiceIdentifier
     * @see     KObjectServiceable
     */
    final public function getIdentifier($identifier = null)
    {
        if (isset($identifier))
        {
            if (!isset($this->__service_manager))
            {
                throw new RuntimeException(
                    "Failed to call " . get_class($this) . "::getIdentifier(). No service_manager object defined."
                );
            }

            $result = $this->__service_manager->getIdentifier($identifier);
        }
        else  $result = $this->__service_identifier;

        return $result;
    }
}