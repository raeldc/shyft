<?php
/**
 * @category    Koowa
 * @package     Koowa_Date
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Date Class
 *
 * @author  Gergo Erdosi <gergo@timble.net>
 */
class KDate extends DateTime implements KDateInterface
{
    /**
     * The name of months.
     *
     * @var array
     */
    protected $_months = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');

    /**
     * The name of days.
     *
     * @var array
     */
    protected $_days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
    
    /**
     * Constructor.
     *
     * @param   array|KConfig An associative array of configuration settings or a KConfig instance.
     */
    public function __construct( $config = array() )
    {
        if(!$config instanceof KConfig) $config = new KConfig($config);
        
        //Initialise the object
        $this->_initialize($config);
        
        parent::__construct($config->date, $config->timezone);
    }
    
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
        	'date'     => 'now',
            'timezone' => NULL,
        ));
    }

    /**
     * Returns date formatted according to given format.
     *
     * @param  string The format to use
     * @return string The formatted data
     */
    public function format($format)
    {
        $format = preg_replace_callback('/(?<!\\\)[DlFM]/', array($this, '_translate'), $format);

        return parent::format($format);
    }
    
    /**
     * Get a handle for this object
     *
     * This function returns an unique identifier for the object. This id can be used as
     * a hash key for storing objects or for identifying an object
     *
     * @return string A string that is unique
     */
    public function getHandle()
    {
        return spl_object_hash( $this );
    }

    /**
     * Translate day and month names.
     *
     * @param array Matched elements of preg_replace_callback.
     * @return The translated string
     */
    protected function _translate($matches)
    {
        switch ($matches[0]) 
        {
            case 'D':
                $replacement = JText::_(strtoupper(substr($this->_days[parent::format('w')], 0, 3)));
                break;

            case 'l':
                $replacement = JText::_(strtoupper($this->_days[parent::format('w')]));
                break;

            case 'F':
                $replacement = JText::_(strtoupper($this->_months[parent::format('n')]).'_SHORT');
                break;

            case 'M':
                $replacement = JText::_(strtoupper($this->_months[parent::format('n')]));
                break;
        }

        $replacement = preg_replace('/^([0-9])/', '\\\\\\\\\1', $replacement);
        $replacement = preg_replace('/([a-z])/i', '\\\\\1', $replacement);

        return $replacement;
    }
}