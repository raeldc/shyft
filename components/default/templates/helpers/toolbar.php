<?php
/**
 * @category	Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Israel Canasa. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.shyfted.com
 */


/**
 * Template Toolbar Helper
.*
 * @author      Israel Canasa <raeldc@shyfted.com>
 * @category    Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 */
class ComDefaultTemplateHelperToolbar extends KTemplateHelperAbstract
{   
    /**
     * Render the toolbar 
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'toolbar' => null,
            'form' => null
        ));
        
        $html	= '<menu type="toolbar" class="toolbar well" id="toolbar-'.$config->toolbar->getName().'">';
        
	    foreach ($config->toolbar->getCommands() as $command) 
	    {
            $name = $command->getName();
	        
	        if(method_exists($this, $name)) {
                $html .= $this->$name(array('command' => $command, 'form' => $config->form));
            } else {
                $html .= $this->command(array('command' => $command, 'form' => $config->form));   
            }
       	}
		
		$html .= '</menu>';
		
		return $html;
    }
    
    /**
     * Render a toolbar command
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function command($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL,
            'form' => null,
        ));
        
        $command = $config->command;
        
         //Add a toolbar class
        $command->attribs->class->append(array('toolbar', 'btn'));

        //Create the id
        $id = 'toolbar-'.$command->id;

        // Add classes
        $command->attribs->class = implode(" ", KConfig::unbox($command->attribs->class));

        // If href is set, make the toolbar an anchor, else make it button
        if (!is_null($command->attribs->href)) 
        {
            $html  = '  <a '.KHelperArray::toString($command->attribs).'>';
            $html .= '      <span class="'.$command->icon.'" title="'.$command->title.'"></span>';
            $html .= $command->label;
            $html .= '   </a>';
        }
        else
        {
            if (!is_null($config->form)) {
                $command->attribs->form = $config->form;
            }

            $html  = '  <button type="submit" '.KHelperArray::toString($command->attribs).'>';
            $html .= '      <span class="'.$command->icon.'" title="'.$command->title.'"></span>';
            $html .= $command->label;
            $html .= '   </button>';
        }
       	
    	return $html;
    }
    
	/**
     * Render a separator
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function separator($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL
        ));
        
        $command = $config->command;
          
       	$html = '<span class="separator"></span>';
       	
    	return $html;
    }
    
	/**
     * Render a modal button
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function modal($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL
        ));
        
        //$html  = $this->getTemplate()->renderHelper('behavior.modal');
        $html = $this->command($config);
        
    	return $html;
    }
}