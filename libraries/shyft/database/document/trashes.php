<?php
class SDatabaseDocumentTrashes extends SDatabaseDocumentDefault
{
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'name'      => '_trash'
        ));

        parent::_initialize($config);
    }
}