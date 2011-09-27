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
 * Default Paginator Helper
.*
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 * @uses        KRequest
 * @uses        KConfig
 */
class ComDefaultTemplateHelperPaginator extends KTemplateHelperPaginator
{
    /**
     * Render item pagination
     * 
     * @param   array   An optional array with configuration options
     * @return  string  Html
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     */
    public function pagination($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'total'   => 0,
            'display' => 4,
            'offset'  => 0,
            'limit'   => 0,
        ));
        
        $this->_initialize($config);

        $html  = '<div class="container pagination">';
        $html .=  $this->_pages($this->_items($config));
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render a list of pages links
     * 
     * This function is overriddes the default behavior to render the links in the khepri template
     * backend style.
     *
     * @param   araay   An array of page data
     * @return  string  Html
     */
    protected function _pages($pages)
    {
        $html = '<ul>';

        $class = $pages['first']->active ? '' : 'off disabled';
        $html  .= $this->_link($pages['first'], 'First', array('start', 'prev', $class));
        
        $class = $pages['previous']->active ? '' : 'off disabled';
        $html  .= $this->_link($pages['previous'], 'Prev', array('prev', $class));
        
        foreach($pages['pages'] as $page) {
            $html .= $this->_link($page, $page->page);
        }
        
        $class = $pages['next']->active ? '' : 'off disabled';
        $html  .= $this->_link($pages['next'], 'Next', array('next', $class));
        
        $class = $pages['last']->active ? '' : 'off disabled';
        $html  .= $this->_link($pages['last'], 'Last', array('next', 'end', $class));

        $html  .= '</ul>';
        return $html;
    }

    
    protected function _link($page, $title, $classes = array())
    {
        $url   = clone KRequest::url();
        $query = $url->getQuery(true);

        $query['limit']      = $page->limit;
        $query['offset'] 	 = $page->offset;   

        $url->setQuery($query);

        $class = $page->current ? array('active') : array();

        $class = array_merge($class, $classes);

        $class = 'class="'.implode(' ', $class).'"';

        if($page->active && !$page->current) {
            $html = '<li '.$class.'><a href="index.php?'.$url->getQuery().'">'.$title.'</a></li>';
        } else {
            $html = '<li '.$class.'><a href="#">'.$title.'</a></li>';
        }

        return $html;
    }
}