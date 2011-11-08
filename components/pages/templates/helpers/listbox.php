<?php

class ComPagesTemplateHelperListbox extends ComDefaultTemplateHelperListbox
{
	public function groups($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'model'		=> 'groups',
            'name'      => 'group',
            'text'      => 'name',
            'value'     => 'id',
            'deselect'  => false,
            'selected'  => $config->selected,
        ));

        return parent::_listbox($config);
    }
}