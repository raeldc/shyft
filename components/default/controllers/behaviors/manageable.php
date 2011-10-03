<?php

class ComDefaultControllerBehaviorManageable  extends KControllerBehaviorAbstract
{
	protected $_template_toolbar = null;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_template_toolbar = $config->template_toolbar;
	}
	
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority' => KCommand::PRIORITY_LOWEST,
            'template_toolbar' => null,
        ));

        parent::_initialize($config);
    }
    
    protected function _beforeGet(KCommandContext $context)
    {
        $view = $this->getView();

        //Assign some values in the view that allows it to decide what to display
        if($context->caller->isExecutable() && ($view instanceof KViewHtml))
        {
             $view->assign('can_edit', $context->caller->canEdit())
                  ->assign('can_add', $context->caller->canAdd())
                  ->assign('can_delete', $context->caller->canDelete());
        }
        
        //If the controller is dispatched and has a commandable behavior, add the toolbar filter to the view
        if($this->isDispatched() && $context->caller->isCommandable() && ($view instanceof KViewHtml))
        {
        	$toolbar = $this->getTemplateToolbar();

            $view->getTemplate()->addFilter(array(
				$this->getService($toolbar, array(
					'toolbar' => $context->caller->getToolbar(),
				))
            ));
        }
    }

    public function getTemplateToolbar()
    {
    	if (!($this->_template_toolbar instanceof KIdentifier)) 
    	{
    		$identifier = clone $this->getIdentifier();
    		$identifier->path = array('template', 'filter');
    		$identifier->name = 'toolbar';

    		$this->_template_toolbar = $identifier;
    	}

    	return $this->_template_toolbar;
    }
}