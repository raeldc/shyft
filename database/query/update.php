<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_Database
 * @subpackage  Query
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Update database query class
 *
 * @author		Gergo Erdosi <gergo@timble.net>
 */
class KDatabaseQueryUpdate extends KDatabaseQueryAbstract
{
    /**
     * The table name.
     * 
     * @var string
     */
    public $table;
    
    /**
     * Data of the set clause.
     * 
     * @var array
     */
    public $set = array();
    
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
     * The number of rows that can be updated.
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
     * @param   string The name of the table to update.
     * @return  KDatabaseQueryUpdate
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }
    
	/**
     * Build the set clause of the query.
     *
     * @param   array|string An array or string of columns to update.
     * @return  KDatabaseQueryUpdate
     */
    public function set($columns)
    {
        foreach ((array) $columns as $column) {
            $this->set[] = $column;
        }
        
        return $this;
    }
    
	/**
     * Build the where clause of the query.
     *
     * @param   string          The condition.
     * @param   string          Combination type, defaults to 'AND'.
     * @return  KDatabaseQueryUpdate
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
     * @return  KDatabaseQueryUpdate
     */
    public function order($columns, $direction = 'ASC')
    {
        foreach ((array) $columns as $column) {
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
     * @return  KDatabaseQueryUpdate
     */
    public function limit($limit)
    {
        $this->limit  = (int) $limit;
        
        return $this;
    }
    
	/**
     * Bind values to a corresponding named placeholders in the query. 
     *
     * @param   array Associative array of parameters.
     * @return  KDatabaseQueryUpdate
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
        $prefix = $adapter->getTablePrefix();
        $query = 'UPDATE';
        
        if ($this->table) {
            $query .= ' '.$adapter->quoteIdentifier($prefix.$this->table);
        }
        
        if ($this->set) {
            $columns = array();
            foreach ($this->set as $column) {
                $columns[] = ' '. $adapter->quoteIdentifier($column);
            }
            
            $query .= ' SET '.implode(', ', $columns);
        }
        
        if ($this->where) {
            $query .= ' WHERE';
            
            foreach ($this->where as $where) {
                if (!empty($where['combination'])) {
                    $query .= ' '.$where['combination'];
                }
                
                $query .= ' '.$adapter->quoteIdentifier($where['condition']);
            }
        }
        
        if ($this->order) {
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
        
        if ($this->params) {
            $params = array();
            foreach ($this->params as $key => $value) {
                if (is_array($value)) {
                    $params[':'.$key] = '('.$adapter->quoteValue($value).')';
                } else {
                    $params[':'.$key] = $adapter->quoteValue($value);
                }
            }
            
            // TODO: Use anonymous function instead of /e when we switch to PHP 5.3.
            $query = preg_replace("/(?<!\w):\w+/e", '$params[\'$0\']', $query);
        }
        
        return $query;
    }
}