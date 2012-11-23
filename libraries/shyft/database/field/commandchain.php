<?php

abstract class SDatabaseFieldCommandchain extends SDatabaseFieldAbstract implements KCommandInterface
{
	/**
	 * The behavior priority
	 *
	 * @var integer
	 */
	protected $_priority;

	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     * @return void
     */
	protected function _initialize(KConfig $config)
    {
    	$config->append(array(
			'priority'   => KCommand::PRIORITY_NORMAL,
		));

    	parent::_initialize($config);
    }
	/**
	 * Command handler
	 *
	 * This function transmlated the command name to a command handler function of
	 * the format '_before[Command]' or '_after[Command]. Command handler
	 * functions should be declared protected.
	 *
	 * @param 	string  	The command name
	 * @param 	object   	The command context
	 * @return 	boolean		Can return both true or false.
	 */
	public function execute($name, KCommandContext $context)
	{
		$identifier = clone $context->caller->getIdentifier();
		$type       = array_pop($identifier->path);

		$parts  = explode('.', $name);
		$method = '_'.$parts[0].ucfirst($type).ucfirst($parts[1]);

		if(method_exists($this, $method)) {
			return $this->$method($context);
		}

		return true;
	}

	/**
	 * Get the priority of a behavior
	 *
	 * @return	integer The command priority
	 */
  	public function getPriority()
  	{
  		return $this->_priority;
  	}

	protected function _afterDocumentInsert(KCommandContext $context)
	{
		$this->enableProcessing();
	}

	protected function _afterDocumentUpdate(KCommandContext $context)
	{
		$this->enableProcessing();
	}
}