<?php
/**
 * @version     $Id$
 * @package     Koowa_Database
 * @subpackage  Query
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Delete database query class
 *
 * @author      Gergo Erdosi <gergo@timble.net>
 * @package     Koowa_Database
 * @subpackage  Query
 */
class KDatabaseQueryDelete extends KDatabaseQueryAbstract
{
    /**
     * The table name.
     *
     * @var string
     */
    public $table;

    /**
     * Data of the where clause.
     *
     * @var array
     */
    public $where = array();

    /**
     * Data of the order clause.
     *
     * @var array
     */
    public $order = array();

    /**
     *  The number of rows that can be deleted.
     *
     * @var integer
     */
    public $limit;

    /**
     * Parameters to bind.
     *
     * @var array
     */
    public $params = array();

    /**
     * Build the table clause of the query.
     *
     * @param   string The name of the table.
     * @return  KDatabaseQueryDelete
     */
    public function table($table) {
        $this->table = $table;

        return $this;
    }

    /**
     * Build the where clause of the query.
     *
     * @param   string          The condition.
     * @param   string          Combination type, defaults to 'AND'.
     * @return  KDatabaseQueryDelete
     */
    public function where($condition, $combination = 'AND')
    {
        $this->where[] = array(
            'condition'   => $condition,
            'combination' => count($this->where) ? $combination : ''
        );

        return $this;
    }

    /**
     * Build the order clause of the query.
     *
     * @param   array|string  A string or array of ordering columns.
     * @param   string        Either DESC or ASC.
     * @return  KDatabaseQueryDelete
     */
    public function order($columns, $direction = 'ASC')
    {
        foreach ((array) $columns as $column)
        {
            $this->order[] = array(
                'column'    => $column,
                'direction' => $direction
            );
        }

        return $this;
    }

    /**
     * Build the limit clause of the query.
     *
     * @param   integer Number of items to update.
     * @return  KDatabaseQueryDelete
     */
    public function limit($limit)
    {
        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * Bind values to a corresponding named placeholders in the query.
     *
     * @param   array Associative array of parameters.
     * @return  KDatabaseQueryDelete
     */
    public function bind(array $params)
    {
        foreach ($params as $key => $value) {
            $this->params[$key] = $value;
        }

        return $this;
    }

    /**
     * Render the query to a string.
     *
     * @return  string  The query string.
     */
    public function __toString()
    {
        $adapter = $this->getAdapter();
        $prefix  = $adapter->getTablePrefix();
        $query   = 'DELETE';

        if ($this->table) {
            $query .= ' FROM '.$adapter->quoteIdentifier($prefix.$this->table);
        }

        if ($this->where)
        {
            $query .= ' WHERE';

            foreach ($this->where as $where) 
            {
                if (!empty($where['combination'])) {
                    $query .= ' '.$where['combination'];
                }

                $query .= ' '.$adapter->quoteIdentifier($where['condition']);
            }
        }

        if ($this->order)
        {
            $query .= ' ORDER BY ';

            $list = array();
            foreach ($this->order as $order) {
                $list[] = $adapter->quoteIdentifier($order['column']).' '.$order['direction'];
            }

            $query .= implode(' , ', $list);
        }

        if ($this->limit) {
            $query .= ' LIMIT '.$this->offset.' , '.$this->limit;
        }

        if ($this->params)
        {
            // TODO: Use anonymous function instead of callback.
            $query = preg_replace_callback("/(?<!\w):\w+/", array($this, '_replaceParam'), $query);
        }

        return $query;
    }
    
    /**
     * Callback method for parameter replacement.
     * 
     * @param  array  $matches Matches of preg_replace_callback.
     * @return string The replacement string.
     */
    protected function _replaceParam($matches)
    {
        $key         = substr($matches[0], 1);
        $replacement = $this->getAdapter()->quoteValue($this->params[$key]);
        
        return is_array($this->params[$key]) ? '('.$replacement.')' : $replacement;
    }
}
