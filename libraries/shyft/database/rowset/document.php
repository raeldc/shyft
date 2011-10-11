<?php

class SDatabaseRowsetDocument extends KDatabaseRowsetAbstract
{
	/**
     * Returns a KDatabaseRow 
     * 
     * This functions accepts either a know position or associative array of key/value pairs
     *
     * @param   string|array  	The position or the key or an associatie array of column data 
     *                          to match
     * @return KDatabaseRow(set)Abstract Returns a row or rowset if successfull. Otherwise NULL.
     */
    public function find($needle)
    {
        $result = null;
        
        if(!is_scalar($needle))
        {
            $result = clone $this;
            
            foreach ($this as $i => $row) 
            { 
                foreach($needle as $key => $value)
                {
                    if(!in_array($row->{$key}, (array) $value)) {
                        $result->extract($row);
                    } 
                }
            }
        }
        else 
        {
            if(isset($this->_object_set[$needle])) {
                $result = $this->_object_set[$needle];
            }
        }

        return $result;
    }
}