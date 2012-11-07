<?php

class ComThemesDatabaseDocumentThemes extends SDatabaseDocumentAbstract
{
	protected $_directory;
    protected $_manifest;

	public function __construct(KConfig $config)
	{
		parent::__construct($config);
	
		$this->_directory = $config->directory;
        $this->_manifest = $config->manifest;
	}

	protected function _initialize(KConfig $config)
	{
		$config->append(array(
			'directory' => SYSTEM_THEMES,
            'manifest'  => 'manifest.json',
		));
	
		parent::_initialize($config);
	}

	public function find($query = null, $mode = KDatabase::FETCH_ROWSET)
    {
        $result    = array();
        $directory = opendir($this->_directory);

        while(($node = readdir($directory)) !== false)
        {
            $path = $this->_directory.DS.$node;
            if ($node != "." && $node != ".." && is_dir($path)) 
            {
                $manifest = new KConfig(
                    get_object_vars(
                        json_decode(
                            file_get_contents($path.DS.$this->_manifest)
                        )
                    )
                );

                $result[] = array(
                    'path'        => $path,
                    'manifest'    => $manifest,
                    'title'       => $manifest->title,
                    'name'        => $node,
                    'description' => $manifest->description,
                );
            }
        }

        closedir($directory);

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
}