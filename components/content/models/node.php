<?php

abstract class ComContentModelNode extends SModelDocument
{
	/**
     * Method to get the "contents" document object 
     * 
     * Function catches KDatabaseDocumentExceptions that are thrown for documents that 
     * don't exist. If no document object can be created the function will return FALSE.
     *
     * @return KDatabaseDocumentAbstract
     */
    public function getDocument()
    {
        if($this->_document !== false)
        {
            if(!($this->_document instanceof ComContentDatabaseDocumentContents))
		    {   		        
		        //Make sure we have a document identifier
		        if(!($this->_document instanceof KIdentifier)) {
		            $this->setDocument($this->_document);
			    }

			    $this->_document = KFactory::get($this->_document);
			    
			    if (!($this->_document instanceof ComContentDatabaseDocumentContents)) 
			    {
			    	$identifier = clone $this->_document->getIdentifier();
			    	$identifier->package = 'content';
			    	$identifier->name = 'contents';

			    	$this->_document = KFactory::get($identifier);
			    }
            }
        }

        return $this->_document;
    }
}