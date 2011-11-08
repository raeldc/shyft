<?php
/**
 * @category    Shyft
 * @package     Shyft_Database
 * @subpackage  Behavior
 * @copyright   Copyright (C) 2011 Israel Canasa. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 */

/**
 * Database Sluggable Behavior
 *
 * @author      Israel Canasa <raeldc@gmail.com>
 * @category    Shyft
 * @package     Shyft_Database
 * @subpackage  Behavior
 */
class SDatabaseBehaviorSluggable extends KDatabaseBehaviorSluggable
{
    /**
     * Insert a slug
     *
     * If multiple columns are set they will be concatenated and seperated by the
     * separator in the order they are defined.
     *
     * Requires a 'slug' column
     *
     * @return void
     */
    protected function _afterDocumentInsert(KCommandContext $context)
    {
        $this->_createSlug();
        $this->save();
    }

    /**
     * Update the slug
     *
     * Only works if {@link $updadocument} property is TRUE. If the slug is empty
     * the slug will be regenerated. If the slug has been modified it will be
     * sanitized.
     *
     * Requires a 'slug' column
     *
     * @return void
     */
    protected function _beforeDocumentUpdate(KCommandContext $context)
    {
        if($this->_updadocument) {
            $this->_createSlug();
        }
    }

    /**
     * Create a sluggable filter
     *
     * @return void
     */
    protected function _createFilter()
    {
        $config = array();
        $config['separator'] = $this->_separator;
        
        //Create the filter
        $filter = $this->getService('koowa:filter.slug', $config);
        return $filter;
    }
    
    /**
     * Create the slug
     *
     * @return void
     */
    protected function _createSlug()
    {
        //Create the slug filter
        $filter = $this->_createFilter();
        
        if(empty($this->slug))
        {
            $slugs = array();
            foreach($this->_columns as $column) 
            {
                $slug = $filter->sanitize($this->$column);

                if (!empty($slug)) {
                    $slugs[] = $slug;
                }
            }

            $this->slug = implode($this->_separator, $slugs);
            
            //Canonicalize the slug
            $this->_canonicalizeSlug();

            return;
        }

        if(in_array('slug', $this->getModified())) 
        {
            $this->slug = $filter->sanitize($this->slug);
            
            //Canonicalize the slug
            $this->_canonicalizeSlug();
        }
    }
    
    /**
     * Make sure the slug is unique
     * 
     * This function checks if the slug already exists and if so appends
     * a number to the slug to make it unique. The slug will get the form
     * of slug-x.
     *
     * @return void
     */
    protected function _canonicalizeSlug()
    {
        $document = $this->getDocument();
        $query = clone $document->getQuery();

        //If unique is not set, use the column metadata
        if(is_null($this->_unique)) { 
            $this->_unique = true;
        }

        //If the slug needs to be unique and it already exist make it unqiue
        if($this->_unique && $document->count($query->where('slug', '=', $this->slug))) 
        {
            // @TODO: Make sure this is working
            $query->where('slug', 'LIKE', $this->slug.'-%');          

            $slugs = $document->select($query, KDatabase::FETCH_FIELD_LIST);
            
            $i = 1;
            while(in_array($this->slug.'-'.$i, $slugs)) {
                $i++;
            }
            
            $this->slug = $this->slug.'-'.$i;
        }
    }
}