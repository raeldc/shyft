<?php

class ComDefaultTemplateFilterToolbar extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_toolbar = $config->toolbar;
    }
    
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'toolbar' => null,
        ));
        
        parent::_initialize($config);
    }

    public function write(&$text)
    {
        $form = $this->getForm();

        $toolbar = $this->getTemplate()->getHelper('toolbar')->render(array(
            'toolbar' => $this->_toolbar,
            'form' => $form,
        ));

        // Add name of form based on the layout's identifier
        $text = preg_replace('/(<form(.*)>)/i', '<form id="'.$form.'" \\2>', $text);

        $text = $toolbar.$text;

        return $this;
    }

    /**
     * Get the layout and generate a form name. Used by toolbar buttons.
     *
     * @return string The layout name
     */
    public function getForm()
    {
        $view = $this->getTemplate()->getView();

        $form = array(
            $view->getIdentifier()->package,
            $view->getName(),
            $view->getLayout()
        );

        return implode('_', $form);
    }
}