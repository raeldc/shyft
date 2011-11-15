<?php
/**
 * @category    Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.shyfted.com
 */

/**
 * Default Toolbar
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 */
class ComDefaultControllerToolbarDefault extends KControllerToolbarDefault
{
    /**
     * Push the toolbar into the view
     *
     * @param   KEvent  A event object
     */
    public function onBeforeControllerGet(KEvent $event)
    {   
        $event->caller->getView()->toolbar = $this;
    }

    /**
     * Add default toolbar commands and set the toolbar title
     * .
     * @param   KEvent  A event object
     */
    public function onAfterControllerRead(KEvent $event)
    {
        $name = ucfirst($this->getController()->getIdentifier()->name);

        if($this->getController()->getModel()->getState()->isUnique()) 
        {        
            $saveable = $this->getController()->canEdit();
            $title    = 'Edit '.$name;
        } 
        else 
        {
            $saveable = $this->getController()->canAdd();
            $title    = 'New '.$name;  
        }

        if($saveable)
        {
            $this->setTitle($title)
                 ->addCommand('save')
                 ->addCommand('apply');
        }

        $this->addCommand('cancel',  array('attribs' => array('data-novalidate' => 'novalidate')));       
    }
      
    /**
     * Add default toolbar commands
     * .
     * @param   KEvent  A event object
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        if($this->getController()->canAdd()) 
        {
            $identifier = $this->getController()->getIdentifier();
            $config     = array('attribs' => array(
                            'href' => $this->getController()->getView()->getRoute('layout=form&view='.$identifier->name)
                          ));
                    
            $this->addCommand('new', $config);
        }
            
        if($this->getController()->canDelete()) {
            $this->addCommand('delete');    
        }
    }
    
    /**
     * Enable toolbar command
     * 
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandEnable(KControllerToolbarCommand $command)
    {   
        $command->append(array(
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:1}'
            )
        ));
    }
    
    /**
     * Disable toolbar command
     * 
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandDisable(KControllerToolbarCommand $command)
    {   
        $command->append(array(   
            'attribs' => array(
                'data-action' => 'edit',
                'data-data'   => '{enabled:0}'
            )
        ));
    }
    
    /**
     * Export toolbar command
     * 
     * @param   object  A KControllerToolbarCommand object
     * @return  void
     */
    protected function _commandExport(KControllerToolbarCommand $command)
    {
        //Get the states
        $states = $this->getController()->getModel()->getState()->toArray(); 
        
        unset($states['limit']);
        unset($states['offset']);
        
        $states['format'] = 'csv';
          
        //Get the query options
        $query  = http_build_query($states);
        $com    = $this->getIdentifier()->package;
        $view   = $this->getIdentifier()->name;
        
        $command->append(array(
            'attribs' => array(
                'href' =>  $this->getController()->getView()->getRoute('view='.$view.'&'.$query)
            )
        ));
    }
}