<?php
/**
 * @category	Flow
 * @package     Flow_Database
 * @subpackage  Query
 * @copyright	Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Database Select Class for database select statement generation
 *
 * @author		Israel Canasa <raeldc@gmail.com>
 * @category	Flow
 * @package     Flow_Database
 * @subpackage  Query
 */
class FlowDatabaseQueryDocument extends KObject
{
    public $from;

    public $where = array();

    public $sort = array();

    public $limit = null;

    public $offset = null;

    public $query = null;

    public function where( $property, $constraint = null, $value = null, $condition = 'AND' )
    {
        if(!empty($property)) 
        {
            $where = array();
            $where['property'] = $property;

            if(isset($constraint))
            {
                $constraint = strtoupper($constraint);
                $condition  = strtoupper($condition);
            
                $where['constraint'] = $constraint;
                $where['value']      = $value;
            }
        
            $where['condition']  = count($this->where) ? $condition : '';

            //Make sure we don't store the same where clauses twice
            $signature = md5($property.$constraint.$value);
            if(!isset($this->where[$signature])) {
                $this->where[$signature] = $where;
            }
        }

        return $this;
    }

    public function limit( $limit, $offset = 0 )
    {
        $this->limit  = (int) $limit;
        $this->offset = (int) $offset;
        
        return $this;
    }

    public function sort( $columns, $direction = 'ASC' )
    {
        settype($columns, 'array'); //force to an array

        foreach($columns as $column)
        {
            $this->sort[] = array(
                $column    => ($direction == 'DESC') ? -1 : 1
            );
        }

        return $this;
    }

    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    public function build($query = null)
    {
        if (!is_null($query))
            return $this->query = $query;

        $this->query = array();

        // TODO: Try to account for OR not just AND
        foreach ($this->where as $where) 
        {

            switch($where['constraint']){
                case '=':
                    $this->query[$where['property']] = $where['value'];
                break;

                default:
                    $constraint = array(
                        'in' => '$in',
                        '<' => '$lt',
                        '<=' => '$lte',
                        '>' => '$gt',
                        '>=' => '$gte',
                        '<>' => '$ne',
                        '!=' => '$ne',
                    );

                    $value = (strtolower($where['constraint']) == 'in' && is_string($value)) ? array($value) : $where['value'];

                    $this->query[$where['property']] = array(strtolower($constraint[$where['constraint']]) => $value);
                break;
            }
        }

        return empty($this->query) ? new stdclass : (object)$this->query;
    }
}