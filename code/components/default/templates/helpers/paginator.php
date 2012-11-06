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
 * @author      Israel Canasa <shyft@me.com>
 * @category    Shyft
 * @package     Shyft_Components
 * @subpackage  Default
 * @uses        KRequest
 * @uses        KConfig
 */
class ComDefaultTemplateHelperPaginator extends KTemplateHelperSelect
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
        $config = new KConfigPaginator($config);
        $config->append(array(
            'total'      => 0,
            'display'    => 4,
            'offset'     => 0,
            'limit'      => 0,
            'attribs'    => array(),
            'show_limit' => false,
            'show_count' => false
        ));

        $html = '';
        $html .= '<div class="container pagination">';
        if($config->show_limit) {
            $html .= '<div class="limit">'.text('Display NUM').' '.$this->limit($config).'</div>';
        }
        $html .=  $this->pages($config);
        if($config->show_count) {
            $html .= '<div class="count"> '.text('Page').' '.$config->current.' '.text('of').' '.$config->count.'</div>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Render a select box with limit values
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html select box
     */
    public function limit($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'limit'     => 0,
            'attribs'   => array(),
        ));

        $html = '';

        $selected = '';
        foreach(array(10 => 10, 20 => 20, 50 => 50, 100 => 100) as $value => $text)
        {
            if($value == $config->limit) {
                $selected = $value;
            }

            $options[] = $this->option(array('text' => $text, 'value' => $value));
        }

        $html .= $this->optionlist(array('options' => $options, 'name' => 'limit', 'attribs' => $config->attribs, 'selected' => $selected));
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
    public function pages($config)
    {
        $config = new KConfigPaginator($config);
        $config->append(array(
            'total'      => 0,
            'display'    => 4,
            'offset'     => 0,
            'limit'      => 0,
            'attribs'   => array(),
        ));

        $html = '<ul>';

        $config->pages->first->attribs = $config->pages->first->active ? array() : array('class' => 'off disabled');
        $html  .= $this->link($config->pages->first);

        $config->pages->prev->attribs = $config->pages->prev->active ? array() : array('class' => 'off disabled');
        $html  .= $this->link($config->pages->prev);

        foreach($config->pages->offsets as $offset) {
            $html .= $this->link($offset);
        }

        $config->pages->next->attribs = $config->pages->next->active ? array() : array('class' => 'off disabled');
        $html  .= $this->link($config->pages->next);

        $config->pages->last->attribs = $config->pages->last->active ? array() : array('class' => 'off disabled');
        $html  .= $this->link($config->pages->last);

        $html  .= '</ul>';
        return $html;
    }

    /**
     * Render a page link
     *
     * @param   object The page data
     * @param   string The link title
     * @return  string  Html
     */
    public function link($config)
    {
        $config = new KConfig($config);
        $config->append(array(
            'title'   => '',
            'current' => false,
            'active'  => false,
            'offset'  => 0,
            'limit'   => 0,
            'rel'     => '',
            'attribs'  => array(),
        ))->attribs->append(array(
            'class' => ''
        ));

        $route = $this->getTemplate()->getView()->getRoute('limit='.$config->limit.'&offset='.$config->offset);

        $class = $config->current ? ' active' : '';
        $class = 'class="'.$config->attribs->class.$class.'"';
        $rel   = !empty($config->rel) ? 'rel="'.$config->rel.'"' : '';

        if($config->active && !$config->current) {
            $html = '<li '.$class.'><a href="'.$route.'" '.$rel.'>'.text($config->title).'</a></li>';
        } else {
            $html = '<li '.$class.'><a href="#">'.text($config->title).'</li></li>';
        }

        return $html;
    }
}