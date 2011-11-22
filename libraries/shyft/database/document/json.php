<?php

class SDatabaseDocumentJson extends SDatabaseDocumentAbstract
{
	protected $_storage_path;
	protected $_data;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_storage_path = $config->storage_path;
		$this->_data = $config->data;
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'storage_path' => null,
			'data'         => null,
		));
	
		parent::_initialize($config);
	}
	
	public function getData()
	{
	    if(empty($this->_data))
		{
			$this->_data = new KConfig(
				get_object_vars(
					json_decode(
						file_get_contents($this->_storage_path)
					)
				)
			);
		}
		
		return $this->_data;
	}

	public function find($path = null, $mode = KDatabase::FETCH_ROWSET)
    {
    	$result = $this->findPath($path);
    	switch($mode)
        {
            case KDatabase::FETCH_ROW    : 
                $data = $this->getRow();
                if(isset($result) && !empty($result)) {
                   $data->setData($result, false)->setStatus(KDatabase::STATUS_LOADED);
                }
            break;

            case KDatabase::FETCH_ROWSET : 
                $data = $this->getRowset();

                if(isset($result) && !empty($result)) {
                    $data->addData($result, false);
                }
			break;
            
            default : $data = $result;
        }

        return $data;
    }

    public function findPath($path, $data = null)
    {
    	if (is_null($data)) {
    		$data = $this->getData()->toArray();
    	}

    	$paths        = explode('.', $path);    	
    	$current_path = array_shift($paths);

    	foreach ($data as $key => $value) 
    	{
    		$current_value = $value;

    		if (is_scalar($value)) {
    			continue;
    		}

    		if ($key != $current_path && count($paths)) 
    		{
    			$current_value = $this->findPath(implode('.', $paths), $value);
    		}
    	}

    	return $current_value;
    }
}