<?php
/**
* @version      $Id$
* @package      Koowa_Template
* @subpackage   Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link         http://www.nooku.org
*/

/**
 * Template read filter for the @template alias. To load templates inline
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage  Filter
 */
class KTemplateFilterTemplate extends KTemplateFilterAbstract implements KTemplateFilterRead
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'   => KCommand::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
     * Replace template alias with a fully qualified identifier
     *
     * This function only replaces relative identifiers to a full path
     * based on the path of the template.
     *
     * @param string
     * @return KTemplateFilterAlias
     */
    public function read(&$text)
    {
        if(preg_match_all('#@template\(\'(.*)\'#siU', $text, $matches))
		{
			foreach($matches[1] as $key => $match)
			{
			    if(is_string($match) && strpos($match, '.') === false )
		        {
                    $identifier = clone $this->getTemplate()->getFile();
                    $identifier->name = '';

		            $text = str_replace($matches[0][$key], '$this->loadIdentifier('."'".$identifier.".".$matches[1][$key]."'", $text);
		        }
			}
		}

        return $this;
    }
}