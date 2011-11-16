<?php

class ComContentTemplateHelperListbox extends ComDefaultTemplateHelperListbox
{
    public function types($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'model'     => 'types',
            'name'      => 'type',
            'text'      => 'title',
            'value'     => 'id',
            'deselect'  => false,
            'selected'  => $config->selected,
        ));

        return parent::_listbox($config);
    }
}